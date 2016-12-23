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
	status_header( 404 );
	exit;
}


/**
 * Class PSK_Tools
 */
class PSK_Tools {

	/**
	 * Get a MYSQLi connector
	 *
	 * @return  mysqli  a new mysqli
	 */
	public static function get_mysqli_cx() {
		@list( $url , $port ) = @explode( ':' , DB_HOST );
		$port = (int)$port;
		if ( $port <= 0 ) {
			$mysqli = new mysqli( DB_HOST , DB_USER , DB_PASSWORD , DB_NAME );
		}
		else {
			$mysqli = new mysqli( $url , DB_USER , DB_PASSWORD , DB_NAME , $port );
		}

		if ( mysqli_connect_errno() )
			return "Connect failed: " . mysqli_connect_error();

		$mysqli->set_charset( "utf8" );

		return $mysqli;
	}

	/**
	 * Log in a text for remote debug
	 *
	 * @param   string  $message  the message to log
	 *
	 * @return  void
	 */
	public static function log( $message ) {
		file_put_contents( dirname( __FILE__ ) . '/../log.txt' , date('Y/m/d H:i:s') . ' | ' . $_SERVER['REMOTE_ADDR'] . ' | ' . $message . "\n" , FILE_APPEND );
	}

	/**
	 * Return the avatar url of a user
	 *
	 * @param       string $string      a user id or user email
	 *
	 * @return      string               the url of the gravatar
	 */
	public static function get_avatar_url( $user ) {
		preg_match( "/src='(.*?)'/i" , get_avatar( $user ) , $matches );
		return $matches[ 1 ];
	}


	/**
	 * Return file name in the /img directory of the file icon acording to its extension
	 *
	 * @param       string $string      a file name, file path...
	 *
	 * @return      string               the icon file name
	 */
	public static function get_file_icon( $file_path ) {
		$extensions = array(
			'3gp'   => 'film.png' ,
			'afp'   => 'code.png' ,
			'afpa'  => 'code.png' ,
			'asp'   => 'code.png' ,
			'aspx'  => 'code.png' ,
			'avi'   => 'film.png' ,
			'bat'   => 'application.png' ,
			'bmp'   => 'picture.png' ,
			'c'     => 'code.png' ,
			'cfm'   => 'code.png' ,
			'cgi'   => 'code.png' ,
			'com'   => 'application.png' ,
			'cpp'   => 'code.png' ,
			'css'   => 'css.png' ,
			'doc'   => 'doc.png' ,
			'exe'   => 'application.png' ,
			'gif'   => 'picture.png' ,
			'fla'   => 'flash.png' ,
			'h'     => 'code.png' ,
			'htm'   => 'html.png' ,
			'html'  => 'html.png' ,
			'jar'   => 'java.png' ,
			'jpg'   => 'picture.png' ,
			'jpeg'  => 'picture.png' ,
			'js'    => 'script.png' ,
			'lasso' => 'code.png' ,
			'log'   => 'txt.png' ,
			'm4p'   => 'music.png' ,
			'mov'   => 'film.png' ,
			'mp3'   => 'music.png' ,
			'mp4'   => 'film.png' ,
			'mpg'   => 'film.png' ,
			'mpeg'  => 'film.png' ,
			'mpeg4' => 'film.png' ,
			'ogg'   => 'music.png' ,
			'pcx'   => 'picture.png' ,
			'pdf'   => 'pdf.png' ,
			'php'   => 'php.png' ,
			'png'   => 'picture.png' ,
			'ppt'   => 'ppt.png' ,
			'pps'   => 'ppt.png' ,
			'psd'   => 'psd.png' ,
			'pl'    => 'script.png' ,
			'py'    => 'script.png' ,
			'rb'    => 'ruby.png' ,
			'rbx'   => 'ruby.png' ,
			'rhtml' => 'ruby.png' ,
			'rpm'   => 'linux.png' ,
			'ruby'  => 'ruby.png' ,
			'sql'   => 'db.png' ,
			'swf'   => 'flash.png' ,
			'tif'   => 'picture.png' ,
			'tiff'  => 'picture.png' ,
			'txt'   => 'txt.png' ,
			'vb'    => 'code.png' ,
			'wav'   => 'music.png' ,
			'wmv'   => 'film.png' ,
			'xls'   => 'xls.png' ,
			'xml'   => 'code.png' ,
			'zip'   => 'zip.png' ,
			'rar'   => 'zip.png' ,
			'bz2'   => 'zip.png' ,
			'tar'   => 'zip.png' ,
			'gz'    => 'zip.png' ,
			'vsa'   => 'vsa.png' ,
		);

		$ext = trim( mb_strtolower( mb_substr( $file_path , mb_strrpos( $file_path , '.' ) + 1 ) ) );
		if ( array_key_exists( $ext , $extensions ) ) {
			return $extensions[ $ext ];
		} else {
			return 'file.png';
		}
	}


	/**
	 * current_user_cans is a current_user_can with several capablities separated by coma
	 *
	 * @param       string $string      the list of capabilities separated by coma
	 *
	 * @return      false                if access not granted
	 * @return      string               a granted capability
	 */
	public static function current_user_cans( $capablities ) {
		$caps = array_unique( explode( ',' , $capablities ) );
		foreach ( $caps as $cap ) {
			$c = strtolower( trim( $cap ) );
			if ( current_user_can( $c ) ) {
				return $c;
			}
		}
		return false;
	}


	/**
	 * return true if a $string starts with $start
	 *
	 * @param       string $string      the haystack
	 * @param       string $start       the start needle
	 *
	 * @return      boolean
	 */
	public static function starts_with( $string , $start ) {
		return ( mb_substr( $string , 0 , mb_strlen( $start ) ) == $start );
	}


	/**
	 * Trim all [anti]slashes at the end and the beginning of a directory
	 *
	 * @param string $directory  the directory to check
	 * @param bool   $start      must begin by a slash
	 * @param bool   $end        must end by a slash
	 *
	 * @return      boolean
	 */
	public static function sanitize_directory_path( $directory , $start = false , $end = false ) {
		$directory = str_replace( DIRECTORY_SEPARATOR , PSK_S2MSFB_DIRECTORY_SEPARATOR , $directory );
		while ( mb_substr( $directory , - 1 , 1 ) == PSK_S2MSFB_DIRECTORY_SEPARATOR ) {
			$directory = mb_substr( $directory , 0 , - 1 );
		}
		while ( mb_substr( $directory , 0 , 1 ) == PSK_S2MSFB_DIRECTORY_SEPARATOR ) {
			$directory = mb_substr( $directory , 1 );
		}
		if ( $end )
			$directory .= PSK_S2MSFB_DIRECTORY_SEPARATOR;
		if ( $start )
			$directory = PSK_S2MSFB_DIRECTORY_SEPARATOR . $directory;
		$directory = str_replace( '//' , '/' , $directory );
		return $directory;
	}


	/**
	 * Check if the specified directory is in s2member_files_path directory
	 *
	 * @param       string $directory   the directory to check
	 *
	 * @return      boolean
	 */
	public static function is_directory_allowed( $directory ) {
		$child  = realpath( $directory );
		$parent = realpath( PSK_S2MSFB_S2MEMBER_FILES_FOLDER );
		return self::starts_with( $child , $parent );
	}


	/**
	 * Remove recursively a directory or a file with check if the specified file/dir is in s2member_files_path directory
	 *
	 * @param       string $filepath   the directory or file to delete
	 *
	 * @return      boolean
	 */
	public static function rm_secure_recursive( $filepath ) {
		if ( is_dir( $filepath ) && ! is_link( $filepath ) ) {

			if ( $dh = opendir( $filepath ) ) {

				while ( ( $sf = readdir( $dh ) ) !== false ) {

					if ( $sf == '.' || $sf == '..' ) {
						continue;
					}

					if ( ! self::rm_secure_recursive( $filepath . PSK_S2MSFB_DIRECTORY_SEPARATOR . $sf ) ) {
						throw new Exception( $filepath . PSK_S2MSFB_DIRECTORY_SEPARATOR . $sf . ' could not be deleted.' );
					}
				}
				closedir( $dh );
			}

			if ( ! self::is_directory_allowed( $filepath ) )
				throw new Exception( $filepath . PSK_S2MSFB_DIRECTORY_SEPARATOR . ' could not be deleted.' );

			return rmdir( $filepath );
		}

		if ( ! self::is_directory_allowed( $filepath ) )
			throw new Exception( $filepath . PSK_S2MSFB_DIRECTORY_SEPARATOR . ' could not be deleted.' );

		return unlink( $filepath );
	}


	/**
	 * Return human readable sizes
	 *
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.3.0
	 * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
	 *
	 * @param       int    $size        size in bytes
	 * @param       string $max         maximum unit
	 * @param       string $system      'si' for SI, 'bi' for binary prefixes
	 * @param       string $retstring   return string format
	 *
	 * @return      string               readable sizes
	 */
	public static function size_readable( $size , $max = null , $system = 'si' , $retstring = '%01.2f %s' ) {
		$sys[ 'si' ][ 'p' ] = array( _x( 'B' , 'Bytes abbr' , PSK_S2MSFB_ID ) , _x( 'KB' , 'Kilobytes abbr' , PSK_S2MSFB_ID ) , _x( 'MB' , 'Megabytes abbr' , PSK_S2MSFB_ID ) , _x( 'GB' , 'Gigabytes abbr' , PSK_S2MSFB_ID ) , _x( 'TB' , 'Terabytes abbr' , PSK_S2MSFB_ID ) , _x( 'PB' , 'Petabytes abbr' , PSK_S2MSFB_ID ) );
		$sys[ 'si' ][ 's' ] = 1000;
		$sys[ 'bi' ][ 'p' ] = array( _x( 'B' , 'Bytes abbr' , PSK_S2MSFB_ID ) , _x( 'KiB' , 'Kibibytes abbr' , PSK_S2MSFB_ID ) , _x( 'MiB' , 'Mebibytes abbr' , PSK_S2MSFB_ID ) , _x( 'GiB' , 'Gibibytes abbr' , PSK_S2MSFB_ID ) , _x( 'TiB' , 'Tebibytes abbr' , PSK_S2MSFB_ID ) , _x( 'PiB' , 'Pebibytes abbr' , PSK_S2MSFB_ID ) );
		$sys[ 'bi' ][ 's' ] = 1024;
		$sys                = isset( $sys[ $system ] ) ? $sys[ $system ] : $sys[ 'si' ];

		$depth = count( $sys[ 'p' ] ) - 1;
		if ( $max && false !== $d = array_search( $max , $sys[ 'p' ] ) ) $depth = $d;
		$i = 0;
		while ( $size >= $sys[ 's' ] && $i < $depth ) {
			$size /= $sys[ 's' ];
			$i ++;
		}
		return sprintf( $retstring , $size , $sys[ 'p' ][ $i ] );
	}


	/**
	 * Return an escaped value for a html attribute
	 *
	 * @param       string $str         the value to escape
	 *
	 * @return      string               the escaped value
	 */
	public static function rel_literal( $str ) {
		//return htmlspecialchars($str,ENT_COMPAT|ENT_HTML401,'UTF-8'|); // Only for PHP >= 5.4
		return htmlspecialchars( $str , ENT_COMPAT , 'UTF-8' );
	}


	/**
	 * Return an utf8 htmlentities value
	 *
	 * @param       string $str         the value to escape
	 *
	 * @return      string               the escaped value
	 */
	public static function html_entities( $str ) {
		//return htmlentities($str,ENT_COMPAT|ENT_HTML401,'UTF-8'|); // Only for PHP >= 5.4
		return htmlentities( $str , ENT_COMPAT , 'UTF-8' );
	}

	/**
	 * Return a javascript literal
	 *
	 * @param       string $str         the value
	 *
	 * @return      string               the literalized value
	 */
	public static function js_literal( $str ) {
		//return htmlentities('\''.str_replace('\'','\\\'',str_replace('\\','\\\\',$str)).'\'',ENT_COMPAT|ENT_HTML401,'UTF-8'); // Only for PHP >= 5.4
		//return htmlentities( '\'' . str_replace( '\'', '\\\'', str_replace( '\\', '\\\\', $str ) ) . '\'', ENT_COMPAT, 'UTF-8' );
		return '\'' . str_replace( '"' , '&quot;' , str_replace( '\'' , '\\\'' , str_replace( '\\' , '\\\\' , $str ) ) ) . '\'';
	}


	/**
	 * Escapes JavaScript and single quotes.
	 *
	 * @param string $string Input string.
	 * @param int $times  Number of escapes. Defaults to 1.
	 *
	 * @return string Output string after JavaScript and single quotes are escaped.
	 */
	public static function js_esc_string( $string = FALSE , $times = FALSE ) {
		$times = ( is_numeric( $times ) && $times >= 0 ) ? (int) $times : 1;
		return str_replace( "'" , str_repeat( "\\" , $times ) . "'" , str_replace( array( "\r" , "\n" ) , array( "" , '\\n' ) , str_replace( "\'" , "'" , (string) $string ) ) );
	}


	/**
	 * Return an utf8 html_entities value
	 *
	 * @param       string $str         the value to escape
	 *
	 * @return      string               the escaped value
	 */
	public static function mb_html_entities( $str , $encoding = 'utf-8' ) {
		mb_regex_encoding( $encoding );
		$pattern     = array( '<' , '>' , '"' , '\'' );
		$replacement = array( '&lt;' , '&gt;' , '&quot;' , '&#39;' );
		for ( $i = 0 ; $i < sizeof( $pattern ) ; $i ++ ) {
			$str = mb_ereg_replace( $pattern[ $i ] , $replacement[ $i ] , $str );
		}
		return $str;
	}


	/*
	 * Display javascript alert. Examples :
	 * 	psk_sfb_alert('Error!','File has been deleted','error');
	 * 	psk_sfb_alert('Info!','File has been deleted','info',4000);
	 * 	psk_sfb_alert('Success!','File has been deleted','success');
	 * 	psk_sfb_alert('Warning!','File has been deleted');
	 *
	 * @param        $title
	 * @param        $message
	 * @param string $alert
	 * @param int    $time
	 *
	 * @return string
	 */
	public static function get_js_alert( $title , $message , $alert = 'info' , $time = 5000 ) {
		$time = (int) $time;
		$ret  = '<script>psk_sfb_alert(' . self::js_literal( $title ) . ', ' . self::js_literal( $message ) . ', ' . self::js_literal( $alert ) . ', ' . self::js_literal( $time ) . ');</script>';
		return $ret;
	}


	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public static function ref_array_values( $arr ) {
		if ( strnatcmp( phpversion() , '5.3' ) >= 0 ) {
			$refs = array();
			foreach ( $arr as $key => $value ) {
				$refs[ $key ] = & $arr[ $key ];
			}
			return $refs;
		}
		return $arr;
	}

}



