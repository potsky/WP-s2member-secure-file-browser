<?php
/*
	Copyright © 2013 Raphael Barbate (potsky) <potsky@me.com> [http://www.potsky.com]
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
if ( ( realpath( __FILE__ ) === realpath( $_SERVER[ "SCRIPT_FILENAME" ] ) ) || ( ! defined( 'ABSPATH' ) ) ) {
	if ( function_exists( 'status_header' ) ) {
		status_header( 404 );
	} else {
		header( 'HTTP/1.0 404 Not Found' );
		echo "<h1>404 Not Found</h1>";
		echo "The page that you have requested could not be found.";
	}
	exit;
}


/**
 * Class PSK_S2MSFBDownload
 */
class PSK_S2MSFBDownload {

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init'           , array( __CLASS__ , 'plugin_init' ) );
		add_action( 'plugins_loaded' , array( __CLASS__ , 'plugins_loaded' ) );
	}


	/**
	 * WP init
	 *
	 * @wp_action    init
	 * @return          void
	 */
	public static function plugin_init() {
	}


	/**
	 * WP plugins_loaded
	 *
	 * @wp_action    plugins_loaded
	 * @return          void
	 */
	public static function plugins_loaded() {
		// Set up language
		load_plugin_textdomain( PSK_S2MSFB_ID , false , dirname( plugin_basename( PSK_S2MSFB_PLUGIN_FILE ) ) . '/languages/' );
	}

}

PSK_S2MSFBDownload::init();

