// jQuery File Tree Plugin
//
// Version 1.03
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
// Visit http://abeautifulsite.net/notebook.php?article=58 for more information
//
// Usage: $('.fileTreeDemo').fileTree( options, callback )
//
// Options:  root           - root folder to display; default = /
//           script         - location of the serverside AJAX file to use; default = jqueryFileTree.php
//           folderevent    - event to trigger expand/collapse; default = click
//           expandspeed    - default = 500 (ms); use -1 for no animation
//           collapsespeed  - default = 500 (ms); use -1 for no animation
//           expandeasing   - easing function to use on expand (optional)
//           collapseeasing - easing function to use on collapse (optional)
//           multifolder    - whether or not to limit the browser to one subfolder at a time
//           loadmessage    - Message to display while initial tree loads (can be HTML)
//
// History:
// 1.?? - Modified by potsky : unable to explain all changes ! (2013/04/01)
// 1.03 - Modified by potsky : LI are now triggerable (2012/12/30)
// 1.02 - Modified by potsky : work with Wordpress plugin s2member-files-browser (2012/12/24)
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// TERMS OF USE
//
// This plugin is dual-licensed under the GNU General Public License and the MIT License and
// is copyright 2008 A Beautiful Site, LLC.
//
;
(function ( $ ) {
	$.extend( $.fn , {

		fileTreeReload: function () {
			$( this ).empty();
			$( this ).fileTree();
		} ,

		fileTree: function ( o , h ) {
			if ( $( this ).data( 'o' ) ) {
				o = $( this ).data( 'o' );
				h = $( this ).data( 'h' );
			}
			else {
				// Defaults
				if ( ! o ) o = {};
				if ( o.action === undefined ) o.action = PSK_S2MSFB.action_get_dir;
				if ( o.script === undefined ) o.script = PSK_S2MSFB.ajaxurl;
				if ( o.folderevent === undefined ) o.folderevent = 'click';
				if ( o.expandspeed === undefined ) o.expandspeed = 500;
				if ( o.collapsespeed === undefined ) o.collapsespeed = 500;
				if ( o.expandeasing === undefined ) o.expandeasing = null;
				if ( o.collapseeasing === undefined ) o.collapseeasing = null;
				if ( o.multifolder === undefined ) o.multifolder = true;
				if ( o.openrecursive === undefined ) o.openrecursive = '0';
				if ( o.loadmessage === undefined ) o.loadmessage = '';
				if ( o.hidden === undefined ) o.hidden = '0';
				if ( o.dirfirst === undefined ) o.dirfirst = '1';
				if ( o.names === undefined ) o.names = '';
				if ( o.dirbase === undefined ) o.dirbase = '';
				if ( o.filterfile === undefined ) o.filterfile = '';
				if ( o.filterdir === undefined ) o.filterdir = '';
				if ( o.displayall === undefined ) o.displayall = '';
				if ( o.cutdirnames === undefined ) o.cutdirnames = '0';
				if ( o.cutfilenames === undefined ) o.cutfilenames = '0';
				if ( o.displaysize === undefined ) o.displaysize = '1';
				if ( o.displaycomment === undefined ) o.displaycomment = '0';
				if ( o.displayname === undefined ) o.displayname = '0';
				if ( o.displaymodificationdate === undefined ) o.displaymodificationdate = '0';
				if ( o.displaybirthdate === undefined ) o.displaybirthdate = '0';
				if ( o.sortby === undefined ) o.sortby = '0';
				if ( o.displaydownloaded === undefined ) o.displaydownloaded = '0';
				if ( o.search === undefined ) o.search = '0';
				if ( o.searchgroup === undefined ) o.searchgroup = '0';
				if ( o.searchdisplay === undefined ) o.searchdisplay = '0';
				if ( o.dirzip === undefined ) o.dirzip = '0';
				if ( o.previewext === undefined ) o.previewext = '';
				if ( o.swfurl === undefined ) o.swfurl = '';

				o.root = '/';
				o.collapsespeed = parseInt( o.collapsespeed , 10 );
				o.expandspeed = parseInt( o.expandspeed , 10 );
				o.multifolder = (o.multifolder != "0");
				o.openrecursive = (o.openrecursive == "1") ? "1" : "0";
				$( this ).data( 'o' , o );
				$( this ).data( 'h' , h );
			}

			$( this ).each( function () {

				function showTree( c , t ) {
					$( c ).addClass( 'wait' );
					$.post( o.script , {
						action                 : o.action ,
						dir                    : t ,
						hidden                 : o.hidden ,
						dirfirst               : o.dirfirst ,
						names                  : o.names ,
						filterfile             : o.filterfile ,
						filterdir              : o.filterdir ,
						displayall             : o.displayall ,
						dirbase                : o.dirbase ,
						openrecursive          : o.openrecursive ,
						cutdirnames            : o.cutdirnames ,
						cutfilenames           : o.cutfilenames ,
						displaysize            : o.displaysize ,
						displaydownloaded      : o.displaydownloaded ,
						search                 : o.search ,
						searchgroup            : o.searchgroup ,
						searchdisplay          : o.searchdisplay ,
						dirzip                 : o.dirzip ,
						previewext             : o.previewext ,
						displaymodificationdate: o.displaymodificationdate ,
						displaybirthdate       : o.displaybirthdate ,
						displaycomment         : o.displaycomment ,
						displayname            : o.displayname ,
						sortby                 : o.sortby ,
						nonce                  : PSK_S2MSFB.nonce
					} , function ( data ) {
						$( c ).removeClass( 'wait' ).append( data );
						$( c ).find( 'UL:hidden' ).slideDown( { duration: o.expandspeed , easing: o.expandeasing } );
						$( c ).find( '.start' ).hide();
						bindTree( c );
					} );
				}

				function searchTree( c , s ) {
					var t;
					if ( $( c ).hasClass( 'psk_jfiletree' ) ) { // top search
						t = '/';
						$( c ).find( '.start' ).show();
						$( c ).find( 'UL.jqueryFileTree' ).not( '.start' ).remove();
					}
					else {
						t = $( c ).find( 'DIV.jftctn A.link' ).attr( 'rel' );
						$( c ).addClass( 'wait' ); // LI
						$( c ).find( 'UL.jqueryFileTree' ).remove(); // UL
					}
					$.post( o.script , {
						action                 : o.action ,
						dir                    : t ,
						hidden                 : o.hidden ,
						dirfirst               : o.dirfirst ,
						names                  : o.names ,
						filterfile             : o.filterfile ,
						filterdir              : o.filterdir ,
						displayall             : o.displayall ,
						dirbase                : o.dirbase ,
						openrecursive          : o.openrecursive ,
						cutdirnames            : o.cutdirnames ,
						cutfilenames           : o.cutfilenames ,
						displaysize            : o.displaysize ,
						displaydownloaded      : o.displaydownloaded ,
						search                 : o.search ,
						searchgroup            : o.searchgroup ,
						searchdisplay          : o.searchdisplay ,
						dirzip                 : o.dirzip ,
						previewext             : o.previewext ,
						displaymodificationdate: o.displaymodificationdate ,
						displaybirthdate       : o.displaybirthdate ,
						displaycomment         : o.displaycomment ,
						displayname            : o.displayname ,
						sortby                 : o.sortby ,
						nonce                  : PSK_S2MSFB.nonce ,
						token                  : s
					} , function ( data ) {
						$( c ).removeClass( 'wait' ).append( data );
						$( c ).find( 'UL:hidden' ).slideDown( { duration: o.expandspeed , easing: o.expandeasing } );
						$( c ).find( '.start' ).hide();
						bindTree( c );
					} );
				}

				function bindTree( t ) {
					var searchgroup = $( t ).find( "li.PSK_S2MSFB_searchli" ).attr( "data-group" );
					searchgroup = (isNaN( searchgroup )) ? 0 : parseInt( searchgroup , 10 );

					// Search feature
					$( t ).find( '.PSK_S2MSFB_searchinp' )
						.blur( function () {
							if ( $( this ).val() == '' ) {
								$( this ).val( $( this ).attr( 'title' ) );
								if ( $( this ).find( 'UL.jqueryFileTree' ).attr( 'data-token' ) == '' ) {
									$( this ).prev().prev().hide();//resetbtn
								}
							}
						} )
						.click( function () {
							if ( $( this ).val() == $( this ).attr( 'title' ) ) {
								$( this ).val( '' );
							}

							if ( ( $( this ).val() == '' ) || ( $( this ).val() == $( this ).attr( 'title' ) ) ) {
								if ( $( this ).find( 'UL.jqueryFileTree' ).attr( 'data-token' ) == '' ) {
									$( this ).prev().prev().hide();//resetbtn
								}
								else {
									$( this ).prev().prev().show();//resetbtn
								}
							}
						} )
						.keypress( function ( e ) {
							if ( ( $( this ).val() == '' ) || ( $( this ).val() == $( this ).attr( 'title' ) ) ) {
								if ( $( this ).find( 'UL.jqueryFileTree' ).attr( 'data-token' ) == '' ) {
									$( this ).prev().prev().hide();//resetbtn
								}
								else {
									$( this ).prev().prev().show();//resetbtn
								}
							}
							if ( e.which == 13 ) {
								if ( ( $( this ).val() == '' ) || ( $( this ).val() == $( this ).attr( 'title' ) ) ) {
									alert( PSK_S2MSFB.errorsearch );
								}
								else {
									if ( searchgroup >= 1 ) {
										var thisval = $( this ).val();
										$( "li.PSK_S2MSFB_searchli[data-group=" + searchgroup + "] .PSK_S2MSFB_searchinp" ).each( function () {
											searchTree( $( this ).parent().parent().parent().parent() , thisval );
										} );
									}
									else {
										searchTree( $( this ).parent().parent().parent().parent() , $( this ).val() );
									}
								}
							}
						} )
					;
					$( t ).find( '.PSK_S2MSFB_searchbtn' )
						.click( function () {
							var e = jQuery.Event( "keypress" );
							e.which = 13;
							$( this ).next().trigger( e );
						} )
					;
					$( t ).find( '.PSK_S2MSFB_reloadbtn' )
						.click( function () {
							if ( searchgroup >= 1 ) {
								$( "li.PSK_S2MSFB_searchli[data-group=" + searchgroup + "] .PSK_S2MSFB_searchinp" ).each( function () {
									searchTree( $( this ).parent().parent().parent().parent() , '' );
								} );
							}
							else {
								searchTree( $( this ).parent().parent().parent().parent() , '' );
							}
						} )
					;
					$( t ).find( '.PSK_S2MSFB_resetbtn' )
						.click( function () {
							$( this ).next().next().val( $( this ).next().next().attr( 'title' ) );
							$( this ).hide();
						} )
					;
					// Preview for mp3
					if ( document.getElementById( 'psk_jquery_jplayer' ) == null ) {
						$( t ).append( '<div id="psk_jquery_jplayer" style="width:1px!important;height:1px!important;"></div>' );
					}
					$( t ).find( 'LI SPAN.prev[data-e=mp3]' ).each( function () {
						PSK_S2MSFB_jplayer_id ++;
						var thisdesign = "PSK_S2MSFB_jdesign" + PSK_S2MSFB_jplayer_id;
						var thisurl = $( this ).attr( 'rel' );
						$( this )
							.html( '<div id="' + thisdesign + '" class="psk_jqjp play"></div>' )
							.unbind( 'click' )
							.click( function () {
								if ( $( '#' + thisdesign ).hasClass( 'play' ) ) {
									$( ".psk_jqjp" )
										.removeClass( 'stop' )
										.addClass( 'play' );
									$( '#' + thisdesign )
										.removeClass( 'play' )
										.addClass( 'stop' );
									$( "#psk_jquery_jplayer" ).jPlayer( "destroy" );
									$( "#psk_jquery_jplayer" )
										.jPlayer( {
											ready        : function () {
												$( this )
													.jPlayer( "setMedia" , { mp3: thisurl } )
													.jPlayer( 'play' );
											} ,
											ended        : function () {
												$( '#' + thisdesign )
													.removeClass( 'stop' )
													.addClass( 'play' );
											} ,
											pause        : function () {
												$( '#' + thisdesign )
													.removeClass( 'stop' )
													.addClass( 'play' );
											} ,
											swfPath      : o.swfurl ,
											supplied     : 'mp3' ,
											solution     : 'flash,html' ,
											preload      : 'auto' ,
											volume       : 1 ,
											muted        : false ,
											errorAlerts  : false ,
											warningAlerts: false ,
											wmode        : "window"
										} );
								}
								else {
									$( "#psk_jquery_jplayer" ).jPlayer( "destroy" );

									$( '#' + thisdesign )
										.removeClass( 'stop' )
										.addClass( 'play' );
								}

							} );
					} );

					// Preview for images
					var ext_pic_loaded = false;
					$( t ).find( 'LI SPAN.prev[data-e=pic]' ).each( function () {
						ext_pic_loaded = true;
						$( this )
							.html( '<div class="play"><a title="' + $( this ).parent().parent().attr( 'data-n' ) + '" href="' + $( this ).attr( 'rel' ) + '" rel="PSKprettyPhoto[pp_gal]"><img src="' + PSK_S2MSFB.imgurl + '/blank.png" width="16" height="16" alt=""/></a></div>' );
					} );
					if ( ext_pic_loaded ) {
						$( t ).find( "a[rel^='PSKprettyPhoto']" ).prettyPhoto( {social_tools: false , overlay_gallery: false , deeplinking: false} );
					}


					$( t ).find( 'LI DIV A.link,LI DIV A.linko' ).bind( o.folderevent , function ( e ) {
						if ( $( this ).parent().parent().hasClass( 'directory' ) ) {
							if ( $( this ).parent().parent().hasClass( 'collapsed' ) ) {
								// Expand
								if ( ! o.multifolder ) {
									$( this ).parent().parent().parent().find( 'UL' ).slideUp( { duration: o.collapsespeed , easing: o.collapseeasing } );
									$( this ).parent().parent().parent().find( 'LI.directory' ).removeClass( 'expanded' ).addClass( 'collapsed' );
								}
								if ( $( this ).attr( 'rel' ) == '' ) {
									$( this ).parent().parent().find( 'UL' ).slideDown( { duration: o.expandspeed , easing: o.expandeasing } );
									$( this ).parent().parent().removeClass( 'collapsed' ).addClass( 'expanded' );
								}
								else {
									$( this ).parent().parent().find( 'UL' ).remove(); // cleanup
									showTree( $( this ).parent().parent() , encodeURIComponent( $( this ).attr( 'rel' ).match( /.*\// ) ) );
									$( this ).parent().parent().removeClass( 'collapsed' ).addClass( 'expanded' );
								}
							}
							else {
								// Collapse
								$( this ).parent().parent().find( 'UL' ).slideUp( { duration: o.collapsespeed , easing: o.collapseeasing } );
								$( this ).parent().parent().removeClass( 'expanded' ).addClass( 'collapsed' );
							}
						}
						else {
							h( $( this ) , e );
						}
						return false;
					} );

					// Prevent A from triggering the # on non-click events
					if ( o.folderevent.toLowerCase != 'click' ) {
						$( t ).find( 'LI DIV A.link' ).bind( 'click' , function () {
							return false;
						} );
					}

					// Show only first search box if there are several shortcodes in the same group
					if ( searchgroup >= 1 ) {
						$( "li.PSK_S2MSFB_searchli[data-group=" + searchgroup + "]" ).hide();
						$( "li.PSK_S2MSFB_searchli[data-group=" + searchgroup + "]:first" ).show();
					}
				}

				// Loading message
				$( this ).html( '<ul class="jqueryFileTree start"><li class="waitinit">' + o.loadmessage + '<li></ul>' );

				// Get the initial file list
				showTree( $( this ) , '/' );
			} );
		}
	} );

}( jQuery ));

var PSK_S2MSFB_jplayer_id = 0;
