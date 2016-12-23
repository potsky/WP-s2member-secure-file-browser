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
 * Class PSK_S2MSFBAdminStats
 */
class PSK_S2MSFBAdminStats {
	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'init_assets' ) );
		add_action( 'admin_init' , array( __CLASS__ , 'admin_init' ) );
	}


	/**
	 * Initialization
	 * @wp_action    init
	 */
	public static function admin_init() {
	}


	/**
	 * Load javascript and css for Public and Admin part
	 * @wp_action    admin_enqueue_scripts
	 * @wp_action    wp_enqueue_scripts
	 */
	public static function init_assets() {
		wp_register_script( 'jquery.tablesorter' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.min.js' , array( 'jquery' ) , false , true );
		wp_enqueue_script( 'jquery.tablesorter' );
		wp_enqueue_script( 'jquery.tablesorter.widgets' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.widgets.min.js' , array(
			'jquery' ,
			'jquery.tablesorter' ,
		) , false , true );
		wp_enqueue_script( 'jquery.tablesorter.pager' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.pager.' . PSK_S2MSFB_EXT_JS , array(
			'jquery' ,
			'jquery.tablesorter' ,
		) , false , true );
		wp_enqueue_style( 'jquery.tablesorter.pager' , PSK_S2MSFB_CSS_URL . 'jquery.tablesorter.pager.' . PSK_S2MSFB_EXT_CSS );
		wp_enqueue_style( 'theme.bootstrap' , PSK_S2MSFB_CSS_URL . 'theme.bootstrap.' . PSK_S2MSFB_EXT_CSS );

		wp_enqueue_script( PSK_S2MSFB_ID . '.admin.stats' , PSK_S2MSFB_JS_URL . 'admin.stats.' . PSK_S2MSFB_EXT_JS , array(
			'jquery' ,
			'jquery.tablesorter' ,
		) , false , true );
		wp_localize_script( PSK_S2MSFB_ID . '.admin.stats' , 'objectL10n' , array(
			'erroroccurs' => __( 'An error occurs' , PSK_S2MSFB_ID ) ,
			'error'       => _x( 'Error!' , 'alertbox' , PSK_S2MSFB_ID ) ,
			'success'     => _x( 'Success!' , 'alertbox' , PSK_S2MSFB_ID ) ,
			'info'        => _x( 'Info!' , 'alertbox' , PSK_S2MSFB_ID ) ,
			'warning'     => _x( 'Warning!' , 'alertbox' , PSK_S2MSFB_ID ) ,
		) );
	}


	/**
	 * Download XML file
	 *
	 * @param sql
	 *
	 * @return      void
	 */
	public static function admin_download_xml_file( $sql ) {
	}


	/**
	 * Admin Screen : Stats > All downloads
	 *
	 * @return      void
	 */
	public static function admin_screen_stats_all() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		/** @var $wpdb WPDB */
		global $wpdb;

		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$where     = ( isset( $_GET[ 't' ] ) ) ? ' WHERE created > NOW() - INTERVAL ' . (int) $_GET[ 't' ] . ' DAY' : '';

		$sql   = "SELECT COUNT(DISTINCT userid) FROM $tablename$where";
		$duser = $wpdb->get_row( $sql , ARRAY_N );
		$duser = $duser[ 0 ];

		$sql   = "SELECT COUNT(DISTINCT filepath) FROM $tablename$where";
		$dfile = $wpdb->get_row( $sql , ARRAY_N );
		$dfile = $dfile[ 0 ];

		$sql    = "SELECT userid,useremail,ip,UNIX_TIMESTAMP(created),filepath FROM $tablename$where ORDER BY created DESC";
		$result = $wpdb->get_results( $sql , ARRAY_A );
		$cresul = count( $result );

		$link = '?page=' . $_GET[ 'page' ];

		if ( $cresul == 0 ) {
			echo '<div class="alert alert-error">' . __( "No download" , PSK_S2MSFB_ID ) . '</div>';
		}

		if ( ( $cresul != 0 ) || ( $where != '' ) ) {
			echo '<div class="btn-group">';
			echo '    <button class="btn btn-primary btn-mini dropdown-toggle" data-toggle="dropdown">' . __( 'Display' , 'PSK_S2MSFB_ID' ) . ' <span class="caret"></span></button>';
			echo '    <ul class="dropdown-menu">';
			echo '    	<li><a href="' . $link . '">' . __( 'all records' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li class="divider"></li>';
			echo '    	<li><a href="' . $link . '&t=1">' . __( 'one day' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=7">' . __( 'one week' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=31">' . __( 'one month' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=365">' . __( 'one year' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    </ul>';
			echo '</div>';
		}

		if ( $cresul > 0 ) {

			foreach ( get_users() as $user ) {
				$users[ $user->ID ] = $user->display_name;
			}

			echo '&nbsp;&nbsp;&nbsp;';

			$zd = ( isset( $_GET[ 't' ] ) ) ? $_GET[ 't' ] : '';

			switch ( $zd ) {
				case '1':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for 24 hours' , PSK_S2MSFB_ID ) , $dfile , $cresul , $duser );
					break;
				case '7':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one week' , PSK_S2MSFB_ID ) , $dfile , $cresul , $duser );
					break;
				case '31':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one month' , PSK_S2MSFB_ID ) , $dfile , $cresul , $duser );
					break;
				case '365':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one year' , PSK_S2MSFB_ID ) , $dfile , $cresul , $duser );
					break;
				default:
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s)' , PSK_S2MSFB_ID ) , $dfile , $cresul , $duser );
					break;
			}
			echo '<br/>';

			echo '<table class="table sort table-bordered table-hover table-condensed">';
			echo '<thead><tr>';
			echo '  <th>' . __( 'When' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th>' . __( 'File' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th class="filter-select filter-exact" data-placeholder="Select user">' . __( 'User' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th>' . __( 'IP Address' , PSK_S2MSFB_ID ) . '</th>';
			echo '</tr></thead>';
			echo '<tbody>';

			foreach ( $result as $row ) {

				$time = (int) $row[ 'UNIX_TIMESTAMP(created)' ];
				$time += get_option( 'gmt_offset' ) * 3600;
				$dt = date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , $time );

				if ( isset( $users[ $row[ 'userid' ] ] ) ) {
					$user      = '<a href="' . admin_url( 'user-edit.php?user_id=' . $row[ 'userid' ] ) . '">' . $users[ $row[ 'userid' ] ] . '</a>';
					$userclass = '';
				} else {
					$user      = $row[ 'useremail' ] . ' - #' . $row[ 'userid' ];
					$userclass = ' class="deleted"';
				}

				echo '<tr>';
				echo '  <td data-t="' . $time . '">' . $dt . '</td>';
				echo '  <td>' . PSK_Tools::mb_html_entities( $row[ 'filepath' ] ) . '</td>';
				echo '  <td' . $userclass . '>' . $user . '</td>';
				echo '  <td>' . $row[ 'ip' ] . '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '<tfoot>';
			echo '  <tr>';
			echo '    <th>' . __( 'When' , PSK_S2MSFB_ID ) . '</th>';
			echo '    <th>' . __( 'File' , PSK_S2MSFB_ID ) . '</th>';
			echo '    <th>' . __( 'User' , PSK_S2MSFB_ID ) . '</th>';
			echo '    <th>' . __( 'IP Address' , PSK_S2MSFB_ID ) . '</th>';
			echo '  </tr>';
			echo '  <tr><th colspan="4" class="pager form-horizontal">';
			echo '    <button class="reset btn btn-mini btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh"></i> Reset filters</button>';
			echo '    <div class="pull-right">';
			echo '      <button class="btn btn-mini first"><i class="icon-step-backward"></i></button>';
			echo '      <button class="btn btn-mini prev"><i class="icon-arrow-left"></i></button>';
			echo '      <span class="pagedisplay"></span> <!-- this can be any element, including an input -->';
			echo '      <button class="btn btn-mini next"><i class="icon-arrow-right"></i></button>';
			echo '      <button class="btn btn-mini last"><i class="icon-step-forward"></i></button>';
			echo '      <select class="pagesize" title="Select page size">';
			echo '      	<option selected="selected" value="10">10</option>';
			echo '      	<option value="20">20</option>';
			echo '      	<option value="50">50</option>';
			echo '      	<option value="100">100</option>';
			echo '      </select>';
			echo '      <select class="pagenum input-mini" title="Select page number"></select>';
			echo '    </div>';
			echo '  </th></tr>';
			echo '</tfoot>';
			echo '</table>';
		}

		if ( ( $cresul != 0 ) || ( $where != '' ) ) {
			echo '<hr/>';
			echo '<a class="btn" href="?psk_s2msfb_download=psk_s2msfb_stats_all_xml&n=0"><span class="icon-tasks"></span> ' . __( 'Export all data as XML' , 'PSK_S2MSFB_ID' ) . '</a>';
			echo '&nbsp;<a class="btn" href="?psk_s2msfb_download=psk_s2msfb_stats_all_csv&n=0' . $_SERVER[ 'QUERY_STRING' ] . '&e=c"><span class="icon-th"></span> ' . __( 'Export all data as Excel CSV' , 'PSK_S2MSFB_ID' ) . '</a>';
		}

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Stats > Top files
	 *
	 * @return      void
	 */
	public static function admin_screen_stats_fil() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		/** @var $wpdb WPDB */
		global $wpdb;

		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$where     = ( isset( $_GET[ 't' ] ) ) ? ' WHERE created > NOW() - INTERVAL ' . (int) $_GET[ 't' ] . ' DAY' : '';

		$sql   = "SELECT COUNT(DISTINCT userid) FROM $tablename$where";
		$duser = $wpdb->get_row( $sql , ARRAY_N );
		$duser = $duser[ 0 ];

		$sql   = "SELECT COUNT(DISTINCT filepath) FROM $tablename$where";
		$dfile = $wpdb->get_row( $sql , ARRAY_N );
		$dfile = $dfile[ 0 ];

		$sql     = "SELECT COUNT(*) FROM $tablename$where";
		$cresult = $wpdb->get_row( $sql , ARRAY_N );
		$cresult = $cresult[ 0 ];

		$sql    = "SELECT filepath, COUNT(*) A FROM $tablename$where GROUP BY filepath ORDER BY A DESC";
		$result = $wpdb->get_results( $sql , ARRAY_A );

		$total = 0;
		$link  = '?page=' . $_GET[ 'page' ];

		if ( count( $result ) == 0 ) {
			echo '<div class="alert alert-error">' . __( "No download" , PSK_S2MSFB_ID ) . '</div>';
		}

		if ( ( (int) $cresult != 0 ) || ( $where != '' ) ) {
			echo '<div class="btn-group">';
			echo '    <button class="btn btn-primary btn-mini dropdown-toggle" data-toggle="dropdown">' . __( 'Display' , 'PSK_S2MSFB_ID' ) . ' <span class="caret"></span></button>';
			echo '    <ul class="dropdown-menu">';
			echo '    	<li><a href="' . $link . '">' . __( 'all records' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li class="divider"></li>';
			echo '    	<li><a href="' . $link . '&t=1">' . __( 'one day' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=7">' . __( 'one week' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=31">' . __( 'one month' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=365">' . __( 'one year' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    </ul>';
			echo '</div>';
		}

		if ( count( $result ) > 0 ) {

			echo '&nbsp;&nbsp;&nbsp;';

			$zd = ( isset( $_GET[ 't' ] ) ) ? $_GET[ 't' ] : '';

			switch ( $zd ) {
				case '1':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for 24 hours' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '7':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one week' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '31':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one month' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '365':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one year' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				default:
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s)' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
			}
			echo '<br/>';

			echo '<table class="table sortn table-bordered table-hover table-condensed">';
			echo '<thead><tr>';
			echo '  <th>' . __( 'File' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th>' . __( 'Count' , PSK_S2MSFB_ID ) . '</th>';
			echo '</tr></thead>';
			echo '<tbody>';

			foreach ( $result as $row ) {
				echo '<tr>';
				echo '  <td>' . PSK_Tools::mb_html_entities( $row[ 'filepath' ] ) . '</td>';
				echo '  <td>' . $row[ 'A' ] . '</td>';
				echo '</tr>';
				$total += (int) $row[ 'A' ];
			}

			echo '</tbody>';
			echo '<tfoot>';
			echo '  <tr>';
			echo '    <th> </th>';
			echo '    <th>' . sprintf( __( 'Total : %s' , PSK_S2MSFB_ID ) , $total ) . '</th>';
			echo '  </tr>';
			echo '  <tr><th colspan="2" class="pager form-horizontal">';
			echo '    <button class="reset btn btn-mini btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh"></i> Reset filters</button>';
			echo '    <div class="pull-right">';
			echo '      <button class="btn btn-mini first"><i class="icon-step-backward"></i></button>';
			echo '      <button class="btn btn-mini prev"><i class="icon-arrow-left"></i></button>';
			echo '      <span class="pagedisplay"></span> <!-- this can be any element, including an input -->';
			echo '      <button class="btn btn-mini next"><i class="icon-arrow-right"></i></button>';
			echo '      <button class="btn btn-mini last"><i class="icon-step-forward"></i></button>';
			echo '      <select class="pagesize" title="Select page size">';
			echo '      	<option selected="selected" value="10">10</option>';
			echo '      	<option value="20">20</option>';
			echo '      	<option value="50">50</option>';
			echo '      	<option value="100">100</option>';
			echo '      </select>';
			echo '      <select class="pagenum input-mini" title="Select page number"></select>';
			echo '    </div>';
			echo '  </th></tr>';
			echo '</tfoot>';
			echo '</table>';
		}

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Stats > Top downloaders
	 *
	 * @return      void
	 */
	public static function admin_screen_stats_use() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		/** @var $wpdb WPDB */
		global $wpdb;

		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$where     = ( isset( $_GET[ 't' ] ) ) ? ' WHERE created > NOW() - INTERVAL ' . (int) $_GET[ 't' ] . ' DAY' : '';

		$sql   = "SELECT COUNT(DISTINCT userid) FROM $tablename$where";
		$duser = $wpdb->get_row( $sql , ARRAY_N );
		$duser = $duser[ 0 ];

		$sql   = "SELECT COUNT(DISTINCT filepath) FROM $tablename$where";
		$dfile = $wpdb->get_row( $sql , ARRAY_N );
		$dfile = $dfile[ 0 ];

		$sql     = "SELECT COUNT(*) FROM $tablename$where";
		$cresult = $wpdb->get_row( $sql , ARRAY_N );
		$cresult = $cresult[ 0 ];

		$sql    = "SELECT userid, COUNT(*) A FROM $tablename$where GROUP BY userid ORDER BY A DESC";
		$result = $wpdb->get_results( $sql , ARRAY_A );

		$total = 0;
		$link  = '?page=' . $_GET[ 'page' ];

		if ( count( $result ) == 0 ) {
			echo '<div class="alert alert-error">' . __( "No download" , PSK_S2MSFB_ID ) . '</div>';
		}

		if ( ( (int) $cresult != 0 ) || ( $where != '' ) ) {
			echo '<div class="btn-group">';
			echo '    <button class="btn btn-primary btn-mini dropdown-toggle" data-toggle="dropdown">' . __( 'Display' , 'PSK_S2MSFB_ID' ) . ' <span class="caret"></span></button>';
			echo '    <ul class="dropdown-menu">';
			echo '    	<li><a href="' . $link . '">' . __( 'all records' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li class="divider"></li>';
			echo '    	<li><a href="' . $link . '&t=1">' . __( 'one day' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=7">' . __( 'one week' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=31">' . __( 'one month' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    	<li><a href="' . $link . '&t=365">' . __( 'one year' , PSK_S2MSFB_ID ) . '</a></li>';
			echo '    </ul>';
			echo '</div>';
		}

		if ( count( $result ) > 0 ) {

			foreach ( get_users() as $user ) {
				/** @var $users WP_User */
				$users[ $user->ID ] = $user->display_name;
			}

			echo '&nbsp;&nbsp;&nbsp;';

			$zd = ( isset( $_GET[ 't' ] ) ) ? $_GET[ 't' ] : '';

			switch ( $zd ) {
				case '1':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for 24 hours' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '7':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one week' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '31':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one month' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				case '365':
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s) for one year' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
				default:
					echo sprintf( __( '%1$s distinct file(s) downloaded %2$s time(s) by %3$s distinct user(s)' , PSK_S2MSFB_ID ) , $dfile , $cresult , $duser );
					break;
			}
			echo '<br/>';

			echo '<table class="table sortn table-bordered table-hover table-condensed">';
			echo '<thead><tr>';
			echo '  <th>' . __( 'User' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th>' . __( 'Count' , PSK_S2MSFB_ID ) . '</th>';
			echo '</tr></thead>';
			echo '<tbody>';

			foreach ( $result as $row ) {
				if ( isset( $users[ $row[ 'userid' ] ] ) ) {
					$user      = '<a href="' . admin_url( 'user-edit.php?user_id=' . $row[ 'userid' ] ) . '">' . $users[ $row[ 'userid' ] ] . '</a>';
					$userclass = '';
				} else {
					$user      = ( isset( $row[ 'useremail' ] ) ) ? $row[ 'useremail' ] : '';
					$user      = $user . ' - #' . $row[ 'userid' ];
					$userclass = ' class="deleted"';
				}
				echo '<tr>';
				echo '  <td' . $userclass . '>' . $user . '</td>';
				echo '  <td>' . $row[ 'A' ] . '</td>';
				echo '</tr>';
				$total += (int) $row[ 'A' ];
			}

			echo '</tbody>';
			echo '<tfoot>';
			echo '  <tr>';
			echo '    <th> </th>';
			echo '    <th>' . sprintf( __( 'Total : %s' , PSK_S2MSFB_ID ) , $total ) . '</th>';
			echo '  </tr>';
			echo '  <tr><th colspan="2" class="pager form-horizontal">';
			echo '    <button class="reset btn btn-mini btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh"></i> Reset filters</button>';
			echo '    <div class="pull-right">';
			echo '      <button class="btn btn-mini first"><i class="icon-step-backward"></i></button>';
			echo '      <button class="btn btn-mini prev"><i class="icon-arrow-left"></i></button>';
			echo '      <span class="pagedisplay"></span> <!-- this can be any element, including an input -->';
			echo '      <button class="btn btn-mini next"><i class="icon-arrow-right"></i></button>';
			echo '      <button class="btn btn-mini last"><i class="icon-step-forward"></i></button>';
			echo '      <select class="pagesize" title="Select page size">';
			echo '      	<option selected="selected" value="10">10</option>';
			echo '      	<option value="20">20</option>';
			echo '      	<option value="50">50</option>';
			echo '      	<option value="100">100</option>';
			echo '      </select>';
			echo '      <select class="pagenum input-mini" title="Select page number"></select>';
			echo '    </div>';
			echo '  </th></tr>';
			echo '</tfoot>';
			echo '</table>';
		}

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Stats > s2member current logs
	 *
	 * @return      void
	 */
	public
	static function admin_screen_stats_log() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		$s2list       = array( 'log' ); // log and/or arc
		$down[ 'ti' ] = array();
		$down[ 'ui' ] = array();
		$down[ 'fn' ] = array();
		$users        = array();

		foreach ( get_users() as $user ) {
			$user = new WP_User( $user->ID );
			/** @noinspection PhpParamsInspection */
			$user_downloads = c_ws_plugin__s2member_files::user_downloads( $user );
			foreach ( $s2list as $type ) {
				if ( isset( $user_downloads[ $type ] ) ) {
					foreach ( $user_downloads[ $type ] as $dl ) {
						$down[ 'ui' ][] = $user->ID;
						$down[ 'fn' ][] = $dl[ 'file' ];
						$down[ 'ti' ][] = $dl[ 'time' ];
					}
				}
			}
		}

		if ( count( $down[ 'ti' ] ) == 0 ) {
			echo '<div class="alert alert-error">' . __( "No current download" , PSK_S2MSFB_ID ) . '</div>';
		} else {

			foreach ( get_users() as $user ) {
				$users[ $user->ID ] = $user->display_name;
			}

			array_multisort( $down[ 'ti' ] , SORT_DESC , $down[ 'ui' ] , $down[ 'fn' ] );

			echo '<table class="table sort table-bordered table-hover table-condensed">';
			echo '<thead><tr>';
			echo '  <th>' . __( 'When' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th>' . __( 'What' , PSK_S2MSFB_ID ) . '</th>';
			echo '  <th class="filter-select filter-exact" data-placeholder="Select user">' . __( 'Who' , PSK_S2MSFB_ID ) . '</th>';
			echo '</tr></thead>';
			echo '<tfoot>';
			echo '  <tr>';
			echo '    <th>' . __( 'When' , PSK_S2MSFB_ID ) . '</th>';
			echo '    <th>' . __( 'What' , PSK_S2MSFB_ID ) . '</th>';
			echo '    <th>' . __( 'Who' , PSK_S2MSFB_ID ) . '</th>';
			echo '  </tr>';
			echo '  <tr><th colspan="3" class="pager form-horizontal">';
			echo '    <button class="reset btn btn-mini btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh"></i> Reset filters</button>';
			echo '    <div class="pull-right">';
			echo '      <button class="btn btn-mini first"><i class="icon-step-backward"></i></button>';
			echo '      <button class="btn btn-mini prev"><i class="icon-arrow-left"></i></button>';
			echo '      <span class="pagedisplay"></span> <!-- this can be any element, including an input -->';
			echo '      <button class="btn btn-mini next"><i class="icon-arrow-right"></i></button>';
			echo '      <button class="btn btn-mini last"><i class="icon-step-forward"></i></button>';
			echo '      <select class="pagesize" title="Select page size">';
			echo '      	<option selected="selected" value="10">10</option>';
			echo '      	<option value="20">20</option>';
			echo '      	<option value="50">50</option>';
			echo '      	<option value="100">100</option>';
			echo '      </select>';
			echo '      <select class="pagenum input-mini" title="Select page number"></select>';
			echo '    </div>';
			echo '  </th></tr>';
			echo '</tfoot>';
			echo '<tbody>';
			foreach ( $down[ 'ti' ] as $key => $time ) {
				$dt = date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , (int) $time );
				$du = $users[ $down[ 'ui' ][ $key ] ];
				echo '<tr>';
				echo '  <td data-t="' . $time . '">' . $dt . '</td>';
				echo '  <td>' . $down[ 'fn' ][ $key ] . '</td>';
				echo '  <td>' . $du . '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


}

PSK_S2MSFBAdminStats::init();

