<?php

// Class for custom post types
class Awesome_Post_Type extends Awesome_Base_Type {
	
	// register_post_type() argument defaults
	static $arg_defaults = array(
		'public' => true
	);
	
	// Post Type constructor
	public function __construct( $params ) {
		// Call Awesome_Base_Type constructor
		parent::__construct( $params, self::$arg_defaults );
		// Initialize CPT columns
		$this->added_cols = array();
		$this->removed_cols = array();
		$this->sortable_cols = array();
		
		// Initialize CPT
		add_action( 'init', array( $this, 'init' ), 10 );
		
		if ( ! empty( $this->post_updated_messages ) ) {
			// Set CPT messages via callback if given
			add_filter( 'post_updated_messages', $this->post_updated_messages, 10 );
		} else {
			// Otherwise, generate messages
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ), 10 );
		}
	}
	
	// Initialize CPT
	public function init() {
		register_post_type( $this->id, $this->args );
	}
	// Set CPT messages
	public function post_updated_messages( $messages ) {
		if ( $this->id === get_post_type() ) {
			$messages[ $this->id ] = $this->create_messages( $messages );
		}
		return $messages;
	}
	// Set CPT custom columns
	public function manage_posts_columns( $columns ) {
		if ( $this->id === get_post_type() ) {
			// Add designated columns
			foreach ( $this->added_cols as $col ) {
				$columns[ $col['id'] ] = $col['title'];
			}
			// Remove designated columns
			foreach ( $this->removed_cols as $col_id ) {
				unset( $columns[ $col_id ] );
			}
		}
		return $columns;
	}
	// Populate CPT custom columns
	public function manage_posts_custom_column( $col_id, $post_id ) {
		if ( $this->id === get_post_type() ) {
			foreach ( $this->added_cols as $col ) {
				if ( $col_id === $col['id'] && $col['populate'] ) {
					call_user_func_array( $col['populate'], array( $post_id ) );
				}
			}
		}
	}
	// Manage CPT sortable columns
	public function manage_sortable_columns( $columns ) {
		if ( $this->id === get_post_type() ) {
			foreach ( $this->sortable_cols as $col ) {
				$columns[ $col['id'] ] = $col['orderby'];
			}
		}
		return $columns;
	}
	// Enable filter page for sortable columns
	public function pre_get_posts( $query ) {
		$orderby = $query->get( 'orderby' );
		$post_type = $query->get( 'post_type' );
		if ( $this->id === $post_type ) {
			foreach ( $this->sortable_cols as $col ) {
				if ( $orderby === $col['orderby'] ) {
					// Optionally sort numeric values
					$meta_value_order = $col['numeric'] ? 'meta_value_num' : 'meta_value';
					// Set sorting parameters
					$query->set( 'meta_key', $col['meta_key'] );
					$query->set( 'orderby', $meta_value_order );
					break;
				}
			}
		}
	}
	
	// Create labels for custom post type
	public function create_labels() {
		$labels = array(
			'name' => __( "{$this->title['plural']}" ),
			'singular_name' => __( "{$this->title['singular']}" ),
			'name' => __( "{$this->title['plural']}" ),
			'add_new_item' => __( "Add New {$this->title['singular']}" ),
			'edit_item' => __( "Edit {$this->title['singular']}" ),
			'new_item' => __( "New {$this->title['singular']}" ),
			'all_items' => __( "All {$this->title['plural']}" ),
			'view_item' => __( "View {$this->title['singular']}" ),
			'search_items' => __( "Search {$this->title['plural']}" ),
			'not_found' => __( "No {$this->name['plural']} found" ),
			'not_found_in_trash' => __( "No {$this->name['plural']} found in the Trash" ),
			'parent_item_colon' => "Parent {$this->title['plural']}:",
			'menu_name' => __( "{$this->title['plural']}" )
		);
		return $labels;
	}
	
	// Set action messages for custom post type
	public function create_messages( $messages ) {
		global $post;
		$messages = array(
			0 => "", 
			1 => sprintf( __( "{$this->cap_name['singular']} updated. <a href='%s'>View {$this->name['singular']}</a>" ), esc_url( get_permalink( $post->ID ) ) ),
			2 => __( "Custom field updated." ),
			3 => __( "Custom field deleted." ),
			4 => __( "{$this->cap_name['singular']} updated." ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( "{$this->cap_name['singular']} restored to revision from %s" ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( "{$this->cap_name['singular']} published. <a href='%s'>View {$this->name['singular']}</a>" ), esc_url( get_permalink( $post->ID ) ) ),
			7 => __( "{$this->cap_name['singular']} saved." ),
			8 => sprintf( __( "{$this->cap_name['singular']} submitted. <a target='_blank' href='%s'>Preview {$this->name['singular']}</a>" ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			9 => sprintf( __( "{$this->cap_name['singular']} scheduled for: <strong>%1\$s</strong>. <a target='_blank' href='%2\$s'>Preview {$this->name['singular']}</a>" ), date_i18n( __( "M j, Y @ G:i" ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
			10 => sprintf( __( "{$this->cap_name['singular']} draft updated. <a target='_blank' href='%s'>Preview {$this->name['singular']}</a>" ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		);
		return $messages;
	}
	
	// Add the columns with the the given IDs
	public function add_columns( $columns ) {
		foreach ( $columns as $col ) {
			// Add column to list of added columns
			array_push( $this->added_cols, $col );
			// If column should be sortable
			if ( ! empty( $col['orderby'] ) && ! empty( $col['meta_key'] ) ) {
				// Designate column as sortable
				array_push( $this->sortable_cols, $col );
			}
		}
		add_filter( 'manage_posts_columns', array( $this, 'manage_posts_columns' ), 5 );
		add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 5, 2 );
		// If at least one column has been made sortable
		if ( count ( $this->sortable_cols ) !== 0 ) {
			// Bind filters/actions for sortable columns
			add_filter( "manage_edit-{$this->id}_sortable_columns", array( $this, 'manage_sortable_columns' ), 10 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10 );
		}
		return $this;
	}
	
	// Remove columns with the given IDs
	public function remove_columns( $col_ids ) {
		foreach ( $col_ids as $col_id ) {
			// If column doesn't exist in list of removed columns
			 if ( false === array_search( $col_id, $this->removed_cols ) ) {
				// Add column to list of removed columns
				array_push( $this->removed_cols, $col_id );
			}
		}
		return $this;
	}
	
}
