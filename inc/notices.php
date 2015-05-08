<?php

add_action( 'admin_notices', 'faqss_admin_notices' );
function faqss_admin_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	$activationDate = get_user_meta( $user_id, 'faqss_plugin_activation' );
	$activationDateVar = $activationDate[0];
	$aWeekFromActivation = strtotime( $activationDateVar . '+1 week' );
	$twoWeeksFromActivation = strtotime( $activationDateVar . '+2 weeks' );
	$currentPluginDate = strtotime( 'now' );

	$rateOutput = '<div id="message" class="updated notice">';
		$rateOutput .= '<p>Please rate <a href="https://wordpress.org/plugins/faqs-shortcode/" target="_blank">FAQs Shortcode</a>. If you have already, simply <a href="?faqss_rate_ignore=dismiss">dismiss this notice</a>.</p>';
	$rateOutput .= '</div>';

	$donateOutput = '<div id="message" class="updated notice">';
		$donateOutput .= '<p>Looks like you\'re enjoying <a href="https://wordpress.org/plugins/faqs-shortcode/" target="_blank">FAQs Shortcode</a>. Consider <a href="" target="_blank">making a donation</a>, alternatively <a href="?faqss_donate_ignore=dismiss">dismiss this notice</a>.</p>';
	$donateOutput .= '</div>';

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'faqss_rate_ignore' ) != 'true' && $currentPluginDate >= $aWeekFromActivation ){
		echo $rateOutput;
	}

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'faqss_donate_ignore' ) != 'true' && $currentPluginDate >= $twoWeeksFromActivation ){
		echo $donateOutput;
	}
}

add_action( 'admin_init', 'faqss_ignore_notices' );
function faqss_ignore_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	if( isset( $_GET['faqss_rate_ignore'] ) && $_GET['faqss_rate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'faqss_rate_ignore', 'true' );
	}

	if( isset( $_GET['faqss_donate_ignore'] ) && $_GET['faqss_donate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'faqss_donate_ignore', 'true' );
	}
}
