<?php
/**
 * Template for the meta section of the editor row in a translation set display
 *
 * @package    GlotPress
 * @subpackage Templates
 */

$more_links = array();
if ( $translation->translation_status ) {
	$translation_permalink = gp_url_project_locale(
		$project,
		$locale->slug,
		$translation_set->slug,
		array(
			'filters[status]'         => 'either',
			'filters[original_id]'    => $translation->original_id,
			'filters[translation_id]' => $translation->id,
		)
	);

	$more_links['translation-permalink'] = '<a tabindex="-1" href="' . esc_url( $translation_permalink ) . '">' . __( 'Permalink to this translation', 'glotpress' ) . '</a>';
} else {
	$original_permalink = gp_url_project_locale( $project, $locale->slug, $translation_set->slug, array( 'filters[original_id]' => $translation->original_id ) );

	$more_links['original-permalink'] = '<a tabindex="-1" href="' . esc_url( $original_permalink ) . '">' . __( 'Permalink to this original', 'glotpress' ) . '</a>';
}

$original_history = gp_url_project_locale(
	$project,
	$locale->slug,
	$translation_set->slug,
	array(
		'filters[status]'      => 'either',
		'filters[original_id]' => $translation->original_id,
		'sort[by]'             => 'translation_date_added',
		'sort[how]'            => 'asc',
	)
);

$more_links['history'] = '<a tabindex="-1" href="' . esc_url( $original_history ) . '">' . __( 'All translations of this original', 'glotpress' ) . '</a>';

/**
 * Allows to modify the more links in the translation editor.
 *
 * @since 2.3.0
 *
 * @param array $more_links The links to be output.
 * @param GP_Project $project Project object.
 * @param GP_Locale $locale Locale object.
 * @param GP_Translation_Set $translation_set Translation Set object.
 * @param GP_Translation $translation Translation object.
 */
$more_links = apply_filters( 'gp_translation_row_template_more_links', $more_links, $project, $locale, $translation_set, $translation );

?>
<div class="meta">
	<h3><?php _e( 'Meta', 'glotpress' ); ?></h3>

	<?php gp_tmpl_load( 'translation-row-editor-meta-status', get_defined_vars() ); ?>

	<?php if ( $translation->context ) : ?>
		<dl>
			<dt><?php _e( 'Context:', 'glotpress' ); ?></dt>
			<dd><?php echo esc_translation( $translation->context ); ?></dd>
		</dl>
	<?php endif; ?>
	<?php if ( $translation->extracted_comments ) : ?>
		<dl>
			<dt><?php _e( 'Comment:', 'glotpress' ); ?></dt>
			<dd>
				<?php
				/**
				 * Filters the extracted comments of an original.
				 *
				 * @param string         $extracted_comments Extracted comments of an original.
				 * @param GP_Translation $translation        Translation object.
				 */
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters( 'gp_original_extracted_comments', $translation->extracted_comments, $translation );
				?>
			</dd>
		</dl>
	<?php endif; ?>
	<?php if ( $translation->translation_added && '0000-00-00 00:00:00' !== $translation->translation_added ) : ?>
		<dl>
			<dt><?php _e( 'Date added:', 'glotpress' ); ?></dt>
			<?php
			$date_added          = strtotime( get_date_from_gmt( $translation->translation_added ) );
			$date_added_formated = sprintf(
				/* translators: 1: Modified date. 2: Modified time. 3: Timezone. */
				esc_html__( '%1$s at %2$s (%3$s)', 'glotpress' ),
				date_i18n( get_option( 'date_format' ), $date_added ),
				date_i18n( get_option( 'time_format' ), $date_added ),
				wp_timezone_string()
			);
			?>
			<dd id="local-date-added-<?php echo esc_attr( $translation->row_id ); ?>"><?php echo esc_html( $date_added_formated ); ?></dd>
		</dl>
	<?php endif; ?>
	<?php if ( $translation->user ) : ?>
		<dl>
			<dt><?php _e( 'Translated by:', 'glotpress' ); ?></dt>
			<dd><?php gp_link_user( $translation->user ); ?></dd>
		</dl>
	<?php endif; ?>
	<?php if ( $translation->user_last_modified && ( ! $translation->user || $translation->user->ID !== $translation->user_last_modified->ID ) ) : ?>
		<dl>
			<dt>
			<?php
			if ( 'current' === $translation->translation_status ) {
				_e( 'Approved by:', 'glotpress' );
			} elseif ( 'rejected' === $translation->translation_status ) {
				_e( 'Rejected by:', 'glotpress' );
			} else {
				_e( 'Last updated by:', 'glotpress' );
			}
			?>
			</dt>
			<dd><?php gp_link_user( $translation->user_last_modified ); ?></dd>
		</dl>
	<?php endif; ?>
	<?php references( $project, $translation ); ?>

	<dl>
		<dt><?php _e( 'Priority:', 'glotpress' ); ?></dt>
		<?php if ( $can_write ) : ?>
			<dd>
				<?php
				echo gp_select(
					'priority-' . $translation->original_id,
					GP::$original->get_static( 'priorities' ),
					$translation->priority,
					array(
						'class'      => 'priority',
						'tabindex'   => '-1',
						'data-nonce' => wp_create_nonce( 'set-priority_' . $translation->original_id ),
					)
				);
				?>
			</dd>
		<?php else : ?>
			<dd>
				<?php
				echo esc_html(
					gp_array_get(
						GP::$original->get_static( 'priorities' ),
						$translation->priority,
						_x( 'Unknown', 'priority', 'glotpress' )
					)
				);
				?>
			</dd>
		<?php endif; ?>
	</dl>

	<dl>
		<dt><?php _e( 'More links:', 'glotpress' ); ?>
			<ul>
				<?php foreach ( $more_links as $more_link ) : ?>
					<li>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $more_link;
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</dt>
	</dl>
</div>
