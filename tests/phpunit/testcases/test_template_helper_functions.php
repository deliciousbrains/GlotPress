<?php

// As the test cases don't load the GP front end, we need to include the helper-functions here to make them available.
require_once GP_TMPL_PATH . 'helper-functions.php';

class GP_Test_Template_Helper_Functions extends GP_UnitTestCase {

	function test_map_glossary_entries_to_translation_originals_with_ampersand_in_glossary() {
		$test_string = 'This string, <code>&lt;/body&gt;</code>, should not have the code tags mangled.';
		$orig = '';
		$expected_result = 'This string, &lt;code&gt;&amp;lt;/body<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;&amp;amp;&quot;,&quot;pos&quot;:&quot;interjection&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">&amp;</span>gt;&lt;/code&gt;, should not have the code tags mangled.';

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entry = array(
			'term' => '&',
			'part_of_speech' => 'interjection',
			'translation' => '&amp;',
			'glossary_id' => $glossary->id,
		);

		GP::$glossary_entry->create_and_select( $glossary_entry );

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching a term with a space between words [color scheme].
	 */
	function test_map_glossary_entries_to_translation_originals_with_spaces_in_glossary() {
		$test_string = 'Please set your favorite color scheme.';
		$orig = '';
		$expected_result = 'Please set your favorite <span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;paleta de cores&quot;,&quot;pos&quot;:&quot;noun&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">color scheme</span>.';

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entry = array(
			'term' => 'color scheme',
			'part_of_speech' => 'noun',
			'translation' => 'paleta de cores',
			'glossary_id' => $glossary->id,
		);

		GP::$glossary_entry->create_and_select( $glossary_entry );

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching a term with an hyphen [color-scheme].
	 */
	function test_map_glossary_entries_to_translation_originals_with_hyphens_in_glossary() {
		$test_string = 'Please set your favorite color-scheme.';
		$orig = '';
		$expected_result = 'Please set your favorite <span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;paleta de cores&quot;,&quot;pos&quot;:&quot;noun&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">color-scheme</span>.';

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entry = array(
			'term' => 'color-scheme',
			'part_of_speech' => 'noun',
			'translation' => 'paleta de cores',
			'glossary_id' => $glossary->id,
		);

		GP::$glossary_entry->create_and_select( $glossary_entry );

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching a term with space and hyphen mixed [GlotPress WP-Team].
	 */
	function test_map_glossary_entries_to_translation_originals_with_spaces_and_hyphens_in_glossary() {
		$test_string = 'Prowdly built by your GlotPress WP-Team.';
		$orig = '';
		$expected_result = 'Prowdly built by your <span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;Equipa-WP do GlotPress&quot;,&quot;pos&quot;:&quot;noun&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">GlotPress WP-Team</span>.';

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entry = array(
			'term' => 'GlotPress WP-Team',
			'part_of_speech' => 'noun',
			'translation' => 'Equipa-WP do GlotPress',
			'glossary_id' => $glossary->id,
		);

		GP::$glossary_entry->create_and_select( $glossary_entry );

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the 3 words term [admin color scheme] instead of the 2 words term [color scheme] or single word term [admin].
	 */
	function test_map_glossary_entries_to_translation_originals_with_word_count_priority() {
		$test_string = 'Please set your admin color scheme.';
		$orig = '';
		$expected_result = 'Please set your <span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;paleta de cores do administrador&quot;,&quot;pos&quot;:&quot;noun&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">admin color scheme</span>.';

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'admin',
				'part_of_speech' => 'noun',
				'translation' => 'administrador',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'color scheme',
				'part_of_speech' => 'noun',
				'translation' => 'paleta de cores',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'admin color scheme',
				'part_of_speech' => 'noun',
				'translation' => 'paleta de cores do administrador',
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects highlighting leading and ending spaces in single line strings, and double/multiple spaces in the middle.
	 */
	function test_prepare_original_with_leading_and_trailing_spaces_and_multiple_spaces_in_middle_of_single_line_strings() {
		$test_string     = '  Two spaces at the begining, double  and triple   spaces in the middle, and one space in the end. ';
		$expected_result = '<span class="invisible-spaces">  </span>Two spaces at the begining, double<span class="invisible-spaces">  </span>and triple<span class="invisible-spaces">   </span>spaces in the middle, and one space in the end.<span class="invisible-spaces"> </span>';

		$orig = prepare_original( $test_string );

		$this->assertEquals( $orig, $expected_result );
	}

	/**
	 * Expects highlighting leading and ending spaces in multi line strings, and double/multiple spaces in the middle.
	 */
	function test_prepare_original_with_leading_and_trailing_spaces_and_multiple_spaces_in_middle_of_multi_line_strings() {
		$test_string     = "  Two spaces at the begining and end, and in the line below:  \n\n One space at the begining and end \n\nNo spaces\n One space at the begining\nOne space at the end \n\n\nMultiple spaces  in   multiline  \n One space at the begining and end ";
		$expected_result = "<span class=\"invisible-spaces\">  </span>Two spaces at the begining and end, and in the line below:<span class=\"invisible-spaces\">  </span><span class='invisibles' title='New line'>&crarr;</span>\n<span class='invisibles' title='New line'>&crarr;</span>\n<span class=\"invisible-spaces\"> </span>One space at the begining and end<span class=\"invisible-spaces\"> </span><span class='invisibles' title='New line'>&crarr;</span>\n<span class='invisibles' title='New line'>&crarr;</span>\nNo spaces<span class='invisibles' title='New line'>&crarr;</span>\n<span class=\"invisible-spaces\"> </span>One space at the begining<span class='invisibles' title='New line'>&crarr;</span>\nOne space at the end<span class=\"invisible-spaces\"> </span><span class='invisibles' title='New line'>&crarr;</span>\n<span class='invisibles' title='New line'>&crarr;</span>\n<span class='invisibles' title='New line'>&crarr;</span>\nMultiple spaces<span class=\"invisible-spaces\">  </span>in<span class=\"invisible-spaces\">   </span>multiline<span class=\"invisible-spaces\">  </span><span class='invisibles' title='New line'>&crarr;</span>\n<span class=\"invisible-spaces\"> </span>One space at the begining and end<span class=\"invisible-spaces\"> </span>";

		$orig = prepare_original( $test_string );

		$this->assertEquals( $orig, $expected_result );
	}

	/**
	 * Expects highlighting line breaks and tabs.
	 */
	function test_prepare_original_with_line_breaks_and_tabs() {
		$test_string     = "This string has 2x tabs\t\tand a line\nbreak.";
		$expected_result = "This string has 2x tabs<span class='invisibles' title='Tab character'>&rarr;</span>\t<span class='invisibles' title='Tab character'>&rarr;</span>\tand a line<span class='invisibles' title='New line'>&crarr;</span>\nbreak.";

		$orig = prepare_original( $test_string );

		$this->assertEquals( $orig, $expected_result );
	}

	function provide_test_map_glossary_entries_to_translation_originals() {
		foreach ( array(
			'party' => array(
				'Welcome to the party.',
				'My parties.',
				'I know, partys is the wrong plural ending but we need it because of nouns like boys.',
			),
			'color' => array(
				'One color.',
				'Two colors.',
			),
			'half' => array(
				'Half a loaf is better than none.',
				'Two halves are even better.',
			),
			'man' => array(
				'The word man is the root of the word mankind.',
				'There are men but there is no menkind.',
			),
			'issue' => array(
				'If you find a bug, file an issue.',
				'If you find two bugs, please file two issues.',
			),
			'report' => array(
				'I reported a bug.',
				'Now there is a bug report.',
				'We call it bug reporting.',
			),
		) as $expected_result => $test_strings ) {
			foreach ( $test_strings as $test_string ) {
				yield array( $test_string, $expected_result );
			}
		}
	}

	/**
	 * @dataProvider provide_test_map_glossary_entries_to_translation_originals
	 */
	function test_map_glossary_entries_to_translation_originals_with_suffixes( $test_string, $expected_result ) {
		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'party',
				'part_of_speech' => 'noun',
				'translation' => 'party',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'color',
				'part_of_speech' => 'noun',
				'translation' => 'color',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'half',
				'part_of_speech' => 'noun',
				'translation' => 'half',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'man',
				'part_of_speech' => 'noun',
				'translation' => 'man',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'issue',
				'part_of_speech' => 'noun',
				'translation' => 'issue',
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'report',
				'part_of_speech' => 'noun',
				'translation' => 'report',
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertMatchesRegularExpression( '#<span class="glossary-word" data-translations="\[{&quot;translation&quot;:&quot;' . $expected_result . '&quot;,[^"]+">[^<]+</span>#', $orig->singular_glossary_markup );
	}

	/**
	 * Expects matching the plurals of Nouns ending in a sibilant. Suffix: '-es'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_ending_with_sibilant_in_glossary() {
		$test_string = 'Testing words kiss, kisses, waltz, waltzes, box, boxes, dish, dishes, coach, coaches.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'beijo' => array( // Portuguese.
				'kiss',         // Singular.
				'kisses',       // Plural.
			),
			'valsa' => array( // Portuguese.
				'waltz',        // Singular.
				'waltzes',      // Plural.
			),
			'caixa' => array( // Portuguese.
				'box',          // Singular.
				'boxes',        // Plural.
			),
			'prato' => array( // Portuguese.
				'dish',         // Singular.
				'dishes',       // Plural.
			),
			'treinador' => array( // Portuguese.
				'coach',            // Singular.
				'coaches',          // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'kiss', // Kiss and kisses.
				'part_of_speech' => $part_of_speech,
				'translation' => 'beijo', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'waltz', // Waltz and waltzes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'valsa', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'box', // Box and boxes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'caixa', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'dish', // Dish and dishes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'prato', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'coach', // Coach and coaches.
				'part_of_speech' => $part_of_speech,
				'translation' => 'treinador', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the plurals of Nouns ending with '-y' preceded by vowel. Suffix: '-s'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_ending_with_y_preceded_by_vowel_in_glossary() {
		$test_string = 'Testing words delay, delays, key, keys, toy, toys, guy, guys.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'atraso' => array( // Portuguese.
				'delay',         // Singular.
				'delays',        // Plural.
			),
			'chave' => array( // Portuguese.
				'key',          // Singular.
				'keys',         // Plural.
			),
			'brinquedo' => array( // Portuguese.
				'toy',              // Singular.
				'toys',             // Plural.
			),
			'rapaz' => array( // Portuguese.
				'guy',          // Singular.
				'guys',         // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'delay', // Delay and delays.
				'part_of_speech' => $part_of_speech,
				'translation' => 'atraso', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'key', // Key and keys.
				'part_of_speech' => $part_of_speech,
				'translation' => 'chave', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'toy', // Toy and toys.
				'part_of_speech' => $part_of_speech,
				'translation' => 'brinquedo', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'guy', // Guy and guys.
				'part_of_speech' => $part_of_speech,
				'translation' => 'rapaz', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the plurals of Nouns ending with '-y' preceded by consonant. Suffix: '-ies'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_ending_with_y_preceded_by_consonant_in_glossary() {
		$test_string = 'Testing words lady, ladies.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'senhora' => array( // Portuguese.
				'lady',           // Singular.
				'ladies',         // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'lady', // Lady and ladies.
				'part_of_speech' => $part_of_speech,
				'translation' => 'senhora', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the plurals of Nouns alternate ending with '-o' preceded by consonant. Suffix: '-es'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_alternate_ending_with_o_preceded_by_consonant_in_glossary() {
		$test_string = 'Testing words hero, heroes, tomato, tomatoes.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'her\u00f3i' => array( // Portuguese.
				'hero',              // Singular.
				'heroes',            // Plural.
			),
			'tomate' => array( // Portuguese.
				'tomato',        // Singular.
				'tomatoes',      // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'hero', // Hero and heroes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'herói', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'tomato', // Tomato and tomatoes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'tomate', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the plurals of Nouns ending with '-an'. Suffix: '-en'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_ending_with_an_in_glossary() {
		$test_string = 'Testing words woman, women.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'mulher' => array( // Portuguese.
				'woman',         // Singular.
				'women',         // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'woman', // Woman and women.
				'part_of_speech' => $part_of_speech,
				'translation' => 'mulher', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the plurals of Nouns ending with '-f' or '-fe'. Suffix: '-ves'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_ending_with_f_in_glossary() {
		$test_string = 'Testing words wife, wives, leaf, leaves, wolf, wolves.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'esposa' => array( // Portuguese.
				'wife',          // Singular.
				'wives',         // Plural.
			),
			'folha' => array( // Portuguese.
				'leaf',         // Singular.
				'leaves',       // Plural.
			),
			'lobo' => array( // Portuguese.
				'wolf',        // Singular.
				'wolves',      // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'wife', // Wife and wives.
				'part_of_speech' => $part_of_speech,
				'translation' => 'esposa', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'leaf', // Leaf and leaves.
				'part_of_speech' => $part_of_speech,
				'translation' => 'folha', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'wolf', // Wolf and wolves.
				'part_of_speech' => $part_of_speech,
				'translation' => 'lobo', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the fallback plurals of Nouns ending with '-s'. Suffix '-es'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_fallback_ending_with_s_in_glossary() {
		$test_string = 'Testing words bus, buses, lens, lenses.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'autocarro' => array( // Portuguese.
				'bus',              // Singular.
				'buses',            // Plural.
			),
			'lente' => array( // Portuguese.
				'lens',         // Singular.
				'lenses',       // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'bus', // Bus and buses.
				'part_of_speech' => $part_of_speech,
				'translation' => 'autocarro', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'lens', // Lens and lenses.
				'part_of_speech' => $part_of_speech,
				'translation' => 'lente', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the fallback plurals of Nouns not ending with '-s'. Suffix '-s'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_nouns_fallback_not_ending_with_s_in_glossary() {
		$test_string = 'Testing words chief, chiefs.';
		$orig = '';
		$part_of_speech = 'noun';

		$matches = array(
			'chefe' => array( // Portuguese.
				'chief',        // Singular.
				'chiefs',       // Plural.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'chief', // Chief and chiefs.
				'part_of_speech' => $part_of_speech,
				'translation' => 'chefe', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the third-person of Verbs ending in a sibilant. Suffix: '-es'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_3rdperson_ending_with_sibilant_in_glossary() {
		$test_string = 'Testing words pass, passes, quiz, quizes, fix, fixes, push, pushes, watch, watches.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'passar' => array( // Portuguese.
				'pass',          // Infinitive.
				'passes',        // Third-person.
			),
			'questionar' => array( // Portuguese.
				'quiz',              // Infinitive.
				'quizes',            // Third-person.
			),
			'corrigir' => array( // Portuguese.
				'fix',             // Infinitive.
				'fixes',           // Third-person.
			),
			'empurrar' => array( // Portuguese.
				'push',            // Infinitive.
				'pushes',          // Third-person.
			),
			'ver' => array( // Portuguese.
				'watch',      // Infinitive.
				'watches',    // Third-person.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'pass', // Pass and passes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'passar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'quiz', // Quiz and quizes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'questionar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'fix', // Fix and fixes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'corrigir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'push', // Push and pushes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'empurrar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'watch', // Watch and watches.
				'part_of_speech' => $part_of_speech,
				'translation' => 'ver', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the third-person of Verbs ending with '-y' preceded by vowel. Suffix: '-s'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_3rdperson_ending_with_y_preceded_by_vowel_in_glossary() {
		$test_string = 'Testing words play, plays.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'reproduzir' => array( // Portuguese.
				'play',              // Infinitive.
				'plays',             // Third-person.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'play', // Play and plays.
				'part_of_speech' => $part_of_speech,
				'translation' => 'reproduzir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the third-person of Verbs ending with '-y' preceded by consonant. Suffix: '-ies'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_3rdperson_ending_with_y_preceded_by_consonant_in_glossary() {
		$test_string = 'Testing words try, tries.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'tentar' => array( // Portuguese.
				'try',           // Infinitive.
				'tries',         // Third-person.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'try', // Try and tries.
				'part_of_speech' => $part_of_speech,
				'translation' => 'tentar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the third-person of Verbs ending with '-o' preceded by consonant. Suffix: '-es'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_3rdperson_ending_with_o_preceded_by_consonant_in_glossary() {
		$test_string = 'Testing words go, goes.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'ir' => array( // Portuguese.
				'go',        // Infinitive.
				'goes',      // Third-person.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'go', // Try and tries.
				'part_of_speech' => $part_of_speech,
				'translation' => 'ir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the third-person fallback suffix for most Verbs. Suffix: '-s'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_most_verbs_3rdperson_fallback_in_glossary() {
		$test_string = 'Testing words format, formats, make, makes, pull, pulls.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'formatar' => array( // Portuguese.
				'format',          // Infinitive.
				'formats',         // Third-person.
			),
			'fazer' => array( // Portuguese.
				'make',         // Infinitive.
				'makes',        // Third-person.
			),
			'puxar' => array( // Portuguese.
				'pull',         // Infinitive.
				'pulls',        // Third-person.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'make', // Make and makes.
				'part_of_speech' => $part_of_speech,
				'translation' => 'fazer', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'format', // Format and formats.
				'part_of_speech' => $part_of_speech,
				'translation' => 'formatar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'pull', // Pull and pulls.
				'part_of_speech' => $part_of_speech,
				'translation' => 'puxar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the past of Verbs ending with '-e'. Suffix '-ed'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_past_ending_with_e_in_glossary() {
		$test_string = 'Testing words contribute, contributed, delete, deleted.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'contribuir' => array( // Portuguese.
				'contribute',        // Infinitive.
				'contributed',       // Past.
			),
			'eliminar' => array( // Portuguese.
				'delete',          // Infinitive.
				'deleted',         // Past.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'contribute', // Contribute and contributed.
				'part_of_speech' => $part_of_speech,
				'translation' => 'contribuir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'delete', // Delete and deleted.
				'part_of_speech' => $part_of_speech,
				'translation' => 'eliminar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the past of Verbs not ending with '-e'. Suffix '-ed'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_past_not_ending_with_e_in_glossary() {
		$test_string = 'Testing words fix, fixed, push, pushed.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'corrigir' => array( // Portuguese.
				'fix',             // Infinitive.
				'fixed',           // Past.
			),
			'empurrar' => array( // Portuguese.
				'push',            // Infinitive.
				'pushed',          // Past.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'fix', // Fix and fixed.
				'part_of_speech' => $part_of_speech,
				'translation' => 'corrigir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'push', // Push and pushed.
				'part_of_speech' => $part_of_speech,
				'translation' => 'empurrar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the present of Verbs ending with '-e'. Suffix '-ing'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_present_ending_with_e_in_glossary() {
		$test_string = 'Testing words contribute, contributing, delete, deleting, care, caring.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'contribuir' => array( // Portuguese.
				'contribute',        // Infinitive.
				'contributing',      // Present.
			),
			'eliminar' => array( // Portuguese.
				'delete',          // Infinitive.
				'deleting',        // Present.
			),
			'cuidar' => array( // Portuguese.
				'care',          // Infinitive.
				'caring',        // Present.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'contribute', // Contribute and contributing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'contribuir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'delete', // Delete and deleting.
				'part_of_speech' => $part_of_speech,
				'translation' => 'eliminar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'care', // Care and caring.
				'part_of_speech' => $part_of_speech,
				'translation' => 'cuidar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the present of Verbs not ending with 'e', or ending with 'ee', 'ye' or 'oe'. Suffix '-ing'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_present_not_ending_with_e_or_ending_with_ee_ye_oe_in_glossary() {
		$test_string = 'Testing words fix, fixing, push, pushing, agree, agreeing, see, seeing, dye, dyeing, tiptoe, tiptoeing.';
		$orig = '';
		$part_of_speech = 'verb';

		$matches = array(
			'corrigir' => array( // Portuguese.
				'fix',             // Infinitive.
				'fixing',          // Present.
			),
			'empurrar' => array( // Portuguese.
				'push',            // Infinitive.
				'pushing',         // Present.
			),
			'concordar' => array( // Portuguese.
				'agree',            // Infinitive.
				'agreeing',         // Present.
			),
			'ver' => array( // Portuguese.
				'see',        // Infinitive.
				'seeing',     // Present.
			),
			'tingir' => array( // Portuguese.
				'dye',           // Infinitive.
				'dyeing',        // Present.
			),
			'andar em pontas dos p\u00e9s' => array( // Portuguese.
				'tiptoe',                              // Infinitive.
				'tiptoeing',                           // Present.
			),
		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'fix', // Contribute and contributing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'corrigir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'push', // Push and pushing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'empurrar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'agree', // Agree and agreeing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'concordar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'see', // See and seeing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'ver', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'dye', // Dye and dyeing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'tingir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'tiptoe', // Tiptoe and tiptoeing.
				'part_of_speech' => $part_of_speech,
				'translation' => 'andar em pontas dos pés', // Portuguese.
				'glossary_id' => $glossary->id,
			),
		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the Verbs that form Nouns ending with suffix '-tion'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_form_nouns_ending_with_tion_in_glossary() {
		$test_string = 'Testing words abbreviate, abbreviation, authorize, authorization, specify, specification, liquefy, liquefaction, exclaim, exclamation, encrypt, encryption, subscribe, subscription, perceive, perception, resume, resumption, correct, correction, delete, deletion, edit, edition, ignite, ignition, contribute, contribution, resolve, resolution, compose, composition, abstain, abstention, contravene, contravention, prevent, prevention, insert, insertion.';

		$part_of_speech = 'verb';

		$matches = array(
			'abreviar' => array( // Portuguese.
				'abbreviate',      // Verb.
				'abbreviation',    // Noun.
			),
			'autorizar' => array( // Portuguese.
				'authorize',        // Verb.
				'authorization',    // Noun.
			),
			'especificar' => array( // Portuguese.
				'specify',            // Verb.
				'specification',      // Noun.
			),
			'liquefazer' => array( // Portuguese.
				'liquefy',           // Verb.
				'liquefaction',      // Noun.
			),
			'exclamar' => array( // Portuguese.
				'exclaim',         // Verb.
				'exclamation',     // Noun.
			),
			'encriptar' => array( // Portuguese.
				'encrypt',          // Verb.
				'encryption',       // Noun.
			),
			'subscrever' => array( // Portuguese.
				'subscribe',         // Verb.
				'subscription',      // Noun.
			),
			'percepcionar' => array( // Portuguese.
				'perceive',            // Verb.
				'perception',          // Noun.
			),
			'resumir' => array( // Portuguese.
				'resume',         // Verb.
				'resumption',     // Noun.
			),
			'corrigir' => array( // Portuguese.
				'correct',         // Verb.
				'correction',      // Noun.
			),
			'eliminar' => array( // Portuguese.
				'delete',          // Verb.
				'deletion',        // Noun.
			),
			'editar' => array( // Portuguese.
				'edit',          // Verb.
				'edition',       // Noun.
			),
			'acender' => array( // Portuguese.
				'ignite',         // Verb.
				'ignition',       // Noun.
			),
			'contribuir' => array( // Portuguese.
				'contribute',        // Verb.
				'contribution',      // Noun.
			),
			'resolver' => array( // Portuguese.
				'resolve',         // Verb.
				'resolution',      // Noun.
			),
			'comp\u00f4r' => array( // Portuguese.
				'compose',            // Verb.
				'composition',        // Noun.
			),
			'abster' => array( // Portuguese.
				'abstain',       // Verb.
				'abstention',    // Noun.
			),
			'transgredir' => array( // Portuguese.
				'contravene',         // Verb.
				'contravention',      // Noun.
			),
			'prevenir' => array( // Portuguese.
				'prevent',         // Verb.
				'prevention',      // Noun.
			),
			'inserir' => array( // Portuguese.
				'insert',         // Verb.
				'insertion',      // Noun.
			),

		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'abbreviate', // Abbreviate and abbreviation.
				'part_of_speech' => $part_of_speech,
				'translation' => 'abreviar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'authorize', // Authorize and authorization.
				'part_of_speech' => $part_of_speech,
				'translation' => 'autorizar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'specify', // Specify and specification.
				'part_of_speech' => $part_of_speech,
				'translation' => 'especificar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'liquefy', // Liquefy and liquefaction.
				'part_of_speech' => $part_of_speech,
				'translation' => 'liquefazer', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'exclaim', // Exclaim and exclamation.
				'part_of_speech' => $part_of_speech,
				'translation' => 'exclamar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'encrypt', // Encrypt and encryption.
				'part_of_speech' => $part_of_speech,
				'translation' => 'encriptar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'subscribe', // Subscribe and subscription.
				'part_of_speech' => $part_of_speech,
				'translation' => 'subscrever', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'perceive', // Perceive and perception.
				'part_of_speech' => $part_of_speech,
				'translation' => 'percepcionar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'resume', // Resume and resumption.
				'part_of_speech' => $part_of_speech,
				'translation' => 'resumir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'correct', // Correct and correction.
				'part_of_speech' => $part_of_speech,
				'translation' => 'corrigir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'delete', // Delete and deletion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'eliminar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'edit', // Edit and edition.
				'part_of_speech' => $part_of_speech,
				'translation' => 'editar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'ignite', // Ignite and ignition.
				'part_of_speech' => $part_of_speech,
				'translation' => 'acender', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'contribute', // Contribute and contribution.
				'part_of_speech' => $part_of_speech,
				'translation' => 'contribuir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'resolve', // Resolve and resolution.
				'part_of_speech' => $part_of_speech,
				'translation' => 'resolver', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'compose', // Compose and composition.
				'part_of_speech' => $part_of_speech,
				'translation' => 'compôr', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'abstain', // Abstain and abstention.
				'part_of_speech' => $part_of_speech,
				'translation' => 'abster', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'contravene', // Contravene and contravention.
				'part_of_speech' => $part_of_speech,
				'translation' => 'transgredir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'prevent', // Prevent and prevention.
				'part_of_speech' => $part_of_speech,
				'translation' => 'prevenir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'insert', // Insert and insertion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'inserir', // Portuguese.
				'glossary_id' => $glossary->id,
			),

		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

	/**
	 * Expects matching the Verbs that form Nouns ending with suffix '-sion'.
	 */
	function test_map_glossary_entries_to_translation_originals_with_verbs_form_nouns_ending_with_sion_in_glossary() {
		$test_string = 'Testing words invade, invasion, precede, precession, decide, decision, explode, explosion, exclude, exclusion, supervise, supervision, confuse, confusion, expel, expulsion, submit, submission, compress, compression, extend, extension, convert, conversion, disperse, dispersion, recur, recursion, emerge, emersion.';

		$part_of_speech = 'verb';

		$matches = array(
			'invadir' => array( // Portuguese.
				'invade',         // Verb.
				'invasion',       // Noun.
			),
			'preceder' => array( // Portuguese.
				'precede',         // Verb.
				'precession',      // Noun.
			),
			'decidir' => array( // Portuguese.
				'decide',         // Verb.
				'decision',       // Noun.
			),
			'explodir' => array( // Portuguese.
				'explode',         // Verb.
				'explosion',       // Noun.
			),
			'excluir' => array( // Portuguese.
				'exclude',        // Verb.
				'exclusion',      // Noun.
			),
			'supervisionar' => array( // Portuguese.
				'supervise',            // Verb.
				'supervision',          // Noun.
			),
			'confundir' => array( // Portuguese.
				'confuse',          // Verb.
				'confusion',        // Noun.
			),
			'expulsar' => array( // Portuguese.
				'expel',           // Verb.
				'expulsion',       // Noun.
			),
			'submeter' => array( // Portuguese.
				'submit',          // Verb.
				'submission',      // Noun.
			),
			'comprimir' => array( // Portuguese.
				'compress',         // Verb.
				'compression',      // Noun.
			),
			'estender' => array( // Portuguese.
				'extend',          // Verb.
				'extension',       // Noun.
			),
			'converter' => array( // Portuguese.
				'convert',          // Verb.
				'conversion',       // Noun.
			),
			'dispersar' => array( // Portuguese.
				'disperse',         // Verb.
				'dispersion',       // Noun.
			),
			'repetir' => array( // Portuguese.
				'recur',          // Verb.
				'recursion',      // Noun.
			),
			'emergir' => array( // Portuguese.
				'emerge',         // Verb.
				'emersion',       // Noun.
			),

		);

		$expected_result = array();
		foreach ( $matches as $glossary_entry => $originals ) {
			foreach ( $originals as $original ) {
				$expected_result[] = '<span class="glossary-word" data-translations="[{&quot;translation&quot;:&quot;' . $glossary_entry . '&quot;,&quot;pos&quot;:&quot;' . $part_of_speech . '&quot;,&quot;comment&quot;:null,&quot;locale_entry&quot;:&quot;&quot;}]">' . $original . '</span>';
			}
		}
		$expected_result = sprintf(
			'Testing words %s.',
			implode( ', ', $expected_result )
		);

		$entry = new Translation_Entry( array( 'singular' => $test_string, ) );

		$set = $this->factory->translation_set->create_with_project_and_locale();
		$glossary = GP::$glossary->create_and_select( array( 'translation_set_id' => $set->id ) );

		$glossary_entries = array(
			array(
				'term' => 'invade', // Invade and invasion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'invadir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'precede', // Precede and precession.
				'part_of_speech' => $part_of_speech,
				'translation' => 'preceder', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'decide', // Decide and decision.
				'part_of_speech' => $part_of_speech,
				'translation' => 'decidir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'explode', // Explode and explosion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'explodir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'exclude', // Exclude and exclusion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'excluir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'supervise', // Supervise and supervision.
				'part_of_speech' => $part_of_speech,
				'translation' => 'supervisionar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'confuse', // Confuse and confusion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'confundir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'expel', // Expel and expulsion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'expulsar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'submit', // Submit and submission.
				'part_of_speech' => $part_of_speech,
				'translation' => 'submeter', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'compress', // Compress and compression.
				'part_of_speech' => $part_of_speech,
				'translation' => 'comprimir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'extend', // Extend and extension.
				'part_of_speech' => $part_of_speech,
				'translation' => 'estender', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'convert', // Convert and conversion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'converter', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'disperse', // Disperse and dispersion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'dispersar', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'recur', // Recur and recursion.
				'part_of_speech' => $part_of_speech,
				'translation' => 'repetir', // Portuguese.
				'glossary_id' => $glossary->id,
			),
			array(
				'term' => 'emerge', // Emerge and emersion
				'part_of_speech' => $part_of_speech,
				'translation' => 'emergir', // Portuguese.
				'glossary_id' => $glossary->id,
			),

		);

		foreach ( $glossary_entries as $glossary_entry ) {
			GP::$glossary_entry->create_and_select( $glossary_entry );
		}

		$orig = map_glossary_entries_to_translation_originals( $entry, $glossary );

		$this->assertEquals( $orig->singular_glossary_markup, $expected_result );
	}

}
