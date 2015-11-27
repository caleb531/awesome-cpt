<?php

// Class for creating custom post types
class Awesome_Post_Type extends Awesome_Base_Type {

	// register_post_type() argument defaults
	static $arg_defaults = array(
		'public' => true
	);

	public function __construct( $params ) {

		// Call constructor for base class
		parent::__construct( $params, self::$arg_defaults );

		// Initialize arrays used to store column data for post type
		$this->added_cols = array();
		$this->removed_cols = array();
		$this->sortable_cols = array();

		// Register custom post type immediately
		register_post_type( $this->id, $this->args );

		if ( ! empty( $this->post_updated_messages ) ) {
			// Set CPT messages via callback if given
			add_filter( 'post_updated_messages', $this->post_updated_messages, 10 );
		} else {
			// Otherwise, generate 'updated' messages
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ), 10 );
		}

		// Ensure that built-in taxonomies can be registered for post type
		if ( ! empty( $this->args['taxonomies'] ) ) {
			foreach ( $this->args['taxonomies'] as $taxonomy_id ) {
				register_taxonomy_for_object_type( $taxonomy_id, $this->id );
			}
		}

	}

	// Sets post type 'updated' messages (these are displayed when an action is
	// taken on a post of this custom type)
	public function post_updated_messages( $messages ) {

		if ( $this->id === get_post_type() ) {
			$messages[ $this->id ] = $this->create_messages( $messages );
		}

		return $messages;
	}

	// Sets any provided custom columns for this post type
	public function manage_posts_columns( $columns ) {

		if ( $this->id === get_post_type() ) {

			// Add any columns designated as columns to be added
			foreach ( $this->added_cols as $col ) {
				$columns[ $col['id'] ] = $col['title'];
			}
			// Remove any columns designated as columns to removed
			foreach ( $this->removed_cols as $col_id ) {
				unset( $columns[ $col_id ] );
			}

		}

		return $columns;
	}

	// Populates the given custom column for the post type
	public function manage_posts_custom_column( $col_id, $post_id ) {

		if ( $this->id === get_post_type() ) {
			foreach ( $this->added_cols as $col ) {

				if ( $col_id === $col['id'] && ! empty( $col['populate'] ) ) {
					call_user_func_array( $col['populate'], array( $post_id ) );
				}

			}
		}

	}

	// Configures post type's sortable columns based on the supplied parameters
	public function manage_sortable_columns( $sortable_columns ) {

		if ( $this->id === get_post_type() ) {
			foreach ( $this->sortable_cols as $col ) {

				$sortable_columns[ $col['id'] ] = $col['orderby'];

			}
		}

		return $sortable_columns;
	}

	// Enables the post list to be sorted by any sortable column
	public function pre_get_posts( $query ) {

		$orderby = $query->get( 'orderby' );
		$post_type = $query->get( 'post_type' );

		if ( $this->id === $post_type ) {
			foreach ( $this->sortable_cols as $col ) {

				if ( $orderby === $col['orderby'] ) {

					// Optionally sort numeric values
					if ( ! empty( $col['numeric'] ) ) {
						$meta_value_order = 'meta_value_num';
					} else {
						$meta_value_order = 'meta_value';
					}
					// Set sorting parameters
					$query->set( 'meta_key', $col['meta_key'] );
					$query->set( 'orderby', $meta_value_order );
					// Stop as soon as this Edit screen is found to be sorted by
					// a sortable column
					break;
				}

			}
		}

	}

	// Creates labels for post type (displayed on post Edit screens)
	public function create_labels() {

		$labels = array(
			'name'               => sprintf( '%s', $this->title['plural'] ),
			'singular_name'      => sprintf( '%s', $this->title['singular'] ),
			'name'               => sprintf( '%s', $this->title['plural'] ),
			'add_new_item'       => sprintf( 'Add New %s', $this->title['singular'] ),
			'edit_item'          => sprintf( 'Edit %s', $this->title['singular'] ),
			'new_item'           => sprintf( 'New %s', $this->title['singular'] ),
			'all_items'          => sprintf( 'All %s', $this->title['plural'] ),
			'view_item'          => sprintf( 'View %s', $this->title['singular'] ),
			'search_items'       => sprintf( 'Search %s', $this->title['plural'] ),
			'not_found'          => sprintf( 'No %s found', $this->name['plural'] ),
			'not_found_in_trash' => sprintf( 'No %s found in the Trash', $this->name['plural'] ),
			'parent_item_colon'  => sprintf( 'Parent %s:', $this->title['plural'] ),
			'menu_name'          => sprintf( '%s', $this->title['plural'] )
		);

		return $labels;
	}

	// Sets action messages for post type (displayed on post Edit screens)
	public function create_messages( $messages ) {
		global $post;

		$messages = array(
			0  => '',
			1  => sprintf( '%s updated. <a href="%s">View %s</a>', $this->cap_name['singular'], esc_url( get_permalink( $post->ID ) ), $this->name['singular'] ),
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => sprintf( '%s updated.', $this->cap_name['singular'] ),
			5  => isset( $_GET['revision'] ) ? sprintf( '%s restored to revision from %s', $this->cap_name['singular'], wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6  => sprintf( '%s published. <a href="%s">View %s</a>', $this->cap_name['singular'], esc_url( get_permalink( $post->ID ) ), $this->name['singular'] ),
			7  => sprintf( '%s saved.', $this->cap_name['singular'] ),
			8  => sprintf( '%s submitted. <a target="_blank" href="%s">Preview %s</a>', $this->cap_name['singular'], esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), $this->name['singular'] ),
			9  => sprintf( '%s scheduled for: <strong>%s</strong>. <a target="_blank" href="%s">Preview %s</a>', $this->cap_name['singular'], date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ), $this->name['singular'] ),
			10 => sprintf( '%s draft updated. <a target="_blank" href="%s">Preview %s</a>', $this->cap_name['singular'], esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), $this->name['singular'] ),
		);

		return $messages;
	}

	// Adds the custom columns with the the given parameters
	public function add_columns( $columns ) {

		foreach ( $columns as $col ) {
			// Add column to list of added columns
			array_push( $this->added_cols, $col );
			if ( ! empty( $col['orderby'] ) && ! empty( $col['meta_key'] ) ) {
				// Add column to list of sortable columns if column is
				// designated as sortable
				array_push( $this->sortable_cols, $col );
			}
		}

		// Bind filters/actions for non-sortable custom columns
		add_filter( 'manage_posts_columns', array( $this, 'manage_posts_columns' ), 5 );
		add_action( 'manage_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 5, 2 );

		// If at least one column has been made sortable
		if ( 0 !== count( $this->sortable_cols ) ) {
			// Bind filters/actions for sortable columns
			add_filter( "manage_edit-{$this->id}_sortable_columns", array( $this, 'manage_sortable_columns' ), 10 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10 );
		}

		return $this;
	}

	// Removes columns with the given IDs
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
