<?php

// Base class for post types, taxonomies, and meta boxes
abstract class Awesome_Base_Type {

	// Base constructor for CPTs and taxonomies
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
		// If labels were passed in parameters
		if ( ! empty( $this->args['labels'] ) ) {
			// Merge labels with defaults
			$this->args['labels'] = array_merge( $default_labels, $this->args['labels'] );
		} else {
			$this->args['labels'] = $default_labels;
		}
		// If contextual help function is given
		if ( ! empty( $this->contextual_help ) ) {
			// Add contextual help to type
			add_action( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );
		}
	}

	// Add contextual help for type
	public function contextual_help( $contextual_help, $screen_id, $screen ) {
		// If contextual help callback was given
		if ( $this->contextual_help ) {
			// Call function
			call_user_func_array( $this->contextual_help, array( $contextual_help, $screen_id, $screen ) );
		}
	}

	// Merge params into class instance
	public function merge_params( $params ) {
		foreach ( $params as $param_name => $param_value ) {
			$this->$param_name = $param_value;
		}
	}

	// Compute other name variants for the type
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
