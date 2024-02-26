<?php
if ( ! function_exists( 'sws_fs' ) ) {
	// Create a helper function for easy SDK access.
	function sws_fs() {
		global $sws_fs;

		if ( ! isset( $sws_fs ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$sws_fs = fs_dynamic_init( array(
				'id'                  => '15005',
				'slug'                => 'smart-woocommerce-search',
				'premium_slug'        => 'smart-woocommerce-search-pro',
				'type'                => 'plugin',
				'public_key'          => 'pk_2fb41ff7a485924db487b74f6ebe5',
				'is_premium'          => false,
				'premium_suffix'      => 'Pro',
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'menu'                => array(
					'slug'           => 'smart-search',
					'account'        => false,
					'contact'        => false,
					'support'        => false,
				),
			) );
		}

		return $sws_fs;
	}

	// Init Freemius.
	sws_fs();

	sws_fs()->add_filter( 'hide_freemius_powered_by', '__return_true' );

	// Signal that SDK was initiated.
	do_action( 'sws_fs_loaded' );
}

function sws_fs_custom_connect_message_on_update(
	$message,
	$user_first_name,
	$plugin_title,
	$user_login,
	$site_link,
	$freemius_link
) {
	return sprintf(
		__( 'Hey %1$s' ) . ',<br>' .
		__( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'smart-woocommerce-search' ),
		$user_first_name,
		'<b>' . $plugin_title . '</b>',
		'<b>' . $user_login . '</b>',
		$site_link,
		$freemius_link
	);
}

function sws_fs_custom_connect_message(
	$message,
	$user_first_name,
	$plugin_title,
	$user_login,
	$site_link,
	$freemius_link
) {
	return sprintf(
		__( 'Hey %1$s' ) . ',<br>' .
		__( 'Never miss an important update -- opt-in to our security and feature updates notifications, and non-sensitive diagnostic tracking with freemius.com.', 'smart-woocommerce-search' ),
		$user_first_name,
		'<b>' . $plugin_title . '</b>',
		'<b>' . $user_login . '</b>',
		$site_link,
		$freemius_link
	);
}

global $sws_fs;

$sws_fs->add_filter( 'connect_message_on_update', 'sws_fs_custom_connect_message_on_update', 10, 6 );
$sws_fs->add_filter( 'connect_message', 'sws_fs_custom_connect_message', 10, 6 );
