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
 * Class PSK_S2MSFBAdminManager
 */
class PSK_S2MSFBAdminManager {

	public static $shortcode_options = array();


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
		wp_enqueue_script( 'jquery.tablesorter' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.min.js' , array( 'jquery' ) , false , true );
		wp_enqueue_script( 'jquery.tablesorter.widgets' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.widgets.min.js' , array( 'jquery' , 'jquery.tablesorter' ) , false , true );

		wp_enqueue_style( 'jquery.tablesorter.pager' , PSK_S2MSFB_CSS_URL . 'jquery.tablesorter.pager.' . PSK_S2MSFB_EXT_CSS );
		wp_enqueue_script( 'jquery.tablesorter.pager' , PSK_S2MSFB_JS_URL . 'jquery.tablesorter.pager.' . PSK_S2MSFB_EXT_JS , array( 'jquery' , 'jquery.tablesorter' ) , false , true );

		wp_enqueue_style( 'theme.bootstrap' , PSK_S2MSFB_CSS_URL . 'theme.bootstrap.' . PSK_S2MSFB_EXT_CSS );

		wp_enqueue_script( PSK_S2MSFB_ID . '.admin.manager' , PSK_S2MSFB_JS_URL . 'admin.manager.' . PSK_S2MSFB_EXT_JS , array( 'jquery' , 'jquery.tablesorter' ) , false , true );
	}


	public static function init_shortcode_options() {
		self::$shortcode_options = array(
			array(
				'name'     => 'collapseeasing' ,
				'desc'     => __( 'Easing function to use on collapse' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => 'swing' ,
				'defaultm' => '' ,
				'more'     => __( 'Can be set to <code>linear</code>' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'collapsespeed' ,
				'desc'     => __( 'Speed of the collapse folder action in ms' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '500' ,
				'defaultm' => '' ,
				'more'     => __( 'Use <code>-1</code> for no animation' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'cutdirnames' ,
				'desc'     => __( 'Truncate directory names to specific chars length' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Do not truncate' , PSK_S2MSFB_ID ) ,
				'more'     => '' ,
			) ,
			array(
				'name'     => 'cutfilenames' ,
				'desc'     => __( 'Truncate file names to specific chars length' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Do not truncate' , PSK_S2MSFB_ID ) ,
				'more'     => '' ,
			) ,
			array(
				'name'     => 'dirbase' ,
				'desc'     => __( 'Initial directory from the s2member-files directory' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '/' ,
				'defaultm' => '' ,
				'more'     => '' ,
			) ,
			array(
				'name'     => 'dirfirst' ,
				'desc'     => __( 'Show directories above files' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '1' ,
				'defaultm' => __( 'Show directories first' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Set to <code>0</code> to display directories with files' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'displayall' ,
				'desc'     => __( 'Display all items without checking if user is granted to download them' , PSK_S2MSFB_ID ) ,
				'descm'    => __( 'If the user downloads an unauthorized file, it will be redirected to the s2member Membership Page' , PSK_S2MSFB_ID ) ,
				'default'  => '0' ,
				'defaultm' => __( 'Only allowed directories are displayed' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Set to <code>1</code> to display all directories' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'displaybirthdate' ,
				'desc'     => __( 'Display files birth date' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => '' ,
				'more'     =>
				__( 'Set to <code>0</code> to hide the date when files and directories have been added' , PSK_S2MSFB_ID ) . '<br/>' .
				__( 'Can be set to <code>1</code> to display files added date only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display directories added date only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to display files and directories added date' , PSK_S2MSFB_ID )
			) ,
			array(
				'name'     => 'displaycomment' ,
				'desc'     => __( 'Display files comment' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '1' ,
				'defaultm' => '' ,
				'more'     =>
				__( 'Set to <code>0</code> to hide files and directories comments' , PSK_S2MSFB_ID ) .'<br/>' .
				__( 'Can be set to <code>1</code> to display files comments only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display directories comments only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to display files and directories comments' , PSK_S2MSFB_ID )
			) ,
			array(
				'name'     => 'displaydownloaded' ,
				'desc'     => __( 'Show if a file has already been downloaded' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Do not show already downloaded files' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Can be set to <code>1</code> to display a confirm box when user clicks to download an already downloaded file' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to lowlight already downloaded files in the browser with a message' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'displayname' ,
				'desc'     => __( 'Display files name' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '3' ,
				'defaultm' => '' ,
				'more'     => __( 'Set to <code>0</code> to display regular files and directories name' , PSK_S2MSFB_ID ) . '<br/>' .
				__( 'Can be set to <code>1</code> to display files displayname only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display directories displayname only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to display files and directories displayname' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'You can use these HTML tags : em strong u style' , PSK_S2MSFB_ID )
			) ,
			array(
				'name'     => 'displaysize' ,
				'desc'     => __( 'Display files size' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '1' ,
				'defaultm' => '' ,
				'more'     => __( 'Set to <code>0</code> to hide files size' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'displaymodificationdate' ,
				'desc'     => __( 'Display files modification date' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => '' ,
				'more'     =>
				__( 'Set to <code>0</code> to hide the date when files and directories have been modified' , PSK_S2MSFB_ID ) . '<br/>' .
				__( 'Can be set to <code>1</code> to display files modification date only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display directories modification date only' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to display files and directories modification date' , PSK_S2MSFB_ID )
			) ,
			array(
				'name'     => 'dirzip' ,
				'desc'     => __( 'Let directories be downloaded' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Directories cannot be downloaded as zip files' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'If set to <code>1</code> and if a zip file has exactly the same name as directory in the same parent folder, then a download button will be displayed and the zip file will be hidden.' , PSK_S2MSFB_ID ) .
					'<br/>' .
					__( 'Example:' , PSK_S2MSFB_ID ) .
					'<ul><li>- <code>parent/my_super_directory/toto</code></li><li>- <code>parent/my_super_directory/tata</code></li><li>- <code>parent/my_super_directory.zip</code></li></ul>' .
					__( 'will result in:' , PSK_S2MSFB_ID ) .
					'<ul><li>- <code>my_super_directory [download]</code></li><li>&nbsp;&nbsp;&nbsp;- <code>toto</code></li><li>&nbsp;&nbsp;&nbsp;- <code>tata</code></li></ul>' ,
			) ,
			array(
				'name'     => 'expandeasing' ,
				'desc'     => __( 'Easing function to use on expand' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => 'swing' ,
				'defaultm' => '' ,
				'more'     => __( 'Can be set to <code>linear</code>' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'expandspeed' ,
				'desc'     => __( 'Speed of the expand folder action in ms' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '500' ,
				'defaultm' => '' ,
				'more'     => __( 'Use <code>-1</code> for no animation' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'filterdir' ,
				'desc'     => __( 'A full regexp directories have to match to be displayed' , PSK_S2MSFB_ID ) ,
				'descm'    => __( 'Syntax available here' , PSK_S2MSFB_ID ) . ' <a href="http://www.php.net/manual/en/pcre.pattern.php">http://www.php.net/manual/en/pcre.pattern.php</a>' . '<br/>' . __( '<code>preg_match</code> PHP function is used' , PSK_S2MSFB_ID ) ,
				'default'  => '' ,
				'defaultm' => '' ,
				'more'     => __( 'eg: <code>/(access|user)/i</code>' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'filterfile' ,
				'desc'     => __( 'A full regexp files have to match to be displayed' , PSK_S2MSFB_ID ) ,
				'descm'    => __( 'Syntax available here' , PSK_S2MSFB_ID ) . ' <a href="http://www.php.net/manual/en/pcre.pattern.php">http://www.php.net/manual/en/pcre.pattern.php</a>' . '<br/>' . __( '<code>preg_match</code> PHP function is used' , PSK_S2MSFB_ID ) ,
				'default'  => '' ,
				'defaultm' => '' ,
				'more'     => __( 'eg: <code>/\.(png|jpe?g|gif|zip)$/i</code>' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'folderevent' ,
				'desc'     => __( 'Event to trigger expand/collapse' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => 'click' ,
				'defaultm' => __( 'User has to click to toggle directories, download files, ...' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Can be any javascript event like <code>mouseover</code>, ...' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'hidden' ,
				'desc'     => __( 'Show hidden files or not' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Do not show hidden files' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Set to <code>1</code> to display' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'multifolder' ,
				'desc'     => __( 'Whether or not to limit the browser to one subfolder at a time' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '1' ,
				'defaultm' => '' ,
				'more'     => __( 'Set to <code>0</code> to display only one open directory at a time' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'names' ,
				'desc'     => __( 'Replace files name with custom values' , PSK_S2MSFB_ID ) ,
				'descm'    => __( 'Syntax : <code>realfilename_1:Custom File Name #1|...|realfilename_n:Custom File Name #n</code>' , PSK_S2MSFB_ID ) ,
				'default'  => '' ,
				'defaultm' => '' ,
				'more'     => __( '<code>access-s2member-level#</code> will be automatically renamed with your s2member level custom labels.' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'openrecursive' ,
				'desc'     => __( 'Whether or not to open all subdirectories when opening a directory' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'User has to open directories himself' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Set to <code>1</code> to open recursively subdirectories when opening a directory (then all directories will be open at initialization)' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'previewext' ,
				'desc'     => __( 'Display file preview button for these extensions' , PSK_S2MSFB_ID ) ,
				'descm'    => __( 'Define file types by extensions seperated by coma' , PSK_S2MSFB_ID ) ,
				'default'  => '' ,
				'defaultm' => __( 'Supported values are : <code>mp3</code>, <code>jpg</code>, <code>jpeg</code>, <code>gif</code>, <code>png</code>' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'eg: <code>mp3,jpg,jpeg,gif,png</code>' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 's2alertbox' ,
				'desc'     => __( 'Display the s2member confirmation box when a user tries to download a file' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'No confirmation box displayed' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Set to <code>1</code> to display the confirmation box' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'search' ,
				'desc'     => __( 'Let user search files' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Search is unavailable' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'Can be set to <code>1</code> to display a global search button on top of the shortcode' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display a global search button on top and a search button for each directory' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'searchgroup' ,
				'desc'     => __( 'Group shortcodes with a single single search box' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'There is no group. Every shortcode has it own search box' , PSK_S2MSFB_ID ) ,
				'more'     => __( 'You can define groups by setting this value to <code>1</code> for all shortcodes in the first group, <code>2</code> for all shortcodes in the second group, ...' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'The first shortcode of every group will display the search box and performing a search in a box will launch a search in all shortcodes of the same group.' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'searchdisplay' ,
				'desc'     => __( 'How to display search results' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Files are flat displayed with full path between parenthesis' , PSK_S2MSFB_ID ) ,
				'more'     =>
				__( 'Can be set to <code>1</code> to display files without path' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to display files group by path' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to display files group by extension' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>4</code> to display files group by extension with full path between parenthesis' , PSK_S2MSFB_ID ) ,
			) ,
			array(
				'name'     => 'sortby' ,
				'desc'     => __( 'Sort files in directories by a criteria' , PSK_S2MSFB_ID ) ,
				'descm'    => '' ,
				'default'  => '0' ,
				'defaultm' => __( 'Files are sorted by name' , PSK_S2MSFB_ID ) ,
				'more'     =>
				__( 'Can be set to <code>0D</code> to sort files by name descendant' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>1</code> to sort files by extension' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>1D</code> to sort files by extension descendant' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2</code> to sort files by size' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>2D</code> to sort files by size descendant' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3</code> to sort files by modification date' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>3D</code> to sort files by modification date descendant' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>4</code> to sort files by birth date' , PSK_S2MSFB_ID ) . '<br/>' .
					__( 'Can be set to <code>4D</code> to sort files by birth date descendant' , PSK_S2MSFB_ID ) ,
			) ,
		);
	}


	/**
	 * Admin Screen Manager > Browser
	 *
	 * @return      void
	 */
	public static function admin_screen_manager_browse() {
		wp_localize_script( PSK_S2MSFB_ID . '.admin.manager' , 'objectL10n' ,
			array(
				 'xdebugerror'            => __( 'It seems you have xebug installed and try to delete a very deep directory.' , PSK_S2MSFB_ID ) ,
				 'erroroccurs'            => __( 'An error occurs' , PSK_S2MSFB_ID ) ,
				 'pleasewait'             => __( 'Please wait...' , PSK_S2MSFB_ID ) ,
				 'renamedirectory'        => __( 'Rename Directory' , PSK_S2MSFB_ID ) ,
				 'renamefile'             => __( 'Rename File' , PSK_S2MSFB_ID ) ,
				 'rename'                 => __( 'Rename' , PSK_S2MSFB_ID ) ,
				 'commentdirectory'       => __( 'Comment Directory' , PSK_S2MSFB_ID ) ,
				 'commentfile'            => __( 'Comment File' , PSK_S2MSFB_ID ) ,
				 'comment'                => __( 'Comment' , PSK_S2MSFB_ID ) ,
				 'commentplaceholder'     => __( 'Enter a comment or leave blank to disable comment' , PSK_S2MSFB_ID ) ,
				 'displaynamedirectory'   => __( 'Change Directory Display Name' , PSK_S2MSFB_ID ) ,
				 'displaynamefile'        => __( 'Change File Display Name' , PSK_S2MSFB_ID ) ,
				 'displayname'            => __( 'Change Display Name' , PSK_S2MSFB_ID ) ,
				 'displaynameplaceholder' => __( 'Enter a displayed name or leave blank to disable the displayed name' , PSK_S2MSFB_ID ) ,
				 'displaynameplacemore'   => __( 'You can use these HTML tags : em strong u style' , PSK_S2MSFB_ID ),
				 'removedirectorywarning' => __( 'Directory and all children will be deleted.<br/>You can not undo this action.' , PSK_S2MSFB_ID ) ,
				 'removefilewarning'      => __( 'File will be deleted.<br/>You can not undo this action.' , PSK_S2MSFB_ID ) ,
				 'remove'                 => __( 'Delete' , PSK_S2MSFB_ID ) ,
				 'removedirectory'        => __( 'Delete Directory' , PSK_S2MSFB_ID ) ,
				 'removefile'             => __( 'Delete File' , PSK_S2MSFB_ID ) ,
				 'renamefileok'           => __( 'File has been successfully renamed' , PSK_S2MSFB_ID ) ,
				 'renamedirectoryok'      => __( 'Directory has been successfully renamed' , PSK_S2MSFB_ID ) ,
				 'commentfileok'          => __( 'File has been successfully commented' , PSK_S2MSFB_ID ) ,
				 'commentdirectoryok'     => __( 'Directory has been successfully commented' , PSK_S2MSFB_ID ) ,
				 'displaynamefileok'      => __( 'File has been successfully virtually renamed' , PSK_S2MSFB_ID ) ,
				 'displaynamedirectoryok' => __( 'Directory has been successfully virtually renamed' , PSK_S2MSFB_ID ) ,
				 'removefileok'           => __( 'File has been successfully deleted' , PSK_S2MSFB_ID ) ,
				 'removedirectoryok'      => __( 'Directory has been successfully deleted' , PSK_S2MSFB_ID ) ,
				 'error'                  => _x( 'Error!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'success'                => _x( 'Success!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'info'                   => _x( 'Info!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'warning'                => _x( 'Warning!' , 'alertbox' , PSK_S2MSFB_ID ) ,
			)
		);

		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );
		echo PSK_S2MSFB::shortcode_s2member_secure_files_browser(
			array(
				 "loadmessage"             => __( "Please wait while loading..." , PSK_S2MSFB_ID ) ,
				 "openrecursive"           => "0" ,
				 "hidden"                  => "1" ,
				 "search"                  => "1" ,
				 "searchdisplay"           => "4" ,
				 "displaybirthdate"        => "3" ,
				 "displaycomment"          => "3" ,
				 "displayname"             => "3" ,
				 "displaymodificationdate" => "3" ,
			)
		);


		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen Manager > Cache Management
	 *
	 * @return      void
	 */
	public static function admin_screen_manager_cache() {

		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		echo __( 'Files structure is cached to improve performance and to check which file has been added, modified or deleted. For example, the <em>s2memberSBF What\'s new ?</em> Widget uses this cache to display latest available files.' , PSK_S2MSFB_ID );
		echo '<br/>';
		echo __( 'The cache is refreshed every hour and it is automatically executed when you delete or rename files/directories from the dashboard but when you manage files via SSH, FTP, ... you can refresh the cache here !' , PSK_S2MSFB_ID );
		echo '<br/><br/>';

		if ( isset( $_POST[ 'action' ] ) ) {
			check_admin_referer( __CLASS__ . __METHOD__ );
			$action = $_POST[ 'action' ];
			switch ( $action ) {
				case 'scannow':
					if ( isset( $_GET[ 'limit' ] ) ) {
						$limit = 100;
					} else {
						$limit = 1;
					}

					if ( isset( $_GET[ 'cc' ] ) ) {
						PSK_S2MSFB::db_uninstall_files();
						PSK_S2MSFB::db_install_files( false );
						$result  = PSK_S2MSFB::db_clean_files( true );
						$message = __( 'The cache has been totally successfully re-computed !' , PSK_S2MSFB_ID ) . '<br/><br/>';
						$limit   = 100;
					} else {
						$result  = PSK_S2MSFB::db_clean_files( true );
						$message = __( 'The cache has been successfully refreshed !' , PSK_S2MSFB_ID ) . '<br/><br/>';
					}
					$total = 0;

					$a = $result[ 0 ];
					$l = count( $a );
					if ( $l > 0 ) {
						$message .= '<strong>';
						$message .= ( $l == 1 ) ? __( '1 new file' , PSK_S2MSFB_ID ) : sprintf( __( '%s new files' , PSK_S2MSFB_ID ) , $l );
						$message .= '</strong>';
						$message .= ' : <ul>';
						$m = 0;
						foreach ( $a as $filepath => $fileinfo ) {
							$message .= '<li><small>' . PSK_Tools::mb_html_entities( $filepath ) . '</small></li>';
							if ( $m ++ > $limit ) {
								$message .= '<li>...</li>';
								break;
							}
						}
						$message .= '</ul>';
						$total += $l;
					}

					$a = $result[ 1 ];
					$l = count( $a );
					if ( $l > 0 ) {
						$message .= '<strong>';
						$message .= ( $l == 1 ) ? __( '1 modified file' , PSK_S2MSFB_ID ) : sprintf( __( '%s modified files' , PSK_S2MSFB_ID ) , $l );
						$message .= '</strong>';
						$message .= ' : <ul>';
						$m = 0;
						foreach ( $a as $filepath => $fileinfo ) {
							$message .= '<li><small>' . PSK_Tools::mb_html_entities( $filepath ) . '</small></li>';
							if ( $m ++ > $limit ) {
								$message .= '<li>...</li>';
								break;
							}
						}
						$message .= '</ul>';
						$total += $l;
					}

					$a = $result[ 2 ];
					$l = count( $a );
					if ( $l > 0 ) {
						$message .= '<strong>';
						$message .= ( $l == 1 ) ? __( '1 deleted file' , PSK_S2MSFB_ID ) : sprintf( __( '%s deleted files' , PSK_S2MSFB_ID ) , $l );
						$message .= '</strong>';
						$message .= ' : <ul>';
						$m = 0;
						foreach ( $a as $filepath => $fileinfo ) {
							$message .= '<li><small>' . PSK_Tools::mb_html_entities( $filepath ) . '</small></li>';
							if ( $m ++ > $limit ) {
								$message .= '<li>...</li>';
								break;
							}
						}
						$message .= '</ul>';
						$total += $l;
					}

					if ( $total == 0 ) {
						$message .= 'Nothing has been detected, everything is fine.';
					}


					echo PSK_Tools::get_js_alert( __( 'Success!' , PSK_S2MSFB_ID ) , $message , 'success' , 60000 );
					break;
				default:
					break;
			}
		}

		$last  = get_option( PSK_S2MSFB_DB_FILES_CLEAN_OPT );
		$count = get_option( PSK_S2MSFB_DB_FILES_CLEAN_COUNT_OPT );
		$last += get_option( 'gmt_offset' ) * 3600;
		$duration = (int) get_option( PSK_S2MSFB_DB_FILES_CLEAN_DURATION_OPT );
		if ( ! is_array( $count ) ) {
			$count = __( 'No file found' , PSK_S2MSFB_ID );
		} else if ( $count[ 0 ] == 0 ) {
			if ( $count[ 1 ] == 0 ) {
				$count = __( 'No file found' , PSK_S2MSFB_ID );
			} else if ( $count[ 1 ] == 1 ) {
				$count = sprintf( __( '1 directory found' , PSK_S2MSFB_ID ) );
			} else {
				$count = sprintf( __( '%s directories found' , PSK_S2MSFB_ID ) , $count[ 1 ] );
			}
		} else if ( $count[ 0 ] == 1 ) {
			if ( $count[ 1 ] == 0 ) {
				$count = __( '1 file found' , PSK_S2MSFB_ID );
			} else if ( $count[ 1 ] == 1 ) {
				$count = sprintf( __( '1 file and 1 directory found' , PSK_S2MSFB_ID ) );
			} else {
				$count = sprintf( __( '1 file and %s directories found' , PSK_S2MSFB_ID ) , $count[ 1 ] );
			}
		} else {
			if ( $count[ 1 ] == 0 ) {
				$count = sprintf( __( '%s files found' , PSK_S2MSFB_ID ) , $count[ 0 ] );
			} else if ( $count[ 1 ] == 1 ) {
				$count = sprintf( __( '%s files and 1 directory found' , PSK_S2MSFB_ID ) , $count[ 0 ] );
			} else {
				$count = sprintf( __( '%s files and %s directories found' , PSK_S2MSFB_ID ) , $count[ 0 ] , $count[ 1 ] );
			}
		}
		echo '<em>' . sprintf( __( 'Last file scan on %s in %ss (%s)' , PSK_S2MSFB_ID ) , date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , $last ) , $duration , $count ) . '</em>';
		echo '<br/>';

		$next = wp_next_scheduled( PSK_S2MSFB_ID . '_cron_db_clean_files_hook' );
		if ( $next !== false ) {
			$next += get_option( 'gmt_offset' ) * 3600;
			echo '<em>' . sprintf( __( 'Next file scan on %s' , PSK_S2MSFB_ID ) , date_i18n( sprintf( '%1$s - %2$s' , get_option( 'date_format' ) , get_option( 'time_format' ) ) , $next ) ) . '</em>';
		}

		echo '<br/><br/>';

		echo '<form class="form-horizontal" action="" method="post">';
		echo '  <input type="hidden" name="action" value="scannow"/>';
		wp_nonce_field( __CLASS__ . __METHOD__ );

		echo '<button type="submit" class="btn btn-primary">' . __( 'Refresh cache now' , PSK_S2MSFB_ID ) . '</button>';
		echo '<span class="help-inline"><em>' . __( 'Can be very long...' ) . '</em></span>';
		echo '</form>';

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Manager > Documentation > Shortcode
	 *
	 * @return      void
	 */
	public static function admin_screen_manager_docshortcode() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );
		self::init_shortcode_options();

		// doc generator for me !
		if ( isset( $_GET[ 'me' ] ) ) {

			echo 'For readme.txt : <br/><pre>';
			foreach ( self::$shortcode_options as $option ) {
				echo "* `" . $option[ 'name' ] . "` : " . $option[ 'desc' ] . "\n";
			}
			echo '</pre>';

			echo 'For wordpress doc : <br/><pre>';
			foreach ( self::$shortcode_options as $option ) {
				echo "&lt;li&gt;&lt;code&gt;" . $option[ 'name' ] . "&lt;/code&gt; : " . $option[ 'desc' ] . "&lt;/li&gt;\n";
			}
			echo '</pre>';

		} else {
			echo '<table class="table table-bordered table-striped">
	            <thead>
	              <tr>
	                <th>' . __( 'Tag' , PSK_S2MSFB_ID ) . '</th>
	                <th>' . __( 'Description' , PSK_S2MSFB_ID ) . '</th>
	                <th>' . __( 'Default value' , PSK_S2MSFB_ID ) . '</th>
	                <th>' . __( 'Comment' , PSK_S2MSFB_ID ) . '</th>
	              </tr>
	            </thead>
	            <tbody>';

			foreach ( self::$shortcode_options as $option ) {
				echo '<tr>
	              <td><code>' . $option[ 'name' ] . '</code></td>
	              <td>' . $option[ 'desc' ] . '<br/><em class="muted">' . $option[ 'descm' ] . '</em></td>
	              <td><code>' . $option[ 'default' ] . '</code><br/><em class="muted">' . $option[ 'defaultm' ] . '</em></td>
	              <td>' . $option[ 'more' ] . '</td>
	            </tr>';
			}

			echo '</tbody></table>';
		}

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Admin Screen : Manager > Tools > Shortcode generator
	 *
	 * @return      void
	 */
	public static function admin_screen_manager_shortcodegenerator() {

		self::init_shortcode_options();
		$tags = array();
		foreach ( self::$shortcode_options as $option ) {
			$tags[ ] = $option[ 'name' ];
		}

		wp_localize_script( PSK_S2MSFB_ID . '.admin.manager' , 'objectL10n' ,
			array(
				 'shortcode'     => PSK_S2MSFB_SHORTCODE_NAME_0 ,
				 'shortcodetags' => implode( ',' , $tags ) ,
				 'error'         => _x( 'Error!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'success'       => _x( 'Success!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'info'          => _x( 'Info!' , 'alertbox' , PSK_S2MSFB_ID ) ,
				 'warning'       => _x( 'Warning!' , 'alertbox' , PSK_S2MSFB_ID ) ,
			)
		);

		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		echo '<table class="table table-bordered table-striped table-condensed">
            <thead>
              <tr>
                <th>' . __( 'Tag' , PSK_S2MSFB_ID ) . '</th>
                <th>' . __( 'Description' , PSK_S2MSFB_ID ) . '</th>
                <th>' . __( 'Value' , PSK_S2MSFB_ID ) . '</th>
              </tr>
            </thead>
            <tbody>';

		foreach ( self::$shortcode_options as $option ) {
			$tagname    = $option[ 'name' ];
			$default    = $option[ 'default' ];
			$currentval = $default;
			$control    = '<div class="control-group" id="cg' . $tagname . '">';


			switch ( $tagname ) {

				case 'displaydownloaded' :
				case 'search' :
					$checked0 = ( $default == "0" ) ? ' checked="checked"' : '';
					$checked1 = ( $default == "1" ) ? ' checked="checked"' : '';
					$checked2 = ( $default == "2" ) ? ' checked="checked"' : '';
					$control .= '
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2" value="2"' . $checked2 . '/>' . __( 'Full' , PSK_S2MSFB_ID ) . '
					</label>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1" value="1"' . $checked1 . '/>' . __( 'Yes' , PSK_S2MSFB_ID ) . '
					</label>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '0" value="0"' . $checked0 . '/>' . __( 'No' , PSK_S2MSFB_ID ) . '
					</label>';
					break;

				case 'sortby' :
					$checked0  = ( $default == "0" ) ? ' checked="checked"' : '';
					$checked1  = ( $default == "1" ) ? ' checked="checked"' : '';
					$checked2  = ( $default == "2" ) ? ' checked="checked"' : '';
					$checked3  = ( $default == "3" ) ? ' checked="checked"' : '';
					$checked4  = ( $default == "4" ) ? ' checked="checked"' : '';
					$checked0D = ( $default == "0D" ) ? ' checked="checked"' : '';
					$checked1D = ( $default == "1D" ) ? ' checked="checked"' : '';
					$checked2D = ( $default == "2D" ) ? ' checked="checked"' : '';
					$checked3D = ( $default == "3D" ) ? ' checked="checked"' : '';
					$checked4D = ( $default == "4D" ) ? ' checked="checked"' : '';
					$control .= '
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '0" value="0"' . $checked0 . '/>' . __( 'Sort files by name' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '0D" value="0D"' . $checked0D . '/>' . __( 'Sort files by name descendant' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1" value="1"' . $checked1 . '/>' . __( 'Sort files by extension' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1D" value="1D"' . $checked1D . '/>' . __( 'Sort files by extension descendant' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2" value="2"' . $checked2 . '/>' . __( 'Sort files by size' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2D" value="2D"' . $checked2D . '/>' . __( 'Sort files by size descendant' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '3" value="3"' . $checked3 . '/>' . __( 'Sort files by modification date' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '3D" value="3D"' . $checked3D . '/>' . __( 'Sort files by modification date descendant' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '4" value="4"' . $checked4 . '/>' . __( 'Sort files by birth date' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '4D" value="4D"' . $checked4D . '/>' . __( 'Sort files by birth date descendant' , PSK_S2MSFB_ID ) . '
					</label>';
					break;

				case 'displaybirthdate'        :
				case 'displaymodificationdate' :
				case 'displaycomment'          :
				case 'displayname'          :
					$checked0 = ( $default == "0" ) ? ' checked="checked"' : '';
					$checked1 = ( $default == "1" ) ? ' checked="checked"' : '';
					$checked2 = ( $default == "2" ) ? ' checked="checked"' : '';
					$checked3 = ( $default == "3" ) ? ' checked="checked"' : '';
					$control .= '
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '0" value="0"' . $checked0 . '/>' . __( 'No' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1" value="1"' . $checked1 . '/>' . __( 'Only files' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2" value="2"' . $checked2 . '/>' . __( 'Only directories' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '3" value="3"' . $checked3 . '/>' . __( 'Files and directories' , PSK_S2MSFB_ID ) . '
					</label>';
					break;

				case 'searchdisplay' :
					$checked0 = ( $default == "0" ) ? ' checked="checked"' : '';
					$checked1 = ( $default == "1" ) ? ' checked="checked"' : '';
					$checked2 = ( $default == "2" ) ? ' checked="checked"' : '';
					$checked3 = ( $default == "3" ) ? ' checked="checked"' : '';
					$checked4 = ( $default == "4" ) ? ' checked="checked"' : '';
					$control .= '
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '0" value="0"' . $checked0 . '/>' . __( 'Flat with path' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1" value="1"' . $checked1 . '/>' . __( 'Flat without path' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2" value="2"' . $checked2 . '/>' . __( 'Group by path' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '3" value="3"' . $checked3 . '/>' . __( 'Group by extension' , PSK_S2MSFB_ID ) . '
					</label><br/>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '4" value="4"' . $checked4 . '/>' . __( 'Group by extension with path' , PSK_S2MSFB_ID ) . '
					</label>';
					break;

				case 'displayall'              :
				case 'displaysize'             :
				case 'dirzip'                  :
				case 'dirfirst'                :
				case 'hidden'                  :
				case 'multifolder'             :
				case 'openrecursive'           :
				case 's2alertbox'              :
					$checked1 = ( $default == "1" ) ? ' checked="checked"' : '';
					$checked0 = ( $default != "1" ) ? ' checked="checked"' : '';
					$control .= '
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '1" value="1"' . $checked1 . '/>' . __( 'Yes' , PSK_S2MSFB_ID ) . '
					</label>
					<label class="radio inline">
					  <input class="generator" type="radio" name="' . $tagname . '" id="' . $tagname . '2" value="0"' . $checked0 . '/>' . __( 'No' , PSK_S2MSFB_ID ) . '
					</label>';
					break;

				case 'folderevent':
					$values = array( 'blur' , 'click' , 'dblclick' , 'focus' , 'focusin' , 'hover' , 'keydown' , 'keypress' , 'keyup' , 'mousedown' , 'mouseenter' , 'mouseleave' , 'mousemove' , 'mouseout' , 'mouseover' , 'mouseup' );
					$control .= '<select class="generator" name="' . $tagname . '" id="' . $tagname . '">';
					foreach ( $values as $value ) {
						$control .= '<option value="' . $value . '"';
						if ( $currentval == $value ) $control .= ' selected="selected"';
						$control .= '>' . $value . '</option>';
					}
					$control .= '</select>';
					break;

				case 'collapseeasing':
				case 'expandeasing':
					$values = array( 'linear' , 'swing' );
					$control .= '<select class="generator" name="' . $tagname . '" id="' . $tagname . '">';
					foreach ( $values as $value ) {
						$control .= '<option value="' . $value . '"';
						if ( $currentval == $value ) $control .= ' selected="selected"';
						$control .= '>' . $value . '</option>';
					}
					$control .= '</select>';
					break;

				case 'names':
					for ( $i = 0 ; $i < 5 ; $i ++ ) {
						$control .= '<label class="control-label inline" for="' . $tagname . $i . '">' . constant( 'PSK_S2MSFB_S2MEMBER_LEVEL' . $i . '_FOLDER' ) . ' : ';
						$control .= '  <input id="h' . $tagname . $i . '" name="h' . $tagname . $i . '" type="hidden" value="' . esc_attr( constant( 'PSK_S2MSFB_S2MEMBER_LEVEL' . $i . '_FOLDER' ) ) . '" />';
						$control .= '  <input class="generator" id="' . $tagname . $i . '" name="' . $tagname . $i . '" type="text" value="' . esc_attr( $currentval ) . '" placeholder="' . esc_attr( constant( 'PSK_S2MSFB_S2MEMBER_LEVEL' . $i . '_FOLDER' ) ) . '" />';
						$control .= '</label>';
					}
					$control .= '<label class="control-label inline" for="' . $tagname . $i . '">' . PSK_S2MSFB_S2MEMBER_CCAP_FOLDER . 'videos' . ' : ';
					$control .= '  <input id="h' . $tagname . $i . '" name="h' . $tagname . $i . '" type="hidden" value="' . esc_attr( PSK_S2MSFB_S2MEMBER_CCAP_FOLDER . 'videos' ) . '" />';
					$control .= '  <input class="generator" id="' . $tagname . $i . '" name="' . $tagname . $i . '" type="text" value="' . esc_attr( $currentval ) . '" placeholder="' . esc_attr( PSK_S2MSFB_S2MEMBER_CCAP_FOLDER . 'videos' ) . '" />';
					$control .= '</label>';
					break;

				case 'searchgroup':
					$control .= '<input class="generator flat" id="' . $tagname . '" name="' . $tagname . '" type="number" value="' . esc_attr( $currentval ) . '" placeholder="' . esc_attr( $default ) . '" />';
					break;

				default:
					$control .= '<input class="generator" id="' . $tagname . '" name="' . $tagname . '" type="text" value="' . esc_attr( $currentval ) . '" placeholder="' . esc_attr( $default ) . '" />';
					break;
			}

			$control .= '</div>';

			echo '<tr>
              <td><code>' . $tagname . '</code></td>
              <td>' . $option[ 'desc' ] . '</td>
              <td>' . $control . '</td>
            </tr>';
		}

		echo '</tbody></table>';

		echo '<h5>' . __( 'Copy paste this shortcode in pages :' , PSK_S2MSFB_ID ) . '</h5>';
		echo '<pre id="shortcode_preview"></pre>';

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}


	/**
	 * Ajax call - Returns a directory as a html structure
	 * We do not call PSK_S2MSFB because we have to set before $is_admin to true
	 */
	public static function ajax_admin_get_directory() {
		PSK_S2MSFB::set_is_admin( true );
		PSK_S2MSFB::ajax_do_get_directory();
		die();
	}


	/**
	 * Ajax call - Delete a file or directory
	 */
	public static function ajax_admin_delete_file() {

		if ( ! isset( $_POST[ 'nonce' ] ) || ! check_ajax_referer( PSK_S2MSFB_ID . '-nonce' , 'nonce' , false ) )
			die ( 'Invalid nonce' );

		if ( ! isset( $_POST[ 's' ] ) )
			die ( 'invalid parameters' );

		$current = PSK_S2MSFB_S2MEMBER_FILES_FOLDER . stripslashes( rawurldecode( @$_POST[ 's' ] ) );
		if ( ! PSK_Tools::is_directory_allowed( $current ) )
			die( 'Forbidden' );

		PSK_Tools::rm_secure_recursive( $current );
		PSK_S2MSFB::db_clean_files();
		die( '1' );
	}


	/**
	 * Ajax call - Rename a file or directory
	 */
	public static function ajax_admin_rename_file() {

		if ( ! isset( $_POST[ 'nonce' ] ) || ! check_ajax_referer( PSK_S2MSFB_ID . '-nonce' , 'nonce' , false ) )
			die ( 'Invalid nonce' );

		if ( ! isset( $_POST[ 's' ] ) )
			die ( 'Invalid parameters' );

		if ( ! isset( $_POST[ 'd' ] ) )
			die ( 'Invalid parameters' );

		$current = PSK_S2MSFB_S2MEMBER_FILES_FOLDER . stripslashes( rawurldecode( @$_POST[ 's' ] ) );
		if ( ! PSK_Tools::is_directory_allowed( $current ) )
			die( 'Forbidden' );

		$destination = dirname( $current ) . DIRECTORY_SEPARATOR . str_replace( array( '\\' , '/' , ':' ) , array( '_' , '_' , '_' ) , stripslashes( rawurldecode( $_POST[ 'd' ] ) ) );
		rename( $current , $destination );
		PSK_S2MSFB::db_clean_files();
		die( '1' );
	}


	/**
	 * Ajax call - Comment a file or directory
	 */
	public static function ajax_admin_comment_file() {

		if ( ! isset( $_POST[ 'nonce' ] ) || ! check_ajax_referer( PSK_S2MSFB_ID . '-nonce' , 'nonce' , false ) )
			die ( 'Invalid nonce' );

		if ( ! isset( $_POST[ 's' ] ) )
			die ( 'Invalid parameters' );

		if ( ! isset( $_POST[ 'c' ] ) )
			die ( 'Invalid parameters' );

		$source  = stripslashes( rawurldecode( @$_POST[ 's' ] ) );
		$current = PSK_S2MSFB_S2MEMBER_FILES_FOLDER . $source;
		if ( ! PSK_Tools::is_directory_allowed( $current ) )
			die( 'Forbidden' );

		/** @var $wpdb WPDB */
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . PSK_S2MSFB_DB_FILES_TABLE_NAME ,
			array( 'comment' => stripslashes( $_POST[ 'c' ] ) ) ,
			array( 'filepath' => $source ) ,
			array( '%s' ) ,
			array( '%s' )
		);

		die( '1' );
	}


	/**
	 * Ajax call - Change display name of a file or directory
	 */
	public static function ajax_admin_displayname_file() {

		if ( ! isset( $_POST[ 'nonce' ] ) || ! check_ajax_referer( PSK_S2MSFB_ID . '-nonce' , 'nonce' , false ) )
			die ( 'Invalid nonce' );

		if ( ! isset( $_POST[ 's' ] ) )
			die ( 'Invalid parameters' );

		if ( ! isset( $_POST[ 'c' ] ) )
			die ( 'Invalid parameters' );

		$source  = stripslashes( rawurldecode( @$_POST[ 's' ] ) );
		$current = PSK_S2MSFB_S2MEMBER_FILES_FOLDER . $source;
		if ( ! PSK_Tools::is_directory_allowed( $current ) )
			die( 'Forbidden' );

		/** @var $wpdb WPDB */
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . PSK_S2MSFB_DB_FILES_TABLE_NAME ,
			array( 'displayname' => strip_tags( stripslashes( $_POST[ 'c' ] ) , '<em><strong><u><style>' ) ) ,
			array( 'filepath' => $source ) ,
			array( '%s' ) ,
			array( '%s' )
		);

		die( '1' );
	}

}

PSK_S2MSFBAdminManager::init();

