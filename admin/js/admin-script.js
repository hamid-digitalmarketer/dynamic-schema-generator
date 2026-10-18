/**
 * Dynamic Schema Generator Admin Scripts
 *
 * @package Dynamic_Schema_Generator
 */

/* global dsgAdmin, jQuery */
jQuery( document ).ready( function( $ ) {
	'use strict';

	// Copy JSON-LD to clipboard
	$( '#dsg-copy-json' ).on( 'click', function() {
		var $btn = $( this );
		var json = $( '#dsg-json-viewer' ).val();

		if ( ! json ) {
			return;
		}

		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( json ).then( function() {
				var orig = $btn.text();
				$btn.text( dsgAdmin.copiedMsg || 'Copied!' ).addClass( 'button-primary' );
				setTimeout( function() {
					$btn.text( orig ).removeClass( 'button-primary' );
				}, 2000 );
			} );
		} else {
			// Fallback select & execCommand
			var textarea = document.getElementById( 'dsg-json-viewer' );
			textarea.select();
			document.execCommand( 'copy' );
			alert( dsgAdmin.copiedMsg || 'Copied to clipboard!' );
		}
	} );

	// Fetch Preview via AJAX
	$( '#dsg-fetch-preview' ).on( 'click', function() {
		var postId = $( '#dsg-preview-target' ).val();
		var $spinner = $( '#dsg-preview-spinner' );

		$spinner.addClass( 'is-active' );

		$.ajax( {
			url: dsgAdmin.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'dsg_get_preview',
				nonce: dsgAdmin.nonce,
				post_id: postId
			},
			success: function( res ) {
				$spinner.removeClass( 'is-active' );
				if ( res.success && res.data ) {
					$( '#dsg-json-viewer' ).val( res.data.json );

					var val = res.data.validation;
					var $banner = $( '#dsg-val-banner' );
					var $title = $( '#dsg-val-title' );
					var $typesList = $( '#dsg-val-types-list' );
					var $msgBox = $( '#dsg-val-messages' );

					if ( val.valid ) {
						$banner.removeClass( 'dsg-banner-error' ).addClass( 'dsg-banner-success' );
						$title.text( '✓ Schema Validated: 0 Syntax Errors Found' );
					} else {
						$banner.removeClass( 'dsg-banner-success' ).addClass( 'dsg-banner-error' );
						$title.text( '⚠ Schema Validation Issues Detected' );
					}

					$typesList.text( val.types && val.types.length ? val.types.join( ', ' ) : 'None' );

					var html = '';
					if ( val.errors && val.errors.length ) {
						html += '<ul style="color:#d63638;margin-top:5px;">';
						$.each( val.errors, function( i, err ) {
							html += '<li>&bull; ' + $( '<div>' ).text( err ).html() + '</li>';
						} );
						html += '</ul>';
					}

					if ( val.warnings && val.warnings.length ) {
						html += '<ul style="color:#b26200;margin-top:5px;">';
						$.each( val.warnings, function( i, warn ) {
							html += '<li>&bull; ' + $( '<div>' ).text( warn ).html() + '</li>';
						} );
						html += '</ul>';
					}

					$msgBox.html( html );
				}
			},
			error: function() {
				$spinner.removeClass( 'is-active' );
				alert( 'Error connecting to schema generator preview API.' );
			}
		} );
	} );

	// Clear Diagnostics Log
	$( '#dsg-clear-logs-btn' ).on( 'click', function() {
		if ( ! confirm( 'Clear all stored diagnostic logs?' ) ) {
			return;
		}

		var $btn = $( this );
		$btn.prop( 'disabled', true );

		$.ajax( {
			url: dsgAdmin.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'dsg_clear_logs',
				nonce: dsgAdmin.nonce
			},
			success: function( res ) {
				$btn.prop( 'disabled', false );
				if ( res.success ) {
					$( '#dsg-logs-table' ).remove();
					if ( ! $( '#dsg-no-logs' ).length ) {
						$( '<p id="dsg-no-logs" class="description">' + ( dsgAdmin.clearedMsg || 'Diagnostics log cleared.' ) + '</p>' ).insertAfter( '.dsg-card:last h2' );
					}
				}
			},
			error: function() {
				$btn.prop( 'disabled', false );
				alert( 'Failed to clear diagnostics log.' );
			}
		} );
	} );
} );
