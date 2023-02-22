/**
 * Community Translation GlotPress module
 */
'use strict';

var debug = require( 'debug' )( 'automattic:community-translator' );
var batcher = require( './batcher.js' );

function GlotPress( locale, translations ) {
	var server = {
			url: '',
			project: '',
			translation_set_slug: 'default',
		},
		batchOptions = {};

	if ( translations ) {
		batchOptions.translations = translations;
	}

	function ajax( options ) {
		options = jQuery.extend( {
			type: 'POST',
			data: {},
			dataType: 'json',
			xhrFields: {
				withCredentials: true,
			},
			crossDomain: true,
		}, options );
		return jQuery.ajax( options );
	}

	function getServerUrl( path ) {
		return server.url + path;
	}

	function fetchOriginals( originals, callback ) {
		ajax( {
			url: getServerUrl( '/api/translations/-query-by-originals' ),
			data: {
				project: server.project,
				translation_set_slug: server.translation_set_slug,
				locale_slug: locale.getLocaleCode(),
				original_strings: JSON.stringify( originals ),
			},
		} ).done( function( response ) {
			callback( response );
		} );
	}

	return {
		getPermalink: function( translationPair ) {
			var originalId = translationPair.getOriginal().getId(),
				projectUrl,
				translateSetSlug = server.translation_set_slug,
				translationId,
				url;

			if ( translationPair.getGlotPressProject() ) {
				projectUrl = translationPair.getGlotPressProject();
			} else {
				projectUrl = server.url + '/projects/' + server.project;
			}

			url = projectUrl + '/' + locale.getLocaleCode() + '/' + translateSetSlug + '?filters[original_id]=' + originalId;

			if ( 'undefined' !== typeof translationId ) {
				url += '&filters[translation_id]=' + translationId;
			}

			return url;
		},

		loadSettings: function( gpInstance ) {
			if ( 'undefined' !== typeof gpInstance.url ) {
				server.url = gpInstance.url;
			} else {
				debug( 'Missing GP server url' );
			}

			if ( 'undefined' !== typeof gpInstance.url ) {
				server.project = gpInstance.project;
			} else {
				debug( 'Missing GP project path' );
			}

			if ( 'undefined' !== typeof gpInstance.translation_set_slug ) {
				server.translation_set_slug = gpInstance.translation_set_slug;
			}
		},

		queryByOriginal: batcher( fetchOriginals, batchOptions ),

		submitTranslation: function( translation, translationPair ) {
			return ajax( {
				url: getServerUrl( '/api/translations/-new' ),
				data: {
					project: translationPair.getGlotPressProject(),
					translation_set_slug: server.translation_set_slug,
					locale_slug: locale.getLocaleCode(),
					translation: translation,
				},
			} );
		},
	};
}

module.exports = GlotPress;
