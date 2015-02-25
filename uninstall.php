<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' )) {
		exit;
	}
	
	if ( get_option('ehive_account_details_options') != false ) {
		delete_option('ehive_account_details_options');
	}