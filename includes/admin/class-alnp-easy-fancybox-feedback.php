<?php
/**
 * Auto Load Next Post: Easy Fancybox - Feedback Notice
 *
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * Forked from CoBlocks
 * https://github.com/thatplugincompany/coblocks
 *
 * @since    1.0.0
 * @author   Sébastien Dumont
 * @category Admin
 * @package  Auto Load Next Post: Easy Fancybox/Admin/Notices
 * @license  GPL-2.0+
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ALNP_Easy_Fancybox_Feedback' ) ) {
	class ALNP_Easy_Fancybox_Feedback {

		/**
		 * Slug.
		 *
		 * @var string $slug
		 */
		private $slug;

		/**
		 * Name.
		 *
		 * @var string $name
		 */
		private $name;

		/**
		 * Time limit.
		 *
		 * @var string $time_limit
		 */
		private $time_limit;

		/**
		 * No Bug Option.
		 *
		 * @var string $nobug_option
		 */
		public $nobug_option;

		/**
		 * Activation Date Option.
		 *
		 * @var string $date_option
		 */
		public $date_option;

		/**
		 * Class constructor.
		 *
		 * @param string $args Arguments.
		 */
		public function __construct( $args ) {
			$this->slug = $args['slug'];
			$this->name = $args['name'];

			$this->date_option  = $this->slug . '_activation_date';
			$this->nobug_option = $this->slug . '_no_bug';

			if ( isset( $args['time_limit'] ) ) {
				$this->time_limit = $args['time_limit'];
			} else {
				$this->time_limit = WEEK_IN_SECONDS;
			}

			// Add actions.
			add_action( 'admin_init', array( $this, 'check_installation_date' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}

		/**
		 * Seconds to words.
		 *
		 * @access public
		 * @param string $seconds Seconds in time.
		 */
		public function seconds_to_words( $seconds ) {
		 // Get the years.
			$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
			if ( $years > 1 ) {
			 /* translators: Number of years */
				return sprintf( __( '%s years', 'alnp-easy-fancybox' ), $years );
			} elseif ( $years > 0 ) {
				return __( 'a year', 'alnp-easy-fancybox' );
			}

			// Get the weeks.
			$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
			if ( $weeks > 1 ) {
				/* translators: Number of weeks */
				return sprintf( __( '%s weeks', 'alnp-easy-fancybox' ), $weeks );
			} elseif ( $weeks > 0 ) {
				return __( 'a week', 'alnp-easy-fancybox' );
			}

			// Get the days.
			$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
			if ( $days > 1 ) {
				/* translators: Number of days */
				return sprintf( __( '%s days', 'alnp-easy-fancybox' ), $days );
			} elseif ( $days > 0 ) {
				return __( 'a day', 'alnp-easy-fancybox' );
			}

			// Get the hours.
			$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
			if ( $hours > 1 ) {
				/* translators: Number of hours */
				return sprintf( __( '%s hours', 'alnp-easy-fancybox' ), $hours );
			} elseif ( $hours > 0 ) {
				return __( 'an hour', 'alnp-easy-fancybox' );
			}

			// Get the minutes.
			$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
			if ( $minutes > 1 ) {
				/* translators: Number of minutes */
				return sprintf( __( '%s minutes', 'alnp-easy-fancybox' ), $minutes );
			} elseif ( $minutes > 0 ) {
				return __( 'a minute', 'alnp-easy-fancybox' );
			}

			// Get the seconds.
			$seconds = intval( $seconds ) % 60;
			if ( $seconds > 1 ) {
				/* translators: Number of seconds */
				return sprintf( __( '%s seconds', 'alnp-easy-fancybox' ), $seconds );
			} elseif ( $seconds > 0 ) {
				return __( 'a second', 'alnp-easy-fancybox' );
			}
		}

		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 *
		 * @access public
		 */
		public function check_installation_date() {
			if ( ! get_site_option( $this->nobug_option ) || false === get_site_option( $this->nobug_option ) ) {

				add_site_option( $this->date_option, time() );

				// Retrieve the activation date.
				$install_date = get_site_option( $this->date_option );

				// If difference between install date and now is greater than time limit, then display notice.
				if ( ( time() - $install_date ) > $this->time_limit ) {
					add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
				}
			}
		}

		/**
		 * Display the admin notice.
		 *
		 * @access public
		 */
		public function display_admin_notice() {
			$screen = get_current_screen();

			if ( isset( $screen->base ) && 'plugins' === $screen->base ) {
				$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'alnp-feedback-nounce' );
				$time       = $this->seconds_to_words( time() - get_site_option( $this->date_option ) );
			?>

			<style>
			.notice.alnp-feedback-notice {
				border-left-color: #61d8aa !important;
				padding: 20px;
			}
			.rtl .notice.alnp-feedback-notice {
				border-right-color: #61d8aa !important;
			}
			.notice.notice.alnp-feedback-notice .alnp-feedback-notice-inner {
				display: table;
				width: 100%;
			}
			.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-feedback-notice-icon,
			.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-feedback-notice-content,
			.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-install-now {
				display: table-cell;
				vertical-align: middle;
			}
			.notice.alnp-feedback-notice .alnp-feedback-notice-icon {
				color: #509ed2;
				font-size: 50px;
				width: 60px;
			}
			.notice.alnp-feedback-notice .alnp-feedback-notice-icon img {
				width: 64px;
			}
			.notice.alnp-feedback-notice .alnp-feedback-notice-content {
				padding: 0 40px 0 20px;
			}
			.notice.alnp-feedback-notice p {
				padding: 0;
				margin: 0;
			}
			.notice.alnp-feedback-notice h3 {
				margin: 0 0 5px;
			}
			.notice.alnp-feedback-notice .alnp-install-now {
				text-align: center;
			}
			.notice.alnp-feedback-notice .alnp-install-now .alnp-install-button {
				padding: 6px 50px;
				height: auto;
				line-height: 20px;
				background: #61d8aa;
				border-color: #65c8a1 #65c8a1 #65c8a1;
				-webkit-box-shadow: 0 1px 0 #65c8a1;
				box-shadow: 0 1px 0 #65c8a1;
				text-shadow: 0 -1px 1px #3ca179, 1px 0 1px #3ca179, 0 1px 1px #3ca179, -1px 0 1px #3ca179;
			}
			.notice.alnp-feedback-notice .alnp-install-now .alnp-install-button:hover {
				background: #65c8a1;
			}
			.notice.alnp-feedback-notice a.no-thanks {
				display: block;
				margin-top: 10px;
				color: #72777c;
				text-decoration: none;
			}

			.notice.alnp-feedback-notice a.no-thanks:hover {
				color: #444;
			}

			@media (max-width: 767px) {
				.notice.notice.alnp-feedback-notice .alnp-feedback-notice-inner {
					display: block;
				}
				.notice.alnp-feedback-notice {
					padding: 20px !important;
				}
				.notice.alnp-noticee .alnp-feedback-notice-inner {
					display: block;
				}
				.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-feedback-notice-content {
					display: block;
					padding: 0;
				}
				.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-feedback-notice-icon {
					display: none;
				}

				.notice.alnp-feedback-notice .alnp-feedback-notice-inner .alnp-install-now {
					margin-top: 20px;
					display: block;
					text-align: left;
				}

				.notice.alnp-feedback-notice .alnp-feedback-notice-inner .no-thanks {
					display: inline-block;
					margin-left: 15px;
				}
			}
			</style>
			<div class="notice updated alnp-feedback-notice">
				<div class="alnp-feedback-notice-inner">
					<div class="alnp-feedback-notice-icon">
						<?php /* translators: 1. Name */ ?>
						<img src="https://ps.w.org/alnp-easy-fancybox/assets/icon-256x256.png" alt="<?php printf( esc_attr__( '%1$s WordPress Plugin', 'alnp-easy-fancybox' ), esc_attr( $this->name ) ); ?>" />
					</div>
					<div class="alnp-feedback-notice-content">
						<?php /* translators: 1. Name */ ?>
						<h3><?php printf( esc_html__( 'Are you enjoying %1$s?', 'alnp-easy-fancybox' ), esc_html( $this->name ) ); ?></h3>
						<p>
							<?php /* translators: 1. Name, 2. Time */ ?>
							<?php printf( esc_html__( 'You have been using %1$s for %2$s now! Mind leaving a quick review to let me know know what you think? I\'d really appreciate it!', 'alnp-easy-fancybox' ), esc_html( $this->name ), esc_html( $time ) ); ?>
						</p>
					</div>
					<div class="alnp-install-now">
						<?php printf( '<a href="%1$s" class="button button-primary alnp-install-button" target="_blank">%2$s</a>', esc_url( ALNP_ADDON_REVIEW_URL . '#new-post' ), esc_html__( 'Leave a Review', 'alnp-easy-fancybox' ) ); ?>
						<a href="<?php echo esc_url( $no_bug_url ); ?>" class="no-thanks"><?php echo esc_html__( 'No thanks / I already have', 'alnp-easy-fancybox' ); ?></a>
					</div>
				</div>
			</div>
			<?php
			}
		}

		/**
		 * Set the plugin to no longer bug users if user asks not to be.
		 *
		 * @access public
		 */
		public function set_no_bug() {
			// Bail out if not on correct page.
			if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'alnp-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
				return;
			}

			add_site_option( $this->nobug_option, true );
		}
	}

}

/*
 * Instantiate the ALNP_Easy_Fancybox_Feedback class.
 */
new ALNP_Easy_Fancybox_Feedback(
	array(
		'slug'       => 'alnp_addon_name_plugin_feedback',
		'name'       => __( 'Auto Load Next Post: Easy Fancybox', 'alnp-easy-fancybox' ),
		'time_limit' => WEEK_IN_SECONDS,
	)
);
