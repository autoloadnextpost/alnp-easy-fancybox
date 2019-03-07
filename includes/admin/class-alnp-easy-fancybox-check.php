<?php
/**
 * Auto Load Next Post: Easy Fancybox - Check for Auto Load Next Post Notice.
 *
 * @since    1.0.0
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Auto Load Next Post: Easy Fancybox/Admin/Check
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ALNP_Easy_Fancybox_Check' ) ) {

	class ALNP_Easy_Fancybox_Check {

		/**
		 * Checks if Auto Load Next Post is installed.
		 *
		 * @access public
		 */
		public function __construct() {
			if ( ! defined( 'AUTO_LOAD_NEXT_POST_VERSION' ) || version_compare( AUTO_LOAD_NEXT_POST_VERSION, ALNP_EASY_FANCYBOX_ALNP_REQUIRED, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'alnp_not_installed' ) );
				return false;
			}
		}

		/**
		 * Auto Load Next Post is Not Installed Notice.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return void
		 */
		public function alnp_not_installed() {
			include( dirname( __FILE__ ) . '/views/html-notice-alnp-not-installed.php' );
		}

	} // END class.

} // END if class exists.

return new ALNP_Easy_Fancybox_Check();
