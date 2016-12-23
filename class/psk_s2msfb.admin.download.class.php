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
 * Class PSK_S2MSFBAdminDownload
 */
class PSK_S2MSFBAdminDownload {

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init' , array( __CLASS__ , 'plugin_init' ) );
		add_action( 'plugins_loaded' , array( __CLASS__ , 'plugins_loaded' ) );
	}


	/**
	 * WP init
	 *
	 * @wp_action    init
	 * @return          void
	 */
	public static function plugin_init() {
		// Set up language
		load_plugin_textdomain( PSK_S2MSFB_ID , false , dirname( plugin_basename( PSK_S2MSFB_PLUGIN_FILE ) ) . '/languages/' );

		$settings = get_option( PSK_S2MSFB_OPT_SETTINGS_GENERAL );
		$capstats = strtolower( trim( ( isset( $settings[ 'capstats' ] ) ) ? $settings[ 'capstats' ] : PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ) );

		switch ( $_GET[ 'psk_s2msfb_download' ] ) {
			case 'psk_s2msfb_stats_all_xml':
				if ( current_user_can( $capstats ) ) {
					self::psk_s2msfb_stats_all_xml();
				}
				break;
			case 'psk_s2msfb_stats_all_csv':
				if ( current_user_can( $capstats ) ) {
					self::psk_s2msfb_stats_all_csv();
				}
				break;
			default:
				break;
		}
		die();
	}


	/**
	 * WP plugins_loaded
	 *
	 * @wp_action    plugins_loaded
	 * @return          void
	 */
	public static function plugins_loaded() {
	}


	/**
	 * Send headers to download a file
	 *
	 * @param $filename
	 */
	private static function download_send_headers( $filename ) {
		header( "Content-Description: File Transfer" );
		header( "Pragma: public" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Content-Disposition: attachment;filename={$filename}" );
		header( "Content-Transfer-Encoding: binary" );
	}


	/**
	 * Return a csv file from an array
	 *
	 * @param array $array
	 *
	 * @return null|string
	 */
	private static function array2csv( $array ) {
		if ( count( $array ) == 0 ) {
			return null;
		}
		ob_start();
		$df = fopen( "php://output" , 'w' );
		fputcsv( $df , array_keys( reset( $array ) ) , "\t" );
		foreach ( $array as $row ) {
			fputcsv( $df , $row , "\t" );
		}
		fclose( $df );

		return ob_get_clean();
	}


	/**
	 * Return a xml file of all download stats
	 */
	private static function psk_s2msfb_stats_all_xml() {
		$minid = (int) $_GET[ 'n' ];

		set_time_limit( 0 );

		$xml = new SimpleXMLElement( '<s2msfballdownload/>' );
		$xml->addAttribute( 'minid' , $minid );

		/** @var $wpdb WPDB */
		global $wpdb;
		$mysqli = PSK_Tools::get_mysqli_cx();
		if ( is_string( $mysqli ) ) {
			$error = $xml->addChild( 'error' );
			$error->addChild( 'num' , '1' );
			$error->addChild( 'msg' , $mysqli );

			return;
		}

		$users = array();
		foreach ( get_users() as $user ) {
			$users[ $user->ID ] = $user;
		}

		$mysqli->set_charset( "utf8" );
		/** @var $tablename $string */

		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$sql       = "SELECT id,created,userid,useremail,ip,filepath FROM $tablename WHERE id>=$minid";
		if ( $stmt = $mysqli->prepare( $sql ) ) {

			$stmt->execute();
			$stmt->bind_result( $db_id , $db_created , $db_userid , $db_useremail , $db_ip , $db_filepath );
			$stmt->store_result();

			while ( $stmt->fetch() ) {

				$username      = '';
				$userlastname  = '';
				$userfirstname = '';

				if ( isset( $users[ $db_userid ] ) ) {
					/** @var WP_User $user */
					$user          = $users[ $db_userid ];
					$username      = $user->nickname;
					$userlastname  = $user->last_name;
					$userfirstname = $user->first_name;
				}

				$d      = $xml->addChild( 'd' );
				$d->{0} = $db_filepath;
				$d->addAttribute( 'id' , $db_id );
				$d->addAttribute( 'ts' , date( "Y-m-d\Th:i:s" , strtotime( $db_created ) ) );
				$d->addAttribute( 'uid' , $db_userid );
				$d->addAttribute( 'nickname' , $username );
				$d->addAttribute( 'lastname' , $userlastname );
				$d->addAttribute( 'firstname' , $userfirstname );
				$d->addAttribute( 'uemail' , $db_useremail );
				$d->addAttribute( 'uip' , $db_ip );
			}
			$stmt->free_result();
			$stmt->close();
		}

		self::download_send_headers( str_replace( ' ' , '' , get_bloginfo( 'name' ) ) . "_s2msfb_downloads_from_id" . $minid . "_" . date( "Y-m-d-His" ) . ".xml" );
		header( "Content-type: text/xml; charset=UTF-8" );
		echo $xml->asXML();
	}


	/**
	 * Display a csv file of all download stats
	 */
	private static function psk_s2msfb_stats_all_csv() {
		$minid = (int) $_GET[ 'n' ];
		set_time_limit( 0 );

		$csv = array();

		/** @var $wpdb WPDB */
		global $wpdb;
		$mysqli = PSK_Tools::get_mysqli_cx();
		if ( is_string( $mysqli ) ) {
			echo $mysqli;

			return;
		}

		$users = array();
		foreach ( get_users() as $user ) {
			$users[ $user->ID ] = $user;
		}

		$mysqli->set_charset( "utf8" );
		/** @var $tablename $string */

		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$sql       = "SELECT id,created,userid,useremail,ip,filepath FROM $tablename WHERE id>=$minid";
		if ( $stmt = $mysqli->prepare( $sql ) ) {

			$stmt->execute();
			$stmt->bind_result( $db_id , $db_created , $db_userid , $db_useremail , $db_ip , $db_filepath );
			$stmt->store_result();

			while ( $stmt->fetch() ) {

				$username      = '';
				$userlastname  = '';
				$userfirstname = '';

				if ( isset( $users[ $db_userid ] ) ) {
					/** @var WP_User $user */
					$user          = $users[ $db_userid ];
					$username      = $user->nickname;
					$userlastname  = $user->last_name;
					$userfirstname = $user->first_name;
				}

				$csv[] = array(
					'id'        => $db_id ,
					'file'      => $db_filepath ,
					'ts'        => date( "Y-m-d h:i:s" , strtotime( $db_created ) ) ,
					'uid'       => $db_userid ,
					'nickname'  => $username ,
					'lastname'  => $userlastname ,
					'firstname' => $userfirstname ,
					'uemail'    => $db_useremail ,
					'uip'       => $db_ip ,
				);
			}
			$stmt->free_result();
			$stmt->close();
		}
		self::download_send_headers( get_bloginfo( 'name' ) . "_s2msfb_downloads_from_id" . $minid . "_" . date( "Y-m-d-His" ) . ".csv" );

		header( "Content-type: application/vnd.ms-excel; charset=UTF-16LE" );
		echo chr( 255 ) . chr( 254 ) . mb_convert_encoding( self::array2csv( $csv ) , 'UTF-16LE' , 'UTF-8' );
	}
}

PSK_S2MSFBAdminDownload::init();

