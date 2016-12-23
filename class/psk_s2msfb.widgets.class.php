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
 * Class PSK_S2MSFB_wdgt_download
 * Widget to display latest download or top downloads
 */
class PSK_S2MSFB_wdgt_download extends WP_Widget {

	private static $types_options                    = array();
	private static $display_icon_options             = array();
	private static $display_time_options             = array();
	private static $display_files_options            = array();
	private static $display_directory_source_options = array();

	public function __construct() {

		self::$types_options                    = array(
			'l'   => __( 'Latest downloads' , PSK_S2MSFB_ID ) ,
			'0'   => __( 'Top downloads' , PSK_S2MSFB_ID ) ,
			'1'   => __( 'Top downloads for a day' , PSK_S2MSFB_ID ) ,
			'7'   => __( 'Top downloads for a week' , PSK_S2MSFB_ID ) ,
			'31'  => __( 'Top downloads for a month' , PSK_S2MSFB_ID ) ,
			'365' => __( 'Top downloads for a year' , PSK_S2MSFB_ID ) ,
		);
		self::$display_icon_options             = array(
			''  => __( 'No' , PSK_S2MSFB_ID ) ,
			'f' => __( 'File icon' , PSK_S2MSFB_ID ) ,
			'u' => __( 'Downloader Gravatar (only for Latest downloads type)' , PSK_S2MSFB_ID ) ,
		);
		self::$display_time_options             = array(
			''  => __( 'No' , PSK_S2MSFB_ID ) ,
			't' => __( 'Date + time' , PSK_S2MSFB_ID ) ,
			'd' => __( 'Date only' , PSK_S2MSFB_ID ) ,
		);
		self::$display_files_options            = array(
			'a' => __( 'All' , PSK_S2MSFB_ID ) ,
			'l' => __( 'All with reachable links for the current user only' , PSK_S2MSFB_ID ) ,
			'm' => __( 'All without links' , PSK_S2MSFB_ID ) ,
			'o' => __( 'Only downloadable files by current user with links' , PSK_S2MSFB_ID ) ,
			'p' => __( 'Only downloadable files by current user without links' , PSK_S2MSFB_ID ) ,
		);
		self::$display_directory_source_options = array(
			''   => __( 'No' , PSK_S2MSFB_ID ) ,
			'1'  => __( 'In file name : First parent/file' , PSK_S2MSFB_ID ) ,
			'2'  => __( 'In file name : Grand parent/First parent/file' , PSK_S2MSFB_ID ) ,
			'3'  => __( 'In file name : First ancestor/.../file' , PSK_S2MSFB_ID ) ,
			'4'  => __( 'In file name : First ancestor/.../First parent/file' , PSK_S2MSFB_ID ) ,
			'5'  => __( 'In file name : First ancestor/Second ancestor/.../file' , PSK_S2MSFB_ID ) ,
			'6'  => __( 'In file name : Full file path' , PSK_S2MSFB_ID ) ,
			'1n' => __( 'On a new line : First parent/' , PSK_S2MSFB_ID ) ,
			'2n' => __( 'On a new line : Grand parent/First parent/' , PSK_S2MSFB_ID ) ,
			'3n' => __( 'On a new line : First ancestor/.../' , PSK_S2MSFB_ID ) ,
			'4n' => __( 'On a new line : First ancestor/.../First parent/' , PSK_S2MSFB_ID ) ,
			'5n' => __( 'On a new line : First ancestor/Second ancestor/.../' , PSK_S2MSFB_ID ) ,
			'6n' => __( 'On a new line : Full directory path' , PSK_S2MSFB_ID ) ,
		);
		$widget_ops                             = array(
			'classname'   => PSK_S2MSFB_WIDGET_DOWNLOAD_ID ,
			'description' => __( 'Display latest and top downloads' , PSK_S2MSFB_ID ) ,
		);

		parent::__construct(
			PSK_S2MSFB_WIDGET_DOWNLOAD_ID , // Base ID
			PSK_S2MSFB_WIDGET_DOWNLOAD_NAME , // Name
			$widget_ops
		);

	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args , $instance ) {
		/** @var $before_widget $string */
		/** @var $before_title $string */
		/** @var $after_title $string */
		/** @var $after_widget $string */
		/** @var $widget_id $string */

		extract( $args );

		$title                    = empty( $instance[ 'title' ] ) ? '' : apply_filters( 'widget_title' , $instance[ 'title' ] );
		$limit                    = $instance[ 'limit' ];
		$names                    = $instance[ 'names' ];
		$types                    = $instance[ 'types' ];
		$display_icon             = $instance[ 'display_icon' ];
		$display_time             = $instance[ 'display_time' ];
		$display_files            = $instance[ 'display_files' ];
		$display_directory_source = $instance[ 'display_directory_source' ];
		$filterfile               = $instance[ 'filterfile' ];
		$filterdir                = $instance[ 'filterdir' ];
		$show_username            = ( $instance[ 'show_username' ] == '1' ) ? true : false;
		$show_count               = ( $instance[ 'show_count' ] == '1' ) ? true : false;
		$show_hr                  = ( $instance[ 'show_hr' ] == '1' ) ? true : false;
		$show_s2alertbox          = ( $instance[ 'show_s2alertbox' ] == '1' ) ? true : false;

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo ( $show_hr ) ? '<div style="width:100%;height:3px;border-bottom:3px solid #888;opacity:0.2"></div>' : '';
		switch ( $types ) {
			case 'l' :
				echo self::get_latest_downloads( $widget_id , $limit , $names , $types , $display_icon , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $show_username , $show_count , $show_hr , $show_s2alertbox );
				break;
			default:
				echo self::get_top_downloads( $widget_id , $limit , $names , $types , $display_icon , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $show_username , $show_count , $show_hr , $show_s2alertbox );
				break;
		}
		echo $after_widget;
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance , $old_instance ) {
		$instance                      = $old_instance;
		$instance[ 'show_hr' ]         = '';
		$instance[ 'show_s2alertbox' ] = '';
		$instance[ 'show_username' ]   = '';
		$instance[ 'show_count' ]      = '';
		foreach ( $new_instance as $key => $value ) {
			$instance[ $key ] = strip_tags( $value );
		}

		return $instance;
	}


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		//  Assigns values
		$instance = wp_parse_args( (array) $instance , array(
			'title'                    => 'Latest Downloads' ,
			'limit'                    => '10' ,
			'names'                    => 'access-s2member-level0:Free|access-s2member-level1:Bronze|access-s2member-level2:Silver|access-s2member-level3:Gold|access-s2member-level4:Platinum|access-s2member-ccap-videos:Videos' ,
			'types'                    => 'l' ,
			'display_icon'             => 'f' ,
			'display_time'             => 'd' ,
			'display_files'            => 'm' ,
			'display_directory_source' => '' ,
			'filterfile'               => '/\\.(png|jpe?g|gif|zip)$/i' ,
			'filterdir'                => '/^\/access-s2member-level0\/$/' ,
			'show_username'            => '' ,
			'show_count'               => '' ,
			'show_hr'                  => '1' ,
			'show_s2alertbox'          => '' ,
		) );

		$title                    = strip_tags( $instance[ 'title' ] );
		$limit                    = strip_tags( $instance[ 'limit' ] );
		$names                    = strip_tags( $instance[ 'names' ] );
		$types                    = strip_tags( $instance[ 'types' ] );
		$display_icon             = strip_tags( $instance[ 'display_icon' ] );
		$display_time             = strip_tags( $instance[ 'display_time' ] );
		$display_files            = strip_tags( $instance[ 'display_files' ] );
		$display_directory_source = strip_tags( $instance[ 'display_directory_source' ] );
		$filterfile               = strip_tags( $instance[ 'filterfile' ] );
		$filterdir                = strip_tags( $instance[ 'filterdir' ] );
		$show_username            = strip_tags( $instance[ 'show_username' ] );
		$show_count               = strip_tags( $instance[ 'show_count' ] );
		$show_hr                  = strip_tags( $instance[ 'show_hr' ] );
		$show_s2alertbox          = strip_tags( $instance[ 'show_s2alertbox' ] );


		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Title' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" ';
		echo 'name="' . $this->get_field_name( 'title' ) . '" type="text" ';
		echo 'value="' . esc_attr( $title ) . '" title="' . __( 'Title of the widget as it appears on the page' , PSK_S2MSFB_ID ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'limit' ) . '">' . __( 'Number of items to show' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'limit' ) . '" ';
		echo 'name="' . $this->get_field_name( 'limit' ) . '" type="number" ';
		echo 'value="' . esc_attr( $limit ) . '" title="" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'types' ) . '">' . __( 'What to show' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'types' ) . '" name="' . $this->get_field_name( 'types' ) . '">';
		foreach ( self::$types_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $types , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_icon' ) . '">' . __( 'Display icon' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_icon' ) . '" name="' . $this->get_field_name( 'display_icon' ) . '">';
		foreach ( self::$display_icon_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_icon , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_time' ) . '">' . __( 'Display time' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_time' ) . '" name="' . $this->get_field_name( 'display_time' ) . '">';
		foreach ( self::$display_time_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_time , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '<br><small>' . __( 'Only for type Latest Downloads' , PSK_S2MSFB_ID ) . '</small>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_files' ) . '">' . __( 'Display files' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_files' ) . '" name="' . $this->get_field_name( 'display_files' ) . '">';
		foreach ( self::$display_files_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_files , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_directory_source' ) . '">' . __( 'Display file path' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_directory_source' ) . '" name="' . $this->get_field_name( 'display_directory_source' ) . '">';
		foreach ( self::$display_directory_source_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_directory_source , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'names' ) . '">' . __( 'Directory name replacements' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'names' ) . '" ';
		echo 'name="' . $this->get_field_name( 'names' ) . '" type="text" ';
		echo 'value="' . esc_attr( $names ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'filterfile' ) . '">' . __( 'Filter filename' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'filterfile' ) . '" ';
		echo 'name="' . $this->get_field_name( 'filterfile' ) . '" type="text" ';
		echo 'value="' . esc_attr( $filterfile ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'filterdir' ) . '">' . __( 'Filter directories name' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'filterdir' ) . '" ';
		echo 'name="' . $this->get_field_name( 'filterdir' ) . '" type="text" ';
		echo 'value="' . esc_attr( $filterdir ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'show_username' ) . '">' . __( 'Show username' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_username' ) . '" value="1" ' . checked( $show_username , '1' , false ) . '/>';
		echo '<br><small>' . __( 'Only for type Latest Downloads' , PSK_S2MSFB_ID ) . '</small>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_count' ) . '">' . __( 'Show Top Downloads count' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_count' ) . '" value="1" ' . checked( $show_count , '1' , false ) . '/>';
		echo '<br><small>' . __( 'Only for type Top Downloads' , PSK_S2MSFB_ID ) . '</small>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_hr' ) . '">' . __( 'Show separators' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_hr' ) . '" value="1" ' . checked( $show_hr , '1' , false ) . '/>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_s2alertbox' ) . '">' . __( 'Show s2member confirmation box' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_s2alertbox' ) . '" value="1" ' . checked( $show_s2alertbox , '1' , false ) . '/>';
		echo '</p>';

	}


	/**
	 * Returns the latest downloads
	 *
	 * @param string $widget_id                the widget instance
	 * @param string $limit                    preference
	 * @param string $names                    preference
	 * @param string $types                    preference
	 * @param string $display_icon             preference
	 * @param string $display_time             preference
	 * @param string $display_files            preference
	 * @param string $display_directory_source preference
	 * @param string $filterfile               preference
	 * @param string $filterdir                preference
	 * @param string $show_username            preference
	 * @param string $show_count               preference
	 * @param string $show_hr                  preference
	 * @param string $show_s2alertbox          preference
	 *
	 * @return string HTML
	 */
	private function get_latest_downloads(
		/** @noinspection PhpUnusedParameterInspection */
		$widget_id , $limit , $names , $types , $display_icon , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $show_username , $show_count , $show_hr , $show_s2alertbox
	) {

		$r     = '';
		$limit = ( (int) $limit > 0 ) ? (int) $limit : 10;

		if ( false === ( $result = get_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_LATEST_ID ) ) ) {
			/** @var $wpdb WPDB */
			global $wpdb;
			$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
			$sql       = "SELECT filepath, userid, created FROM $tablename ORDER BY created DESC LIMIT 0, 100";
			$result    = $wpdb->get_results( $sql , ARRAY_A );
			set_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_LATEST_ID , $result );
		}

		if ( count( $result ) == 0 ) {
			$r .= __( "No download" , PSK_S2MSFB_ID );
		} else {

			$users = array();
			if ( $show_username ) {
				foreach ( get_users() as $user ) {
					$users[ $user->ID ] = $user->display_name;
				}
			}

			$r = '<ul>';
			$i = 0;
			foreach ( $result as $row ) {

				$file_path = $row[ 'filepath' ];
				$user_id   = $row[ 'userid' ];
				$created   = $row[ 'created' ];
				$dir_name  = mb_substr( $file_path , 0 , strrpos( $file_path , '/' ) + 1 );
				$file_name = mb_substr( $file_path , strrpos( $file_path , '/' ) + 1 );

				if ( $filterdir ) {
					if ( ! preg_match( $filterdir , $dir_name ) ) {
						continue;
					}
				}

				if ( $filterfile ) {
					if ( ! preg_match( $filterfile , $file_name ) ) {
						continue;
					}
				}

				switch ( $display_time ) {
					case 't':
						$time = ( $show_username ) ? ' - ' : '<br/>';
						$time .= '<small>' . date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , strtotime( $created ) ) . '</small>';
						break;
					case 'd':
						$time = ( $show_username ) ? ' - ' : '<br/>';
						$time .= '<small>' . date_i18n( sprintf( '%1$s' , get_option( 'date_format' ) ) , strtotime( $created ) ) . '</small>';
						break;
					default:
						$time = '';
						break;
				}

				switch ( strval( (int) $display_directory_source ) ) {
					case '1':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ];
						break;
					case '2':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '3':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ];
						break;
					case '4':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '5':
						preg_match( "/^\/([^\/]*)\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/' . $matches[ 2 ] . '/&hellip;/' . $matches[ 3 ];
						break;
					case '6':
						$file = $file_path;
						break;
					default:
						$file = $file_name;
						break;
				}

				$replacements = explode( '|' , $names );
				foreach ( $replacements as $replacement ) {
					list( $search , $replace ) = explode( ':' , $replacement );
					$file = str_replace( $search , $replace , $file );
				}

				$show_filepath = false;
				$path          = '';
				if ( ( strval( (int) $display_directory_source ) != $display_directory_source ) && ( (int) $display_directory_source > 0 ) ) {
					$show_filepath = true;
					$path          = mb_substr( $file , 0 , mb_strrpos( $file , '/' ) + 1 );
					$path          = PSK_Tools::mb_html_entities( $path );
					$file          = mb_substr( $file , mb_strrpos( $file , '/' ) + 1 );
				}

				$file = PSK_Tools::mb_html_entities( $file );

				$s2options                        = array();
				$s2options[ 'file_download' ]     = $file_path;
				$s2options[ 'skip_confirmation' ] = ( $show_s2alertbox ) ? 'false' : 'true';
				switch ( $display_files ) {
					case 'a':
						$url  = s2member_file_download_url( $s2options );
						$file = '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'p':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : $file;
						break;
					case 'o':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'l':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? $file : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					default:
						break;
				}

				if ( $file == '' ) {
					continue;
				}

				$r .= '<li style="padding-top:4px;">';
				$r .= ( $display_icon == 'u' ) ? '<img style="float:left;width:32px;height:32px;margin-right:5px;margin-top:5px;border:1px solid #888;" src="' . PSK_Tools::get_avatar_url( $user_id , 32 ) . '" />' : '';
				$r .= ( $display_icon == 'f' ) ? '<img style="float:left;width:16px;height:16px;margin-right:5px;margin-top:2px;border:0;" src="' . PSK_S2MSFB_IMG_URL . PSK_Tools::get_file_icon( $file_path ) . '" />' : '';
				$r .= '<span style="font-size:11px;word-wrap:break-word;font-weight:bold;">';
				$r .= $file . '</span>';
				$r .= ( $show_filepath ) ? '<br/><small>' . sprintf( __( 'in %s' , PSK_S2MSFB_ID ) , $path ) . '</small>' : '';
				$r .= ( $show_username ) ? '<br/><small>' . sprintf( __( 'by %s' , PSK_S2MSFB_ID ) , $users[ $user_id ] ) . '</small>' : '';
				$r .= $time;
				$r .= ( $show_hr ) ? '<br/><div style="width:100%;height:5px;border-bottom:1px solid #888;opacity:0.2"/>' : '';
				$r .= '</li>';

				$i ++;
				if ( $i >= $limit ) {
					break;
				}
			}

			$r .= '</ul>';

			if ( $i == 0 ) {
				$r = __( "No download" , PSK_S2MSFB_ID );
			}
		}

		return $r;
	}


	/**
	 * Returns top downloads
	 *
	 * @param string $widget_id                the widget instance
	 * @param string $limit                    preference
	 * @param string $names                    preference
	 * @param string $types                    preference
	 * @param string $display_icon             preference
	 * @param string $display_time             preference
	 * @param string $display_files            preference
	 * @param string $display_directory_source preference
	 * @param string $filterfile               preference
	 * @param string $filterdir                preference
	 * @param string $show_username            preference
	 * @param string $show_count               preference
	 * @param string $show_hr                  preference
	 * @param string $show_s2alertbox          preference
	 *
	 * @return string HTML
	 */
	private function get_top_downloads(
		/** @noinspection PhpUnusedParameterInspection */
		$widget_id , $limit , $names , $types , $display_icon , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $show_username , $show_count , $show_hr , $show_s2alertbox
	) {

		$r     = '';
		$types = (int) $types;
		$tra   = 'PSK_S2MSFB_WIDGET_DOWNLOAD_TOP' . $types . '_ID';
		$limit = ( (int) $limit > 0 ) ? (int) $limit : 10;


		if ( ! defined( $tra ) ) {
			return 'Error';
		}
		$tra = constant( $tra );

		if ( false === ( $result = get_transient( $tra ) ) ) {

			/** @var $wpdb WPDB */
			global $wpdb;
			$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
			$where     = ( $types > 0 ) ? ' WHERE created > NOW() - INTERVAL ' . $types . ' DAY' : '';
			$sql       = "SELECT filepath, COUNT(*) A FROM $tablename$where GROUP BY filepath ORDER BY A DESC";
			$result    = $wpdb->get_results( $sql , ARRAY_A );
			set_transient( $tra , $result );
		}

		if ( count( $result ) == 0 ) {
			$r .= __( "No download" , PSK_S2MSFB_ID );
		} else {

			$r = '<ul>';
			$i = 0;
			foreach ( $result as $row ) {

				$file_path = $row[ 'filepath' ];
				$count     = $row[ 'A' ];
				$dir_name  = substr( $file_path , 0 , strrpos( $file_path , '/' ) + 1 );
				$file_name = substr( $file_path , strrpos( $file_path , '/' ) + 1 );

				if ( $show_count ) {
					switch ( strval( $types ) ) {
						case '0':
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time' , PSK_S2MSFB_ID ) , $count ) : sprintf( __( '%s times' , PSK_S2MSFB_ID ) , $count );
							break;
						case '1':
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time for a day' , PSK_S2MSFB_ID ) , $count ) : sprintf( __( '%s times for a day' , PSK_S2MSFB_ID ) , $count );
							break;
						case '7':
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time for a week' , PSK_S2MSFB_ID ) , $count ) : sprintf( __( '%s times for a week' , PSK_S2MSFB_ID ) , $count );
							break;
						case '31':
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time for a month' , PSK_S2MSFB_ID ) , $count ) : sprintf( __( '%s times for a month' , PSK_S2MSFB_ID ) , $count );
							break;
						case '365':
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time for a year' , PSK_S2MSFB_ID ) , $count ) : sprintf( __( '%s times for a year' , PSK_S2MSFB_ID ) , $count );
							break;
						default:
							$count = ( (int) $count == 1 ) ? sprintf( __( '%s time for %s days' , PSK_S2MSFB_ID ) , $count , $types ) : sprintf( __( '%s times for %s days' , PSK_S2MSFB_ID ) , $count , $types );
							break;
					}
				}

				if ( $filterdir ) {
					if ( ! preg_match( $filterdir , $dir_name ) ) {
						continue;
					}
				}

				if ( $filterfile ) {
					if ( ! preg_match( $filterfile , $file_name ) ) {
						continue;
					}
				}

				switch ( strval( (int) $display_directory_source ) ) {
					case '1':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ];
						break;
					case '2':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '3':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ];
						break;
					case '4':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '5':
						preg_match( "/^\/([^\/]*)\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/' . $matches[ 2 ] . '/&hellip;/' . $matches[ 3 ];
						break;
					case '6':
						$file = $file_path;
						break;
					default:
						$file = $file_name;
						break;
				}

				$replacements = explode( '|' , $names );
				foreach ( $replacements as $replacement ) {
					list( $search , $replace ) = explode( ':' , $replacement );
					$file = str_replace( $search , $replace , $file );
				}

				$show_filepath = false;
				$path          = '';
				if ( ( strval( (int) $display_directory_source ) != $display_directory_source ) && ( (int) $display_directory_source > 0 ) ) {
					$show_filepath = true;
					$path          = substr( $file , 0 , strrpos( $file , '/' ) + 1 );
					$path          = PSK_Tools::mb_html_entities( $path );
					$file          = substr( $file , strrpos( $file , '/' ) + 1 );
				}

				$file = PSK_Tools::mb_html_entities( $file );

				$s2options                        = array();
				$s2options[ 'file_download' ]     = $file_path;
				$s2options[ 'skip_confirmation' ] = ( $show_s2alertbox ) ? 'false' : 'true';
				switch ( $display_files ) {
					case 'a':
						$url  = s2member_file_download_url( $s2options );
						$file = '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'p':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : $file;
						break;
					case 'o':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'l':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? $file : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					default:
						break;
				}

				if ( $file == '' ) {
					continue;
				}

				$r .= '<li style="padding-top:4px;">';
				$r .= ( $display_icon == 'f' ) ? '<img style="float:left;width:16px;height:16px;margin-right:5px;margin-top:2px;border:0;" src="' . PSK_S2MSFB_IMG_URL . PSK_Tools::get_file_icon( $file_path ) . '" />' : '';
				$r .= '<span style="font-size:11px;word-wrap:break-word;font-weight:bold;">';
				$r .= $file . '</span>';
				$r .= ( $show_filepath ) ? '<br/><small>' . sprintf( __( 'in %s' , PSK_S2MSFB_ID ) , $path ) . '</small>' : '';
				$r .= ( $show_count ) ? '<br/><small>' . $count . '</small>' : '';
				$r .= ( $show_hr ) ? '<br/><div style="width:100%;height:5px;border-bottom:1px solid #888;opacity:0.2"/>' : '';
				$r .= '</li>';

				$i ++;
				if ( $i >= $limit ) {
					break;
				}
			}

			$r .= '</ul>';

			if ( $i == 0 ) {
				$r = __( "No download" , PSK_S2MSFB_ID );
			}
		}

		return $r;
	}
}


/**
 * Widget to display latest download or top downloads
 */
class PSK_S2MSFB_wdgt_files extends WP_Widget {

	private static $display_time_options             = array();
	private static $display_files_options            = array();
	private static $display_directory_source_options = array();

	public function __construct() {

		self::$display_time_options             = array(
			''  => __( 'No' , PSK_S2MSFB_ID ) ,
			't' => __( 'Date + time' , PSK_S2MSFB_ID ) ,
			'd' => __( 'Date only' , PSK_S2MSFB_ID ) ,
		);
		self::$display_files_options            = array(
			'a' => __( 'All' , PSK_S2MSFB_ID ) ,
			'l' => __( 'All with reachable links for the current user only' , PSK_S2MSFB_ID ) ,
			'm' => __( 'All without links' , PSK_S2MSFB_ID ) ,
			'o' => __( 'Only downloadable files by current user with links' , PSK_S2MSFB_ID ) ,
			'p' => __( 'Only downloadable files by current user without links' , PSK_S2MSFB_ID ) ,
		);
		self::$display_directory_source_options = array(
			''   => __( 'No' , PSK_S2MSFB_ID ) ,
			'1'  => __( 'In file name : First parent/file' , PSK_S2MSFB_ID ) ,
			'2'  => __( 'In file name : Grand parent/First parent/file' , PSK_S2MSFB_ID ) ,
			'3'  => __( 'In file name : First ancestor/.../file' , PSK_S2MSFB_ID ) ,
			'4'  => __( 'In file name : First ancestor/.../First parent/file' , PSK_S2MSFB_ID ) ,
			'5'  => __( 'In file name : First ancestor/Second ancestor/.../file' , PSK_S2MSFB_ID ) ,
			'6'  => __( 'In file name : Full file path' , PSK_S2MSFB_ID ) ,
			'1n' => __( 'On a new line : First parent/' , PSK_S2MSFB_ID ) ,
			'2n' => __( 'On a new line : Grand parent/First parent/' , PSK_S2MSFB_ID ) ,
			'3n' => __( 'On a new line : First ancestor/.../' , PSK_S2MSFB_ID ) ,
			'4n' => __( 'On a new line : First ancestor/.../First parent/' , PSK_S2MSFB_ID ) ,
			'5n' => __( 'On a new line : First ancestor/Second ancestor/.../' , PSK_S2MSFB_ID ) ,
			'6n' => __( 'On a new line : Full directory path' , PSK_S2MSFB_ID ) ,
		);
		$widget_ops                             = array(
			'classname'   => PSK_S2MSFB_WIDGET_FILES_ID ,
			'description' => __( 'Display new and latest modified available files' , PSK_S2MSFB_ID ) ,
		);

		parent::__construct(
			PSK_S2MSFB_WIDGET_FILES_ID , // Base ID
			PSK_S2MSFB_WIDGET_FILES_NAME , // Name
			$widget_ops
		);

	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args , $instance ) {
		/** @var $before_widget $string */
		/** @var $before_title $string */
		/** @var $after_title $string */
		/** @var $after_widget $string */
		/** @var $widget_id $string */

		extract( $args );

		$title                    = empty( $instance[ 'title' ] ) ? '' : apply_filters( 'widget_title' , $instance[ 'title' ] );
		$limit                    = $instance[ 'limit' ];
		$names                    = $instance[ 'names' ];
		$display_time             = $instance[ 'display_time' ];
		$display_files            = $instance[ 'display_files' ];
		$display_directory_source = $instance[ 'display_directory_source' ];
		$filterfile               = $instance[ 'filterfile' ];
		$filterdir                = $instance[ 'filterdir' ];
		$display_file_icons       = ( $instance[ 'display_file_icons' ] == '1' ) ? true : false;
		$show_modified_files      = ( $instance[ 'show_modified_files' ] == '1' ) ? true : false;
		$show_hr                  = ( $instance[ 'show_hr' ] == '1' ) ? true : false;
		$show_s2alertbox          = ( $instance[ 'show_s2alertbox' ] == '1' ) ? true : false;

		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo ( $show_hr ) ? '<div style="width:100%;height:3px;border-bottom:3px solid #888;opacity:0.2"></div>' : '';
		echo self::get_latest_files( $widget_id , $limit , $names , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $display_file_icons , $show_modified_files , $show_hr , $show_s2alertbox );
		echo $after_widget;
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance , $old_instance ) {
		$instance                          = $old_instance;
		$instance[ 'show_hr' ]             = '';
		$instance[ 'show_s2alertbox' ]     = '';
		$instance[ 'display_file_icons' ]  = '';
		$instance[ 'show_modified_files' ] = '';
		foreach ( $new_instance as $key => $value ) {
			$instance[ $key ] = strip_tags( $value );
		}

		return $instance;
	}


	/**
	 * Back-end widget form
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		//  Assigns values
		$instance = wp_parse_args( (array) $instance , array(
			'title'                    => 'Latest Files' ,
			'limit'                    => '10' ,
			'names'                    => 'access-s2member-level0:Free|access-s2member-level1:Bronze|access-s2member-level2:Silver|access-s2member-level3:Gold|access-s2member-level4:Platinum|access-s2member-ccap-videos:Videos' ,
			'display_time'             => 'd' ,
			'display_files'            => 'm' ,
			'display_directory_source' => '' ,
			'filterfile'               => '/\\.(png|jpe?g|gif|zip|mp3)$/i' ,
			'filterdir'                => '/^\/access-s2member-level0\/$/' ,
			'display_file_icons'       => '1' ,
			'show_modified_files'      => '1' ,
			'show_hr'                  => '1' ,
			'show_s2alertbox'          => '' ,
		) );

		$title                    = strip_tags( $instance[ 'title' ] );
		$limit                    = strip_tags( $instance[ 'limit' ] );
		$names                    = strip_tags( $instance[ 'names' ] );
		$display_time             = strip_tags( $instance[ 'display_time' ] );
		$display_files            = strip_tags( $instance[ 'display_files' ] );
		$display_directory_source = strip_tags( $instance[ 'display_directory_source' ] );
		$filterfile               = strip_tags( $instance[ 'filterfile' ] );
		$filterdir                = strip_tags( $instance[ 'filterdir' ] );
		$display_file_icons       = strip_tags( $instance[ 'display_file_icons' ] );
		$show_modified_files      = strip_tags( $instance[ 'show_modified_files' ] );
		$show_hr                  = strip_tags( $instance[ 'show_hr' ] );
		$show_s2alertbox          = strip_tags( $instance[ 'show_s2alertbox' ] );


		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">' . __( 'Title' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" ';
		echo 'name="' . $this->get_field_name( 'title' ) . '" type="text" ';
		echo 'value="' . esc_attr( $title ) . '" title="' . __( 'Title of the widget as it appears on the page' , PSK_S2MSFB_ID ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'limit' ) . '">' . __( 'Number of items to show' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'limit' ) . '" ';
		echo 'name="' . $this->get_field_name( 'limit' ) . '" type="number" ';
		echo 'value="' . esc_attr( $limit ) . '" title="" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_time' ) . '">' . __( 'Display time' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_time' ) . '" name="' . $this->get_field_name( 'display_time' ) . '">';
		foreach ( self::$display_time_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_time , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_files' ) . '">' . __( 'Display files' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_files' ) . '" name="' . $this->get_field_name( 'display_files' ) . '">';
		foreach ( self::$display_files_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_files , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_directory_source' ) . '">' . __( 'Display file path' , PSK_S2MSFB_ID );
		echo '<select class="widefat" id="' . $this->get_field_id( 'display_directory_source' ) . '" name="' . $this->get_field_name( 'display_directory_source' ) . '">';
		foreach ( self::$display_directory_source_options as $key => $value ) {
			echo '<option value="' . $key . '"' . selected( $display_directory_source , $key , false ) . '>' . $value . '</option>';
		}
		echo '</select>';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'names' ) . '">' . __( 'Directory name replacements' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'names' ) . '" ';
		echo 'name="' . $this->get_field_name( 'names' ) . '" type="text" ';
		echo 'value="' . esc_attr( $names ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'filterfile' ) . '">' . __( 'Filter filename' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'filterfile' ) . '" ';
		echo 'name="' . $this->get_field_name( 'filterfile' ) . '" type="text" ';
		echo 'value="' . esc_attr( $filterfile ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'filterdir' ) . '">' . __( 'Filter directories name' , PSK_S2MSFB_ID );
		echo '<input class="widefat" id="' . $this->get_field_id( 'filterdir' ) . '" ';
		echo 'name="' . $this->get_field_name( 'filterdir' ) . '" type="text" ';
		echo 'value="' . esc_attr( $filterdir ) . '" />';
		echo '</label></p>';

		echo '<p><label for="' . $this->get_field_id( 'display_file_icons' ) . '">' . __( 'Show file icons' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'display_file_icons' ) . '" value="1" ' . checked( $display_file_icons , '1' , false ) . '/>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_modified_files' ) . '">' . __( 'Show modified files' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_modified_files' ) . '" value="1" ' . checked( $show_modified_files , '1' , false ) . '/>';
		echo '<br/><small>' . __( 'Uncheck to show only new files' , PSK_S2MSFB_ID ) . '</small>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_hr' ) . '">' . __( 'Show separators' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_hr' ) . '" value="1" ' . checked( $show_hr , '1' , false ) . '/>';
		echo '</p>';

		echo '<p><label for="' . $this->get_field_id( 'show_s2alertbox' ) . '">' . __( 'Show s2member confirmation box' , PSK_S2MSFB_ID ) . ' </label>';
		echo '<input type="checkbox" name="' . $this->get_field_name( 'show_s2alertbox' ) . '" value="1" ' . checked( $show_s2alertbox , '1' , false ) . '/>';
		echo '</p>';

	}


	/**
	 * Returns the latest files
	 *
	 * @param string $widget_id                the widget instance
	 * @param string $limit                    preference
	 * @param string $names                    preference
	 * @param string $display_time             preference
	 * @param string $display_files            preference
	 * @param string $display_directory_source preference
	 * @param string $filterfile               preference
	 * @param string $filterdir                preference
	 * @param string $display_file_icons       preference
	 * @param string $show_modified_files      preference
	 * @param string $show_hr                  preference
	 * @param string $show_s2alertbox          preference
	 *
	 * @return string HTML
	 */
	private function get_latest_files(
		/** @noinspection PhpUnusedParameterInspection */
		$widget_id , $limit , $names , $display_time , $display_files , $display_directory_source , $filterfile , $filterdir , $display_file_icons , $show_modified_files , $show_hr , $show_s2alertbox
	) {

		$r     = '';
		$limit = ( (int) $limit > 0 ) ? (int) $limit : 10;

		delete_transient( PSK_S2MSFB_WIDGET_FILES_LATEST_ID );
		if ( false === ( $result = get_transient( PSK_S2MSFB_WIDGET_FILES_LATEST_ID ) ) ) {

			/** @var $wpdb WPDB */
			global $wpdb;
			$tablename = $wpdb->prefix . PSK_S2MSFB_DB_FILES_TABLE_NAME;
			$sql       = "SELECT filepath, filemodificationdate, lastdate FROM $tablename ORDER BY lastdate DESC LIMIT 0, 100";
			$result    = $wpdb->get_results( $sql , ARRAY_A );
			set_transient( PSK_S2MSFB_WIDGET_FILES_LATEST_ID , $result );
		}

		if ( count( $result ) == 0 ) {
			$r .= __( "No files" , PSK_S2MSFB_ID );
		} else {
			$r          = '<ul>';
			$i          = 0;
			$gmt_offset = get_option( 'gmt_offset' ) * 3600;

			foreach ( $result as $row ) {

				$modified = $row[ 'filemodificationdate' ];
				if ( ( false == $show_modified_files ) && ( 0 != (int) $modified ) ) {
					continue;
				}

				$file_path = $row[ 'filepath' ];
				$created   = strtotime( $row[ 'lastdate' ] ) + $gmt_offset;
				$dir_name  = mb_substr( $file_path , 0 , mb_strrpos( $file_path , '/' ) + 1 );
				$file_name = mb_substr( $file_path , mb_strrpos( $file_path , '/' ) + 1 );

				if ( $filterdir ) {
					if ( ! preg_match( $filterdir , $dir_name ) ) {
						continue;
					}
				}

				if ( $filterfile ) {
					if ( ! preg_match( $filterfile , $file_name ) ) {
						continue;
					}
				}

				switch ( $display_time ) {
					case 't':
						$time = date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , $created );
						break;
					case 'd':
						$time = date_i18n( sprintf( '%1$s' , get_option( 'date_format' ) ) , $created );
						break;
					default:
						$time = '';
						break;
				}

				if ( $time != '' ) {
					if ( 0 == (int) $modified ) {
						$time = sprintf( __( 'Added on %s' , PSK_S2MSFB_ID ) , $time );
					} else {
						$time = sprintf( __( 'Modified on %s' , PSK_S2MSFB_ID ) , $time );
					}
					$time = '<br/><small>' . $time . '</small>';
				}

				switch ( strval( (int) $display_directory_source ) ) {
					case '1':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ];
						break;
					case '2':
						preg_match( "/^.*\/([^\/]*)\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : $matches[ 1 ] . '/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '3':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ];
						break;
					case '4':
						preg_match( "/^\/([^\/]*)\/.*\/([^\/]*)\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/&hellip;/' . $matches[ 2 ] . '/' . $matches[ 3 ];
						break;
					case '5':
						preg_match( "/^\/([^\/]*)\/([^\/]*)\/.*\/([^\/]*)$/i" , $file_path , $matches );
						$file = ( $matches[ 1 ] == '' ) ? $file_path : '/' . $matches[ 1 ] . '/' . $matches[ 2 ] . '/&hellip;/' . $matches[ 3 ];
						break;
					case '6':
						$file = $file_path;
						break;
					default:
						$file = $file_name;
						break;
				}

				$replacements = explode( '|' , $names );
				foreach ( $replacements as $replacement ) {
					list( $search , $replace ) = explode( ':' , $replacement );
					$file = str_replace( $search , $replace , $file );
				}

				$show_filepath = false;
				$path          = '';
				if ( ( strval( (int) $display_directory_source ) != $display_directory_source ) && ( (int) $display_directory_source > 0 ) ) {
					$show_filepath = true;
					$path          = mb_substr( $file , 0 , mb_strrpos( $file , '/' ) + 1 );
					$path          = PSK_Tools::mb_html_entities( $path );
					$file          = mb_substr( $file , mb_strrpos( $file , '/' ) + 1 );
				}

				$file                             = PSK_Tools::mb_html_entities( $file );
				$s2options                        = array();
				$s2options[ 'file_download' ]     = $file_path;
				$s2options[ 'skip_confirmation' ] = ( $show_s2alertbox ) ? 'false' : 'true';
				switch ( $display_files ) {
					case 'a':
						$url  = s2member_file_download_url( $s2options );
						$file = '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'p':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : $file;
						break;
					case 'o':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? '' : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					case 'l':
						$s2options[ 'check_user' ] = true;
						$url                       = s2member_file_download_url( $s2options );
						$file                      = ( $url === false ) ? $file : '<a href="' . PSK_Tools::rel_literal( $url ) . '">' . $file . '</a>';
						break;
					default:
						break;
				}

				if ( $file == '' ) {
					continue;
				}

				$r .= '<li style="padding-top:4px;">';
				$r .= ( $display_file_icons ) ? '<img style="float:left;width:16px;height:16px;margin-right:5px;margin-top:2px;border:0;" src="' . PSK_S2MSFB_IMG_URL . PSK_Tools::get_file_icon( $file_path ) . '" />' : '';
				$r .= '<span style="font-size:11px;word-wrap:break-word;font-weight:bold;">';
				$r .= $file . '</span>';
				$r .= ( $show_filepath ) ? '<br/><small>' . sprintf( __( 'in %s' , PSK_S2MSFB_ID ) , $path ) . '</small>' : '';
				$r .= $time;
				$r .= ( $show_hr ) ? '<br/><div style="width:100%;height:5px;border-bottom:1px solid #888;opacity:0.2"/>' : '';
				$r .= '</li>';

				$i ++;
				if ( $i >= $limit ) {
					break;
				}
			}

			$r .= '</ul>';

			if ( $i == 0 ) {
				$r = __( "No files" , PSK_S2MSFB_ID );
			}
		}

		return $r;
	}


}
