<?php
/*
 * Plugin Name: Awesome CPT
 * Description: The easiest and most flexible way to code custom post types in WordPress
 * Author:      Caleb Evans
 * Author URI:  http://calebevans.me/
 * Version:     1.0
 * License:     GPL2
 * Copyright 2014  Caleb Evans  (email : calebevans.me@gmail.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
 
// Define path to AwesomeCPT directory
if ( ! defined( 'AWESOME_CPT_DIR' ) ) {
	define( 'AWESOME_CPT_DIR', dirname( __FILE__ ) );
}
// Define constant indicating the existence of AwesomeCPT
if ( ! defined( 'AWESOME_CPT' ) ) {
	define( 'AWESOME_CPT', true );
}

// Import AwesomeCPT classes
require_once AWESOME_CPT_DIR . '/classes/class-awesome-base-type.php';
require_once AWESOME_CPT_DIR . '/classes/class-awesome-post-type.php';
require_once AWESOME_CPT_DIR . '/classes/class-awesome-taxonomy.php';
require_once AWESOME_CPT_DIR . '/classes/class-awesome-meta-box.php';