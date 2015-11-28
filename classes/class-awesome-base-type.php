<?php

// Base class for post types, taxonomies, and meta boxes
abstract class Awesome_Base_Type {

	// The base constructor is only called for post types and taxonomies
	public function __construct( $params, $arg_defaults ) {

		$this->merge_params( $params );

		// Merge argument defaults into arguments array
		if ( empty( $this->args ) ) {
			$this->args = $arg_defaults;
		} else {
			$this->args = array_merge( $arg_defaults, $this->args );
		}

		$this->add_names();
		$default_labels = $this->create_labels();

		if ( ! empty( $this->args['labels'] ) ) {
			// Merge labels with defaults if custom labels were supplied
			$this->args['labels'] = array_merge( $default_labels, $this->args['labels'] );
		} else {
			// Otherwise, use default labels
			$this->args['labels'] = $default_labels;
		}

		// Add contextual help menus to page if provided
		if ( ! empty( $this->help_menus ) ) {
			add_action( 'admin_head', array( $this, 'add_help_menu', ), 10 );
		}

	}

	// Adds the contextual help menu assigned to the current screen
	public function add_help_menu() {

		$screen = get_current_screen();

		foreach ( $this->help_menus as $help_menu ) {

			if ( $screen->id === $help_menu['screen'] ) {

				foreach ( $help_menu['tabs'] as $tab ) {
					$screen->add_help_tab( $tab );
				}

				if ( ! empty( $help_menu['sidebar'] ) ) {
					$screen->set_help_sidebar( $help_menu['sidebar'] );
				}

			}

		}

	}

	// Merges params into class instance
	public function merge_params( $params ) {

		foreach ( $params as $param_name => $param_value ) {
			$this->$param_name = $param_value;
		}

	}

	// Computes other name variants for the type
	public function add_names() {

		// Construct title from the given name
		if ( empty( $this->title ) ) {
			$this->title = array(
				'singular' => ucwords( $this->name['singular'] ),
				'plural'   => ucwords( $this->name['plural'] ),
			);
		}

		// Construct capitalized name from the given name
		if ( empty( $this->cap_name ) ) {
			$this->cap_name = array(
				'singular' => ucfirst( $this->name['singular'] ),
				'plural'   => ucfirst( $this->name['plural'] ),
			);
		}

	}

}
