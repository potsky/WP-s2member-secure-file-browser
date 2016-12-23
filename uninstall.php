<?php

if (
	! defined( 'WP_UNINSTALL_PLUGIN' ) ||
	! WP_UNINSTALL_PLUGIN ||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) )
) {
	status_header( 404 );
	exit;
}


/*
 * Delete database
 */
define( 'PSK_S2MSFB_PLUGIN_FILE', __FILE__ );
require_once( 'inc/define.php' );
require( PSK_S2MSFB_INCLUDES_FOLDER . 'tools.class.php' );
require( PSK_S2MSFB_CLASSES_FOLDER . 'psk_s2msfb.class.php' );
PSK_S2MSFB::db_uninstall();


/*
 * Delete options
 */
delete_option( PSK_S2MSFB_OPT_SETTINGS_NOTIFY );
delete_option( PSK_S2MSFB_OPT_SETTINGS_GENERAL );


/*
 * Delete transients
 */
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_LATEST_ID );
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_TOP0_ID );
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_TOP1_ID );
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_TOP7_ID );
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_TOP31_ID );
delete_transient( PSK_S2MSFB_WIDGET_DOWNLOAD_TOP365_ID );
