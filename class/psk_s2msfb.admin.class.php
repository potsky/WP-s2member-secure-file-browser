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
 * Class PSK_S2MSFBAdmin
 */
class PSK_S2MSFBAdmin {
	private static $admin_menu_right = PSK_S2MSFB_ADMIN_SETTINGS_ACCESS;
	private static $admin_menu       = array();


	/**
	 * Returns the name of the current page (not for ajax)
	 *
	 * @return      string    empty of page not found or the name of the page
	 */
	public static function get_admin_my_plugin_page() {
		if ( isset( $_REQUEST[ 'page' ] ) ) {
			$current_screen = $_REQUEST[ 'page' ];
			$token          = PSK_S2MSFB_ID . '_';
			$p              = strpos( $current_screen , $token );
			if ( $p === false ) {
				return '';
			} else {
				return substr( $current_screen , $p + strlen( $token ) );
			}
		} else {
			return '';
		}
	}


	/**
	 * Return the page name according to the class and method names
	 *
	 * @param      string $method the method name like PSK_S2MSFBAdminManager::admin_screen_manager_browse
	 *
	 * @return      string    page title
	 */
	public static function get_admin_screen_title( $method ) {
		list( $psk , $admin , $screen , $parent , $child ) = array_pad( explode( '_' , $method , 5 ) , 5 , null );

		if ( isset( self::$admin_menu[ 'left' ][ $parent ] ) ) {
			$parent = self::$admin_menu[ 'left' ][ $parent ];
		} else if ( isset( self::$admin_menu[ 'right' ][ $parent ] ) ) {
			$parent = self::$admin_menu[ 'right' ][ $parent ];
		} else {
			$parent = '';
		}

		if ( $parent != '' ) {
			$parent = $parent[ 'name' ] . ' &gt; ' . $parent[ 'chil' ][ $child ][ 'name' ];
		}

		return $parent;
	}


	/**
	 * Return the admin class file to load for the current page
	 *
	 * @param      string $page the page name like manager_browse
	 *
	 * @return      string    page title
	 */
	public static function get_admin_class_file( $page ) {
		if ( $page == 'home' ) {
			return '';
		}

		list( $parent , $child ) = array_pad( explode( '_' , $page , 2 ) , 2 , null );

		return ( $parent == '' ) ? '' : PSK_S2MSFB_ADMIN_CLASS_FILE_BASE . '.' . $parent . '.class.php';
	}


	/**
	 * Load the worker class according to the page name
	 *
	 * @param      string $page the page name like manager_browse
	 *
	 * @return      boolean    load or not ?
	 */
	public static function load_admin_class_file( $page ) {
		$load_class_file = self::get_admin_class_file( $page );
		if ( $load_class_file != '' ) {
			$load_class_file = PSK_S2MSFB_CLASSES_FOLDER . $load_class_file;
			if ( file_exists( $load_class_file ) ) {
				/** @noinspection PhpIncludeInspection */
				require_once( $load_class_file );

				return true;
			} else {
			}
		}

		return false;
	}


	/**
	 * Initialization
	 *
	 * @return void
	 */
	public static function init() {
		// Define actions
		add_action( "ws_plugin__s2member_during_add_admin_options_additional_pages" , array(
			__CLASS__ ,
			'admin_menu_items' ,
		) , 666 );
		add_action( 'admin_enqueue_scripts' , array( __CLASS__ , 'init_assets' ) );
		add_action( 'admin_init' , array( __CLASS__ , 'admin_init' ) );


		// Load the worker class
		self::load_admin_class_file( self::get_admin_my_plugin_page() );

	}


	/**
	 * Initialization
	 *
	 * @wp_action    init
	 */
	public static function admin_init() {
	}


	/**
	 * Menu Initialization
	 * Set menu and submenus title and rights
	 *
	 * @param $settings string the right settings
	 *
	 * @wp_action    ws_plugin__s2member_during_add_admin_options_additional_pages
	 */
	public static function init_menu( $settings ) {
		self::$admin_menu = array(
			'left'  => array(
				'stats'   => array(
					'class' => 'Stats' ,
					'right' => ( @$settings[ 'capstats' ] == '' ) ? PSK_S2MSFB_ADMIN_SETTINGS_ACCESS : PSK_S2MSFB_ADMIN_SETTINGS_ACCESS . ',' . @$settings[ 'capstats' ] ,
					'name'  => __( 'Statistics' , PSK_S2MSFB_ID ) ,
					'chil'  => array(
						'all' => array( 'name' => __( 'All downloads' , PSK_S2MSFB_ID ) ) ,
						'fil' => array( 'name' => __( 'Top files' , PSK_S2MSFB_ID ) ) ,
						'use' => array( 'name' => __( 'Top downloaders' , PSK_S2MSFB_ID ) ) ,
						'di2' => '' ,
						'log' => array( 'name' => __( 'Current s2member Accounting' , PSK_S2MSFB_ID ) ) ,
					) ,
				) ,
				'manager' => array(
					'class' => 'Manager' ,
					'right' => PSK_S2MSFB_ADMIN_DOCUMENTATION_ACCESS ,
					'name'  => __( 'Browser' , PSK_S2MSFB_ID ) ,
					'chil'  => array(
						'browse'             => array(
							'name'  => __( 'Manage files' , PSK_S2MSFB_ID ) ,
							'right' => ( @$settings[ 'capmanager' ] == '' ) ? PSK_S2MSFB_ADMIN_SETTINGS_ACCESS : PSK_S2MSFB_ADMIN_SETTINGS_ACCESS . ',' . @$settings[ 'capmanager' ] ,
						) ,
						'di1'                => '' ,
						'he1'                => __( 'Tools' , PSK_S2MSFB_ID ) ,
						'cache'              => array(
							'name'   => __( 'Cache management' , PSK_S2MSFB_ID ) ,
							'rights' => PSK_S2MSFB_ADMIN_DOCUMENTATION_ACCESS ,
						) ,
						'shortcodegenerator' => array(
							'name'   => __( 'Shortcode generator' , PSK_S2MSFB_ID ) ,
							'rights' => PSK_S2MSFB_ADMIN_DOCUMENTATION_ACCESS ,
						) ,
						'di2'                => '' ,
						'he2'                => __( 'Documentation' , PSK_S2MSFB_ID ) ,
						'docshortcode'       => array(
							'name'   => __( 'Shortcode options' , PSK_S2MSFB_ID ) ,
							'rights' => PSK_S2MSFB_ADMIN_DOCUMENTATION_ACCESS ,
						) ,
					) ,
				) ,
			) ,
			'right' => array(
				'settings' => array(
					'class' => 'Settings' ,
					'right' => PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ,
					'name'  => __( 'Settings' , PSK_S2MSFB_ID ) ,
					'chil'  => array(
						'main'         => array( 'name' => __( 'General' , PSK_S2MSFB_ID ) ) ,
						'notification' => array( 'name' => __( 'Notification' , PSK_S2MSFB_ID ) ) ,
					) ,
				) ,
			) ,
		);
	}


	/**
	 * Load javascript and css for Public and Admin part
	 * @wp_action    admin_enqueue_scripts
	 * @wp_action    wp_enqueue_scripts
	 *
	 * @return      void
	 */
	public static function init_assets() {
		if ( self::get_admin_my_plugin_page() != '' ) {

			PSK_S2MSFB::init_assets();

			wp_register_script( 'bootstrap' , PSK_S2MSFB_JS_URL . 'bootstrap.psk.min.js' , array( 'jquery' ) , false , true );
			wp_enqueue_script( 'bootstrap' );
			wp_register_style( 'bootstrap' , PSK_S2MSFB_CSS_URL . 'bootstrap.psk.min.css' );
			wp_enqueue_style( 'bootstrap' );

			wp_enqueue_script( PSK_S2MSFB_ID . '.admin' , PSK_S2MSFB_JS_URL . 'admin.' . PSK_S2MSFB_EXT_JS , array( 'jquery' ) , false , false ); // in header because of alert manager
			wp_enqueue_style( PSK_S2MSFB_ID . '.admin' , PSK_S2MSFB_CSS_URL . 'admin.' . PSK_S2MSFB_EXT_CSS );
		}
	}


	/**
	 * Add menu pages according to rights
	 *
	 * @return      void
	 */
	public static function admin_menu_items() {
		$settings    = get_option( PSK_S2MSFB_OPT_SETTINGS_GENERAL );
		$capablities = PSK_S2MSFB_ADMIN_DOCUMENTATION_ACCESS;
		$capablities .= ( isset( $settings[ 'capstats' ] ) ) ? ',' . $settings[ 'capstats' ] : ',' . PSK_S2MSFB_ADMIN_SETTINGS_ACCESS;
		$capablities .= ( isset( $settings[ 'capmanager' ] ) ) ? ',' . $settings[ 'capmanager' ] : ',' . PSK_S2MSFB_ADMIN_SETTINGS_ACCESS;

		self::init_menu( $settings );

		if ( current_user_can( PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ) ) {
			add_submenu_page( 'ws-plugin--s2member-start' , '' , '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>' , 'administrator' , "#" );
			add_submenu_page( 'ws-plugin--s2member-start' , PSK_S2MSFB_NAME , PSK_S2MSFB_MENUNAME , 'administrator' , PSK_S2MSFB_ID . '_home' , array(
				__CLASS__ ,
				'admin_screen_home' ,
			) );
			$type = 'submenu';
		} else if ( PSK_Tools::current_user_cans( $capablities ) ) {
			add_management_page( PSK_S2MSFB_NAME , PSK_S2MSFB_MENUNAME , 'read' , PSK_S2MSFB_ID . '_home' , array(
				__CLASS__ ,
				'admin_screen_home' ,
			) );
			$type = 'management';
		}

		foreach ( self::$admin_menu as $id => $pos ) {
			if ( is_array( $pos ) ) {
				foreach ( $pos as $pid => $parent ) {
					$pname  = PSK_S2MSFB_NAME . ' &gt; ' . $parent[ 'name' ];
					$pright = ( isset( $parent[ 'right' ] ) ) ? $parent[ 'right' ] : self::$admin_menu_right;
					$pclass = ( isset( $parent[ 'class' ] ) ) ? $parent[ 'class' ] : '';
					if ( isset( $parent[ 'chil' ] ) ) {
						if ( is_array( $parent[ 'chil' ] ) ) {
							foreach ( $parent[ 'chil' ] as $cid => $child ) {
								if ( is_array( $child ) ) {
									$cname  = $pname . ' &gt; ' . $child[ 'name' ];
									$cright = ( isset( $child[ 'right' ] ) ) ? $child[ 'right' ] : $pright;
									if ( current_user_can( PSK_S2MSFB_ADMIN_SETTINGS_ACCESS ) ) {
										add_submenu_page( 'options.php' , __( $cname , PSK_S2MSFB_ID ) , '' , PSK_S2MSFB_ADMIN_SETTINGS_ACCESS , PSK_S2MSFB_ID . '_' . $pid . '_' . $cid , array(
											__CLASS__ . $pclass ,
											'admin_screen_' . $pid . '_' . $cid ,
										) );
									} else {
										$c = PSK_Tools::current_user_cans( $cright );
										if ( $c !== false ) {
											add_submenu_page( 'tools.php' , __( $cname , PSK_S2MSFB_ID ) , '' , $c , PSK_S2MSFB_ID . '_' . $pid . '_' . $cid , array(
												__CLASS__ . $pclass ,
												'admin_screen_' . $pid . '_' . $cid ,
											) );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}


	/**
	 * Return the menu header
	 *
	 * @param $method
	 *
	 * @return string menu as html
	 */
	public static function get_admin_header( $method ) {
		$menu = '<div class="wrap psk">
		<div id="' . PSK_S2MSFB_ID . 'menu" class="navbar navbar-static">
          <div class="navbar-inner">
            <div class="container" style="width: auto;">
              <a class="brand" href="?page=' . PSK_S2MSFB_ID . '_home">' . __( PSK_S2MSFB_NAME , PSK_S2MSFB_ID ) . '</a>';

		foreach ( self::$admin_menu as $id => $pos ) {

			if ( $id == 'left' ) {
				$innerul = ' class="nav" role="navigation"';
			} else if ( $id == 'right' ) {
				$innerul = ' class="nav pull-right"';
			} else {
				$innerul = ' class="nav" role="navigation"';
			}

			$menu .= '<ul' . $innerul . '>';

			foreach ( $pos as $pid => $parent ) {

				$pright = ( isset( $parent[ 'right' ] ) ) ? $parent[ 'right' ] : self::$admin_menu_right;

				if ( PSK_Tools::current_user_cans( $pright ) ) {
					$menu .= '  <li class="dropdown">';
					$menu .= '    <a id="' . PSK_S2MSFB_ID . $pid . '" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">' . __( $parent[ 'name' ] , PSK_S2MSFB_ID ) . '<b class="caret"></b></a>';
					$menu .= '    <ul class="dropdown-menu" role="menu" aria-labelledby="' . PSK_S2MSFB_ID . $pid . '">';

					foreach ( $parent[ 'chil' ] as $cid => $child ) {

						if ( is_array( $child ) ) {
							$cright = ( isset( $child[ 'right' ] ) ) ? $child[ 'right' ] : $pright;
							if ( PSK_Tools::current_user_cans( $cright ) ) {
								$menu .= '<li><a tabindex="-1" href="?page=' . PSK_S2MSFB_ID . '_' . $pid . '_' . $cid . '">' . __( $child[ 'name' ] , PSK_S2MSFB_ID ) . '</a></li>';
							}
						} else if ( $child == '' ) {
							$menu .= '<li class="divider"></li>';
						} else {
							$menu .= '<li class="nav-header">' . $child . '</li>';
						}
					}
					$menu .= '    </ul>';
					$menu .= '  </li>';
				}
			}
			$menu .= '</ul>';
		}
		$menu .= '</div>
          </div>
        </div><div id="psk-alert-area"></div>';

		$menu .= '<h4>' . self::get_admin_screen_title( $method ) . '</h4>';

		return $menu;
	}


	/**
	 * Return the menu footer
	 *
	 * @return      string    menu as html
	 */
	public static function get_admin_footer() {
		return '</div>';
	}


	/**
	 * Admin Screen Home
	 *
	 * @return      string    the screen as html
	 */
	public static function admin_screen_home() {
		echo PSK_S2MSFBAdmin::get_admin_header( __METHOD__ );

		echo PSK_S2MSFBAdmin::get_admin_footer();
	}
}

PSK_S2MSFBAdmin::init();

