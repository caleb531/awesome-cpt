<?php

// Class for creating custom taxonomies
class Awesome_Taxonomy extends Awesome_Base_Type {
	
	// register_taxonomy() argument defaults
	static $arg_defaults = array(
		'public' => true
	);
		
	// Taxonomy constructor
	public function __construct( $params ) {
		// Call Awesome_Base_Type constructor
		parent::__construct( $params, self::$arg_defaults );
		if ( ! empty( $this->filterable ) ) {
			add_action( 'restrict_manage_posts', array( $this, 'restrict_taxonomy' ), 10 );
		}
		// Initialize taxonomy
		add_action( 'init', array( $this, 'init' ), 10 );
	}
	
	// Initialize taxonomy
	public function init() {
		register_taxonomy(
			$this->id,
			$this->post_types,
			$this->args
		);
	}
	
	// Add dropdown to filter posts by taxonomy term
	public function restrict_taxonomy() {
		global $typenow;
		foreach ( $this->post_types as $post_type_id ) {
			if ( $typenow === $post_type_id ) {
				// Output HTML for term filter dropdown
				echo "<select name='$this->id' class='postform'>";
				echo "<option value=''>View all {$this->name['plural']}</option>";
				// Retrieve list of all terms
				$terms = get_terms( $this->id );
				foreach ( $terms as $term ) {
					echo "<option value='$term->slug'";
					// If term is currently being filtered
					if ( ! empty( $_GET[ $this->id ] ) && $_GET[ $this->id ] === $term->slug ) {
						// Select the corresponding option
						echo " selected";
					}
					echo ">$term->name</option>";
				}
				echo "</select>";
				break;
			}
		}
	}
	
	// Create labels for custom post type
	public function create_labels() {
		$labels = array(
			'name' => __( "{$this->title['plural']}" ),
			'singular_name' => __( "{$this->title['singular']}" ),
			'search_items' => __( "Search {$this->title['plural']}" ),
			'all_items' => __( "All {$this->title['plural']}" ),
			'parent_item' => __( "Parent {$this->title['singular']}" ),
			'parent_item_colon' => __( "Parent {$this->title['singular']}:" ),
			'edit_item' => __( "Edit {$this->title['singular']}" ), 
			'update_item' => __( "Update {$this->title['singular']}" ),
			'add_new_item' => __( "Add New {$this->title['singular']}" ),
			'new_item_name' => __( "New {$this->title['singular']}" ),
			'menu_name' => __( "{$this->title['plural']}" ),
		);
		return $labels;
	}
		
}
