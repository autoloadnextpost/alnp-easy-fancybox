<?php
/**
 * Auto Load Next Post: Easy Fancybox - Admin.
 *
 * @since    1.0.0
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Auto Load Next Post: Easy Fancybox/Admin
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ALNP_Easy_Fancybox_Admin' ) ) {

	class ALNP_Easy_Fancybox_Admin {

		/**
		 * Constructor
		 *
		 * @access public
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'includes' ) );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta'), 10, 3 );
		}

		/**
		 * Include any classes we need within admin.
		 *
		 * @access public
		 */
		public function includes() {
			include( dirname( __FILE__ ) . '/class-alnp-easy-fancybox-check.php' );
			include( dirname( __FILE__ ) . '/class-alnp-easy-fancybox-feedback.php' );
		}

		/**
		 * Plugin row meta links
		 *
		 * @access public
		 * @param  array  $links Plugin Row Meta
		 * @param  string $file  Plugin Base file
		 * @param  array  $data  Plugin Information
		 * @return array  $links
		 */
		public function plugin_row_meta( $links, $file, $data ) {
			if ( $file == plugin_basename( ALNP_EASY_FANCYBOX_PLUGIN_FILE ) ) {
				$links[ 1 ] = sprintf( __( 'Developed By %s', 'alnp-easy-fancybox' ), '<a href="' . $data[ 'AuthorURI' ] . '" aria-label="' . esc_attr__( 'View the Developers Site', 'alnp-easy-fancybox' ) . '">' . $data[ 'Author' ] . '</a>' );

				$row_meta = array(
					'community' => '<a href="' . esc_url( ALNP_EASY_FANCYBOX_SUPPORT_URL ) . '" aria-label="' . esc_attr__( 'Get Support from the Community', 'alnp-easy-fancybox' ). '" target="_blank">' . esc_attr__( 'Community Support', 'alnp-easy-fancybox' ) . '</a>',
					'review' => '<a href="' . esc_url( ALNP_EASY_FANCYBOX_REVIEW_URL ) . '" aria-label="' . esc_attr( __( 'Review Auto Load Next Post: Easy Fancybox on WordPress.org', 'alnp-easy-fancybox' ) ) . '" target="_blank">' . __( 'Leave a Review', 'alnp-easy-fancybox' ) . '</a>',
				);

				$links = array_merge( $links, $row_meta );
			}

			return $links;
		}

	}

}

return new ALNP_Easy_Fancybox_Admin();
