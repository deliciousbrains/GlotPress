
/* global window, $gp */

/*
	These are the general hotkeys.
	The editor hotkeys are present in the assets/js/editor.js file
 */
jQuery( function( $ ) {
	$( 'html' ).keydown( function( e ) {
		var previousPage, nextPage, firstEditorRow;
		if ( 37 === e.keyCode && e.altKey ) { // Alt-Left Arrow or Option-Left Arrow = Move to the previous page.
			previousPage = $( '.gp-table-actions.top' ).find( '.previous' );
			if ( ( previousPage.length > 0 ) && ( undefined !== previousPage.attr( 'href' ) ) ) {
				window.location.href = previousPage.attr( 'href' );
			}
		}
		if ( 39 === e.keyCode && e.altKey ) { // Alt-Right Arrow or Option-Right Arrow = Move to the next page.
			nextPage = $( '.gp-table-actions.top' ).find( '.next' );
			if ( ( nextPage.length > 0 ) && ( undefined !== nextPage.attr( 'href' ) ) ) {
				window.location.href = nextPage.attr( 'href' );
			}
		}
		if ( 49 === e.keyCode && e.altKey ) { // Alt-1 or Option-1 = Show the editor for the first translation in the table.
			e.preventDefault();
			firstEditorRow = $( 'table > tbody  > tr:nth-child(2)' );
			if ( firstEditorRow.length > 0 ) {
				$gp.editor.show( firstEditorRow );
			}
		}
	} );
} );
