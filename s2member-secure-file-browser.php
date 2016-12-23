<?php
/*
Plugin Name: s2member Secure File Browser
Plugin URI: http://www.potsky.com/code/wordpress-plugins/s2member-secure-file-browser/
Description:	A plugin for browsing files from the secure-files location of the s2member WordPress Membership plugin.
				You can display the file browser via the shortcode [s2member_secure_files_browser /].
				You can manage files and get statistics in the Dashboard > s2Member > Secure File Browser
Version: 0.4.19
Date: 2016-04-07
Author: Potsky
Author URI: http://www.potsky.com/about/
Licence:
	Copyright © 2014 Raphael Barbate (potsky) <potsky@me.com> [http://www.potsky.com]
	This file is part of s2member Secure File Browser.

	s2member Secure File Browser is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License.

	s2member Secure File Browser is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with s2member Secure File Browser.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ( realpath( __FILE__ ) === realpath( $_SERVER[ "SCRIPT_FILENAME" ] ) )
	||
	( ! defined( 'ABSPATH' ) )
) {
	status_header( 404 );
	exit;
}

// Verify if s2member is active
//
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 's2member/s2member.php' ) ) {

	define( 'PSK_S2MSFB_PLUGIN_FILE' , __FILE__ );
	require( 'inc/define.php' );

	// Verify versions
	//
	if ( ! version_compare( PHP_VERSION , PSK_S2MSFB_MIN_PHP_VERSION , ">=" ) ) {
		add_action( "all_admin_notices" , create_function( '' , 'echo \'<div class="error fade"><p>You need PHP v\' . PSK_S2MSFB_MIN_PHP_VERSION . \'+ to use \' . PSK_S2MSFB_NAME . \'.</p></div>\';' ) );
	} else if ( ! version_compare( get_bloginfo( "version" ) , PSK_S2MSFB_MIN_WP_VERSION , ">=" ) ) {
		add_action( "all_admin_notices" , create_function( '' , 'echo \'<div class="error fade"><p>You need WordPress® v\' . PSK_S2MSFB_MIN_WP_VERSION . \'+ to use \' . PSK_S2MSFB_NAME . \'.</p></div>\';' ) );
	} else {
		/*
		 * Trick to unload my plugin when debugging remotely on wordpress installations
		 * - Just set a GET parameter to psk_s2msfb_unload to 1 to unload
		 * - fix the bug in the dashboard
		 * - Reactivate by setting GET parameter psk_s2msfb_unload to 0
		 */
		if ( isset( $_GET[ 'psk_s2msfb_unload' ] ) ) {
			if ( $_GET[ 'psk_s2msfb_unload' ] == '0' ) {
				setcookie( 'psk_s2msfb_unload' , '1' , 1 );
				unset( $_COOKIE[ 'psk_s2msfb_unload' ] );
			} else {
				setcookie( 'psk_s2msfb_unload' , '1' );
				$_COOKIE[ 'psk_s2msfb_unload' ] = '1';
			}
		}
		if ( ! isset( $_COOKIE[ 'psk_s2msfb_unload' ] ) ) {
			if ( isset( $_GET[ 'psk_s2msfb_download' ] ) ) {
				require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.download.class.php' );
			} else {
				require( PSK_S2MSFB_INCLUDES_FOLDER . 'tools.class.php' );
				require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.widgets.class.php' );
				require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.class.php' );
			}
			if ( is_admin() ) {
				if ( isset( $_GET[ 'psk_s2msfb_download' ] ) ) {
					require( PSK_S2MSFB_INCLUDES_FOLDER . 'tools.class.php' );
					require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.admin.download.class.php' );
				} else {
					require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.admin.class.php' );
				}
			}
		}
	}
}

