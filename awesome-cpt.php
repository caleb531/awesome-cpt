<?php
/*
 * Plugin Name: Awesome CPT
 * Description: The easiest and most flexible way to code custom post types in WordPress
 * Author:      Caleb Evans
 * Author URI:  http://calebevans.me/
 * Version:     1.0.2
 * License:     MIT
 */

// The AWESOME_CPT constant indicates whether Awesome CPT has been loaded
if ( ! defined( 'AWESOME_CPT' ) ) {

	define( 'AWESOME_CPT_DIR', dirname( __FILE__ ) );
	define( 'AWESOME_CPT', true );

	// Import AwesomeCPT classes
	require_once AWESOME_CPT_DIR . '/classes/class-awesome-base-type.php';
	require_once AWESOME_CPT_DIR . '/classes/class-awesome-post-type.php';
	require_once AWESOME_CPT_DIR . '/classes/class-awesome-taxonomy.php';
	require_once AWESOME_CPT_DIR . '/classes/class-awesome-meta-box.php';

}
