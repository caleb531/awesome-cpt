<?php
/*
 * Plugin Name: Awesome CPT
 * Description: The easiest and most flexible way to code custom post types in WordPress
 * Author:      Caleb Evans
 * Author URI:  https://calebevans.me/
 * Version:     1.2.0
 * License: GNU General Public License v2.0 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
