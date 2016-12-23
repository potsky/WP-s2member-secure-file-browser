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
 * Class PSK_S2MSFBAdminSettings
 */
class PSK_S2MSFBAdminSettings {
	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'init_assets' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
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
		wp_enqueue_script( PSK_S2MSFB_ID . '.admin.settings', PSK_S2MSFB_JS_URL . 'admin.settings.' . PSK_S2MSFB_EXT_JS , array( 'jquery', 'jquery.tablesorter' ), false, true );
		wp_localize_script( PSK_S2MSFB_ID . '.admin.settings', 'objectL10n', array(
			'erroroccurs' => __( 'An error occurs', PSK_S2MSFB_ID ),
			'error'       => _x( 'Error!', 'alertbox', PSK_S2MSFB_ID ),
			'success'     => _x( 'Success!', 'alertbox', PSK_S2MSFB_ID ),
			'info'        => _x( 'Info!', 'alertbox', PSK_S2MSFB_ID ),
			'warning'     => _x( 'Warning!', 'alertbox', PSK_S2MSFB_ID ),
		) );
	}

	/**
	 * Admin Screen : Stats > General
	 *
	 * @return      void
	 */
	public static function admin_screen_settings_main() {

		$retention_days = array(
			0   => __( 'Do not delete', PSK_S2MSFB_ID ),
			7   => __( 'Keep 1 week', PSK_S2MSFB_ID ),
			31  => __( 'Keep 1 month', PSK_S2MSFB_ID ),
			93  => __( 'Keep 3 months', PSK_S2MSFB_ID ),
			186 => __( 'Keep 6 months', PSK_S2MSFB_ID ),
			365 => __( 'Keep 1 year', PSK_S2MSFB_ID ),
			730 => __( 'Keep 2 years', PSK_S2MSFB_ID ),
		);

		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		$settings   = get_option( PSK_S2MSFB_OPT_SETTINGS_GENERAL );
		$maxcount   = (int) $settings['maxcount'];
		$retention  = (int) $settings['retention'];
		$capstats   = ( @$settings['capstats'] == '' ) ? PSK_S2MSFB_ADMIN_SETTINGS_ACCESS : @$settings['capstats'];
		$capmanager = ( @$settings['capmanager'] == '' ) ? PSK_S2MSFB_ADMIN_SETTINGS_ACCESS : @$settings['capmanager'];

		if ( isset( $_GET['action'] ) ) {
			switch ( $_GET['action'] ) {
				case 'deletedwnl':
					PSK_S2MSFB::db_uninstall_download();
					PSK_S2MSFB::db_install_download();
					echo PSK_Tools::get_js_alert( __( 'Success!', PSK_S2MSFB_ID ), __( 'Download records deleted', PSK_S2MSFB_ID ), 'success' );
					break;
				default:
					break;
			}
		}

		if ( isset( $_POST['action'] ) ) {

			check_admin_referer( __CLASS__ . __METHOD__ );

			$maxcount   = (int) $_POST['maxcount'];
			$retention  = (int) $_POST['retention'];
			$capstats   = $_POST['capstats'];
			$capmanager = $_POST['capmanager'];

			switch ( $_POST['action'] ) {

				case 'update':
					$form_is_valid = true;
					if ( $form_is_valid === true ) {
						update_option( PSK_S2MSFB_OPT_SETTINGS_GENERAL, array(
							'maxcount'   => $maxcount,
							'retention'  => $retention,
							'capstats'   => $capstats,
							'capmanager' => $capmanager,
						) );
						echo PSK_Tools::get_js_alert( __( 'Success!', PSK_S2MSFB_ID ), __( 'General settings saved', PSK_S2MSFB_ID ), 'success' );
					}
					break;
			}

		}

		if ( ( $maxcount == 0 ) && ( $retention == 0 ) ) {
			echo PSK_Tools::get_js_alert( __( 'Warning!', PSK_S2MSFB_ID ), __( 'Download logs limit and retention disabled', PSK_S2MSFB_ID ), 'warning', 60000 );
		} else {
			if ( $maxcount == 0 ) {
				echo PSK_Tools::get_js_alert( __( 'Info!', PSK_S2MSFB_ID ), __( 'Download logs limit disabled', PSK_S2MSFB_ID ), 'info', 60000 );
			} else if ( $retention == 0 ) {
				echo PSK_Tools::get_js_alert( __( 'Info!', PSK_S2MSFB_ID ), __( 'Download logs retention disabled', PSK_S2MSFB_ID ), 'info', 60000 );
			}
		}


		echo '<form class="form-horizontal" action="" method="post">';
		echo '  <input type="hidden" name="action" value="update"/>';
		wp_nonce_field( __CLASS__ . __METHOD__ );

		echo '  <fieldset>';
		echo '    <legend>' . __( 'Main settings', PSK_S2MSFB_ID ) . '</legend>';

		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="maxcount">' . __( 'Logs limit', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="maxcount" id="maxcount" value="' . esc_attr( $maxcount ) . '" required="required" />';
		echo '        <span class="help-inline"><em>' . __( 'When download records count has reach this limit, older records are deleted', PSK_S2MSFB_ID ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';

		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="retention">' . __( 'Logs retention', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <select id="retention" name="retention">';
		foreach ( $retention_days as $day => $val ) {
			$sel = ( $retention == $day ) ? ' selected="selected"' : "";
			echo '			<option value="' . $day . '"' . $sel . '>' . $val . '</option>';
		}
		echo '        </select>';
		echo '        <span class="help-inline"><em>' . __( 'Older download records are deleted', PSK_S2MSFB_ID ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';

		global $wpdb;
		$tablename = $wpdb->prefix . PSK_S2MSFB_DB_DOWNLOAD_TABLE_NAME;
		$sql       = "SELECT COUNT(*) FROM $tablename";
		$result    = $wpdb->get_row( $sql, ARRAY_N );
		echo '  <span class="help-inline"><em>' . sprintf( __( 'There are %s records now', PSK_S2MSFB_ID ), $result[0] ) . '</em>.</span>';
		echo '';

		echo '
		<a href="#myModal" role="button" class="btn btn-mini btn-danger" data-toggle="modal"><span class="icon-remove"></span> ' . __( 'Delete all records...' , PSK_S2MSFB_ID ) . '</a>
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel">' . __('Delete all download records',PSK_S2MSFB_ID) . '</h3>
		  </div>
		  <div class="modal-body">
		    <p>'.__('Hum... you should backup all download records before.',PSK_S2MSFB_ID).'</p>';
		echo '<a class="btn" href="?psk_s2msfb_download=psk_s2msfb_stats_all_xml&n=0"><span class="icon-tasks"></span> ' . __( 'Export all data as XML' , 'PSK_S2MSFB_ID' ) . '</a>';
		echo '&nbsp;<a class="btn" href="?psk_s2msfb_download=psk_s2msfb_stats_all_csv&n=0' . $_SERVER[ 'QUERY_STRING' ] . '&e=c"><span class="icon-th"></span> ' . __( 'Export all data as Excel CSV' , 'PSK_S2MSFB_ID' ) . '</a>';
		echo '</div>
		  <div class="modal-footer">
		    <button class="btn" data-dismiss="modal" aria-hidden="true">'.__('Close',PSK_S2MSFB_ID).'</button>
		    <a href="?'.$_SERVER['QUERY_STRING'].'&action=deletedwnl" class="btn btn-danger">'.__('Delete',PSK_S2MSFB_ID).'</a>
		  </div>
		</div>';


		echo '  </fieldset>';

		echo '<br/>';

		echo '  <fieldset>';
		echo '    <legend>' . __( 'Access settings', PSK_S2MSFB_ID ) . '</legend>';

		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="capstats">' . __( 'Stats capabilities', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="capstats" id="capstats" placeholder="' . esc_attr( PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ) . '" value="' . esc_attr( $capstats ) . '" required="required" />';
		echo '        <span class="help-inline"><em>' . __( 'Separate requested capabilities with coma', PSK_S2MSFB_ID ) . ' (<a href="http://codex.wordpress.org/Roles_and_Capabilities#Capabilities">' . __( 'Available capabilities here', PSK_S2MSFB_ID ) . '</a>)</em></span>';
		echo '      </div>';
		echo '    </div>';

		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="capmanager">' . __( 'File management capabilities', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="capmanager" id="capmanager" placeholder="' . esc_attr( PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ) . '" value="' . esc_attr( $capmanager ) . '" required="required" />';
		echo '        <span class="help-inline"><em>' . __( 'Separate requested capabilities with coma', PSK_S2MSFB_ID ) . ' (<a href="http://codex.wordpress.org/Roles_and_Capabilities#Capabilities">' . __( 'Available capabilities here', PSK_S2MSFB_ID ) . '</a>)</em></span>';
		echo '      </div>';
		echo '    </div>';

		echo '  </fieldset>';


		echo '  <br/>';
		echo '  <button type="submit" class="btn btn-primary">' . __( 'Save Changes', PSK_S2MSFB_ID ) . '</button>';
		echo '</form>';

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Stats > Notification
	 *
	 * @return      void
	 */
	public static function admin_screen_settings_notification() {

		$report_frequencies = array(
			""  => __( 'Never', PSK_S2MSFB_ID ),
			"d" => __( 'Daily', PSK_S2MSFB_ID ),
			"w" => __( 'Weekly', PSK_S2MSFB_ID ),
			"m" => __( 'Monthly', PSK_S2MSFB_ID ),
		);

		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		$settings        = get_option( PSK_S2MSFB_OPT_SETTINGS_NOTIFY );
		$emailfrom       = ( $settings['emailfrom'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_FROM : $settings['emailfrom'];
		$subject         = ( $settings['subject'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_SUBJECT : $settings['subject'];
		$emailto         = ( $settings['emailto'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_TO : $settings['emailto'];
		$emailnotify     = ( $settings['emailnotify'] != '1' ) ? '0' : '1';
		$reportemailfrom = ( $settings['reportemailfrom'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_REPORT_FROM : $settings['emailfrom'];
		$reportsubject   = ( $settings['reportsubject'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_REPORT_SUBJECT : $settings['subject'];
		$reportemailto   = ( $settings['reportemailto'] == '' ) ? PSK_S2MSFB_DEFAULT_EMAIL_REPORT_TO : $settings['emailto'];
		$reportfrequency = @$settings['reportfrequency'];
		$reporthour      = @$settings['reporthour'];

		if ( isset( $_POST['action'] ) ) {

			check_admin_referer( __CLASS__ . __METHOD__ );

			$action          = $_POST['action'];
			$emailfrom       = trim( $_POST['emailfrom'] );
			$emailto         = trim( $_POST['emailto'] );
			$emailnotify     = $_POST['emailnotify'];
			$subject         = $_POST['subject'];
			$reportemailfrom = trim( $_POST['reportemailfrom'] );
			$reportemailto   = trim( $_POST['reportemailto'] );
			$reportfrequency = $_POST['reportfrequency'];
			$reporthour      = $_POST['reporthour'];
			$reportsubject   = $_POST['reportsubject'];

			switch ( $action ) {

				case 'update':

					$form_is_valid = true;

					if ( is_email( $emailfrom ) != $emailfrom ) {
						echo PSK_Tools::get_js_alert( __( 'Error!', PSK_S2MSFB_ID ), sprintf( __( 'From email address %s is invalid', PSK_S2MSFB_ID ), $emailfrom ), 'error', 60000 );
						$form_is_valid = false;
					}

					$addresses = explode( ',', $emailto );
					$cleanaddr = array();
					foreach ( $addresses as $address ) {
						$address = trim( $address );
						if ( is_email( $address ) == $address ) {
							$cleanaddr[] = $address;
						} else {
							echo PSK_Tools::get_js_alert( __( 'Error!', PSK_S2MSFB_ID ), sprintf( __( 'Notify email address %s is invalid', PSK_S2MSFB_ID ), $address ), 'error', 60000 );
							$form_is_valid = false;
						}
					}

					if ( is_email( $reportemailfrom ) != $reportemailfrom ) {
						echo PSK_Tools::get_js_alert( __( 'Error!', PSK_S2MSFB_ID ), sprintf( __( 'From report email address %s is invalid', PSK_S2MSFB_ID ), $reportemailfrom ), 'error', 60000 );
						$form_is_valid = false;
					}

					$addresses       = explode( ',', $reportemailto );
					$reportcleanaddr = array();
					foreach ( $addresses as $address ) {
						$address = trim( $address );
						if ( is_email( $address ) == $address ) {
							$reportcleanaddr[] = $address;
						} else {
							echo PSK_Tools::get_js_alert( __( 'Error!', PSK_S2MSFB_ID ), sprintf( __( 'Notify report email address %s is invalid', PSK_S2MSFB_ID ), $reportaddress ), 'error', 60000 );
							$form_is_valid = false;
						}
					}


					if ( $form_is_valid === true ) {
						$emailto       = implode( ',', $cleanaddr );
						$reportemailto = implode( ',', $reportcleanaddr );
						update_option( PSK_S2MSFB_OPT_SETTINGS_NOTIFY, array(
							'subject'         => $subject,
							'emailfrom'       => $emailfrom,
							'emailto'         => $emailto,
							'emailnotify'     => $emailnotify,
							'reportsubject'   => $reportsubject,
							'reportemailfrom' => $reportemailfrom,
							'reportemailto'   => $reportemailto,
							'reportfrequency' => $reportfrequency,
							'reporthour'      => $reporthour,
						) );
						echo PSK_Tools::get_js_alert( __( 'Success!', PSK_S2MSFB_ID ), __( 'Notification settings saved', PSK_S2MSFB_ID ), 'success' );
					}

					$timestamp = wp_next_scheduled( PSK_S2MSFB_ID . '_cron_report' );
					wp_unschedule_event( $timestamp, PSK_S2MSFB_ID . '_cron_report' );
					PSK_S2MSFB::enable_cron();

					break;
			}
		}

		$emailnotify = ( $emailnotify == '1' ) ? ' checked="checked"' : "";

		echo '<form class="form-horizontal" action="" method="post">';
		echo '  <input type="hidden" name="action" value="update"/>';
		wp_nonce_field( __CLASS__ . __METHOD__ );

		echo '  <fieldset>';
		echo '    <legend>' . __( 'Real-time notification', PSK_S2MSFB_ID ) . '</legend>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="emailNotify">' . __( 'Notify by email', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '          <input type="checkbox" value="1" ' . $emailnotify . ' name="emailnotify" id="emailNotify "/>';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="emailFrom">' . __( 'From email address', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="email" name="emailfrom" id="emailFrom" value="' . esc_attr( $emailfrom ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_FROM ) . '" required="required" />';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="emailTo">' . __( 'Notify email address', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="emailto" id="emailTo" value="' . esc_attr( $emailto ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_TO ) . '" required="required" />';
		echo '        <span class="help-inline"><em>' . __( 'Separate multiple email address with a comma (,)' ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="subject">' . __( 'Email subject', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="subject" id="subject" value="' . esc_attr( $subject ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_DOWNLOAD_SUBJECT ) . '" />';
		echo '        <span class="help-inline"><em>' . __( 'You can use variable %blogname%' ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';
		echo '  </fieldset>';

		echo '  <fieldset>';
		echo '    <legend>' . __( 'Notification reports', PSK_S2MSFB_ID ) . '</legend>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="reportFrequency">' . __( 'Report Frequency', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '          <select name="reportfrequency">';
		foreach ( $report_frequencies as $i => $val ) {
			echo '<option value="' . $i . '"' . selected( $reportfrequency, $i, false ) . '>' . $val . '</option>';
		}
		echo '          </select>';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="reportHour">' . __( 'Delivery hour', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '          <select name="reporthour">';
		for ( $i = 0; $i < 24; $i ++ ) {
			echo '<option value="' . $i . '"' . selected( $reporthour, $i, false ) . '>' . str_pad( $i, 2, '0', STR_PAD_LEFT ) . ':00' . '</option>';
		}
		echo '          </select>';
		echo '      </div>';
		echo '    </div>';


		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="reportemailFrom">' . __( 'From email address', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="email" name="reportemailfrom" id="reportemailFrom" value="' . esc_attr( $reportemailfrom ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_REPORT_FROM ) . '" required="required" />';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="reportemailTo">' . __( 'Notify email address', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="reportemailto" id="reportemailTo" value="' . esc_attr( $reportemailto ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_REPORT_TO ) . '" required="required" />';
		echo '        <span class="help-inline"><em>' . __( 'Separate multiple email address with a comma (,)' ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';
		echo '    <div class="control-group">';
		echo '      <label class="control-label" for="reportsubject">' . __( 'Email subject', PSK_S2MSFB_ID ) . '</label>';
		echo '      <div class="controls">';
		echo '        <input type="text"  name="reportsubject" id="reportsubject" value="' . esc_attr( $reportsubject ) . '" placeholder="' . esc_attr( PSK_S2MSFB_DEFAULT_EMAIL_REPORT_SUBJECT ) . '" />';
		echo '        <span class="help-inline"><em>' . __( 'You can use variable %blogname%' ) . '</em></span>';
		echo '      </div>';
		echo '    </div>';

		$next = wp_next_scheduled( PSK_S2MSFB_ID . '_cron_report' );
		if ( $next !== false ) {
			$next += get_option( 'gmt_offset' ) * 3600;
			echo '<em>' . sprintf( __( 'Next report : %s', PSK_S2MSFB_ID ), date_i18n( sprintf( '%1$s - %2$s', get_option( 'date_format' ), get_option( 'time_format' ) ), $next ) ) . '</em>';
		}

		echo '  </fieldset>';
		echo '  <br/>';
		echo '  <button type="submit" class="btn btn-primary">' . __( 'Save Changes', PSK_S2MSFB_ID ) . '</button>';
		echo '</form>';

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}

}

PSK_S2MSFBAdminSettings::init();

