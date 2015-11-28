<?php

// Class for creating custom meta boxes
class Awesome_Meta_Box extends Awesome_Base_Type {

	// Default property values for all meta box fields
	static $field_defaults = array(
		'type'   => 'text',
		'before' => '<p>',
		'after'  => '</p>'
	);

	// Map each essential field type to a class function that constructs it
	static $field_callbacks = array(
		'select'   => 'populate_select',
		'textarea' => 'populate_textarea',
		'select'   => 'populate_select',
		'image'    => 'populate_image',
		'checkbox' => 'populate_checkbox',
		'radio'    => 'populate_checkbox',
		'text'     => 'populate_input',
		'default'  => 'populate_input'
	);

	// Standard attributes available to any form element
	static $std_attrs = array( 'id', 'name', 'class', 'placeholder', 'pattern' );

	function __construct( $params ) {

		$this->merge_params( $params );
		$this->nonce_id = "{$this->id}-nonce";

		// Initialize array-type properties if they're not supplied
		if ( empty( $this->post_types ) ) {
			$this->post_types = array();
		}
		if ( empty( $this->fields ) ) {
			$this->fields = array();
		}
		if ( empty( $this->callback_args ) ) {
			$this->callback_args = array();
		}

		// Merge field defaults into every field
		$this->modify_fields();

		// Bind actions to make meta boxes functional
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10 );

	}

	// Modifies the given meta box fields by supplying defaults
	public function modify_fields() {

		foreach ( $this->fields as &$field ) {
			// Field name defaults to field ID
			if ( empty( $field['name'] ) ) {
				$field['name'] = $field['id'];
			}
			// Merge field defaults with field
			$field = array_merge( self::$field_defaults, $field );
			// Add field to list of all fields with same name
			$this->field_names[ $field['name'] ][] = $field;
		}

	}

	// Register this meta box for all assigned post types
	public function add_meta_boxes() {

		foreach ( $this->post_types as $post_type_id ) {
			add_meta_box(
				$this->id,
				$this->title,
				array( $this, 'populate_fields' ),
				$post_type_id,
				$this->context,
				$this->priority,
				$this->callback_args
			);
		}

	}

	// Outputs the escaped HTML value (between optional before/after text)
	public function echo_value( $before, $value, $after ) {

		echo $before, esc_html( $value ), $after;

	}

	// Retrieves, escapes, and outputs the given HTML attributes
	public function echo_attrs( $field, $attrs ) {

		foreach ( $attrs as $attr ) {
			if ( isset( $field[ $attr ] ) ) {
				$this->echo_value( " {$attr}='", $field[ $attr ], "'" );
			}
		}

	}

	// Outputs the attributes standard to all field types
	public function echo_std_attrs( $meta_value, $field, $post ) {

		$this->echo_attrs( $field, self::$std_attrs );
		// The 'required' attribute is special because it has no value
		if ( ! empty( $field['required'] ) ) {
			echo " required";
		}

	}

	// Returns a boolean indicating if the given field is selected/checked
	public function is_field_selected( $meta_value, $field, $attr ) {

		return (
			(
				$meta_value && ! empty( $field['value'] ) &&
				(
					$meta_value === $field['value'] ||
					(
						is_array( $meta_value ) &&
						in_array( $field['value'], $meta_value )
					)
				)
			) || ( ! $meta_value && ! empty( $field[ $attr ] ) )
		);

	}

	// Outputs the HTML for a checkbox/radio field
	public function populate_checkbox( $meta_value, $field, $post ) {

		// If this is the first field with this particular name
		if ( $this->field_names[ $field['name'] ][0] === $field ) {
			// Create hidden input to make unchecked checkbox visible to PHP
			$this->populate_input( 'off', array_merge( $field, array(
				'id'    => null,
				'label' => null,
				'type'  => 'hidden'
			) ), $post );
		}

		// Output checkbox element
		echo "<input";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'type', 'value' ) );
		// If field should be checked
		if ( $this->is_field_selected( $meta_value, $field, 'checked' ) ) {
			// Mark input as checked if necessary
			echo " checked";
		}
		echo " />";

	}

	// Outputs the HTML for an image input
	public function populate_image( $meta_value, $field, $post ) {

		echo "<input";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'type', 'value', 'src', 'alt' ) );
		echo " />";

	}

	// Outputs the HTML for a select (dropdown) menu
	public function populate_select( $meta_value, $field, $post ) {

		// Output select element
		echo "<select";
		$this->echo_std_attrs( $meta_value, $field, $post );
		// If select field allows for multiple values
		if ( ! empty( $field['multiple'] ) ) {
			// Enable multiple selections on element
			echo " multiple";
		}
		echo ">";

		// If value for options field is given
		if ( ! empty( $field['options'] ) ) {
			if ( is_callable( $field['options'] ) ) {
				// Accept options array returned from the given callback (if
				// 'options' is a callback)
				$options = call_user_func_array( $field['options'], array( $meta_value, $field, $post ) );
			} elseif ( is_array( $field['options'] ) ) {
				// Otherwise, assume 'options' in an array
				$options = $field['options'];
			}
		} else {
			// The options array is empty by default
			$options = array();
		}

		// Output each option element
		foreach ( $options as $option ) {
			echo "<option";
			$this->echo_attrs( $option, array( 'value', 'class' ) );
			// If option should be selected, add attribute
			if ( $this->is_field_selected( $meta_value, $option, 'selected' ) ) {
				echo " selected";
			}
			// Output content of option element
			$this->echo_value( ">", $option['content'], "</option>" );
		}

		echo "</select>";

	}


	// Outputs the HTML for a multiline textarea element
	public function populate_textarea( $meta_value, $field, $post ) {

		echo "<textarea";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'rows', 'cols' ) );

		// If meta value is not set for this post, output default value
		if ( ! empty( $field['content'] ) && '' === $meta_value ) {
			$this->echo_value( ">", $field['content'], "</textarea>" );
		} else {
			// Otherwise, output retrieved meta value
			$this->echo_value( ">", $meta_value, "</textarea>" );
		}

	}

	// Outputs the HTML for a custom field determined by the given callback
	public function populate_custom( $meta_value, $field, $post ) {

		if ( ! empty( $field['populate'] ) ) {
			call_user_func_array( $field['populate'], array( $meta_value, $field, $post ) );
		}

	}

	// Outputs the HTML for a regular input field (text, email, etc.)
	public function populate_input( $meta_value, $field, $post ) {

		// Output input element
		echo "<input";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'type', 'min', 'max', 'step' ) );

		// If meta value is not set for this post, output default value
		if ( ! empty( $field['value'] ) && '' === $meta_value ) {
			$this->echo_value( " value='", $field['value'], "'" );
		} else {
			// Otherwise, output retrieved meta value
			$this->echo_value( " value='", $meta_value, "'" );
		}
		echo " />";

	}

	// Outputs the HTML for a field label
	public function populate_label( $meta_value, $field, $post ) {

		if ( ! empty( $field['label'] ) ) {
			echo "<label";
			$this->echo_value( " for='", $field['id'], "'" );
			$this->echo_value( ">", $field['label'], "</label>" );
		}

	}

	// Outputs the HTML for the given field for the given post
	public function populate_field( $meta_value, $field, $post ) {

		// If filter callback is given
		if ( ! empty( $field['display'] ) ) {
			// Filter displayed meta value
			$meta_value = call_user_func_array( $field['display'], array( $meta_value, $field, $post ) );
		}

		if ( ! empty( $field['populate'] ) ) {

			// If custom callback reference is given, use it to output HTML
			$this->populate_custom( $meta_value, $field, $post );

		} else {
			// Otherwise, output HTML based on supplied parameters

			// Output any custom HTML to appear before field
			echo $field['before'];
			if ( 'checkbox' === $field['type'] || 'radio' === $field['type'] ) {
				// Label should appear after checkboxes and radio buttons

				call_user_func_array( array( $this, self::$field_callbacks[ $field['type'] ] ), array( $meta_value, $field, $post ) );
				$this->populate_label( $meta_value, $field, $post );

			} else {
				// For all other field types, label should precede input

				$this->populate_label( $meta_value, $field, $post );
				if ( ! empty( $field['type'] ) ) {
					call_user_func_array( array( $this, self::$field_callbacks[ $field['type'] ] ), array( $meta_value, $field, $post ) );
				} else {
					call_user_func_array( array( $this, self::$field_callbacks[ 'default' ] ), array( $meta_value, $field, $post ) );
				}

			}
			// Output any custom HTML to appear after field
			echo $field['after'];

		}
	}

	// Outputs HTML for all fields for this meta box
	public function populate_fields( $post ) {

		// Add nonce field for security
		wp_nonce_field( plugin_basename( __FILE__ ), $this->nonce_id );
		// Output each field
		foreach ( $this->fields as $field ) {
			// Retrieve meta data for this field
			$meta_value = get_post_meta( $post->ID, $field['name'], true );
			// Populate the given field with the given value
			$this->populate_field( $meta_value, $field, $post );
		}

	}

	// Retrieves POST variable from given name (supports nesting with [])
	public function get_post_var( $name ) {

		// Retrieve key names from the given name attribute
		$key_matches = array();
		preg_match_all( '/[^\[\]]+/', $name, $key_matches );

		// If there is at least one key in name
		if ( count( $key_matches ) !== 0 ) {

			// Retrieve list of key names from matches
			$key_names = $key_matches[0];
			// Search for POST value based on hierarchy
			$val = $_POST;
			foreach ( $key_names as $key_name ) {
				if ( isset( $val[ $key_name ] ) ) {
					$val = $val[ $key_name ];
				} else {
					$val = null;
				}
			}

		}

		return $val;
	}

	// Saves meta data on post save
	public function save_post( $post_id ) {
		global $post;

		// Do not save if autosave is in progress
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Do not save if nonce field does not validate
		if ( ! empty( $_POST[ $this->nonce_id ] ) && ! wp_verify_nonce( $_POST[ $this->nonce_id ], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// Do not save if unprivileged user edits post
		if ( ! empty( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		// Loop through all associated post types
		foreach ( $this->post_types as $post_type_id ) {
			// If current post type is an associated post type
			if ( ! empty( $_POST['post_type'] ) && $_POST['post_type'] === $post_type_id ) {
				// Save each field to database
				foreach ( $this->fields as $field ) {

					$meta_value = $this->get_post_var( $field['name'] );
					if ( null !== $meta_value ) {

						// If filter callback is given
						if ( ! empty( $field['save'] ) ) {
							// Filter saved meta value
							$meta_value = call_user_func_array( $field['save'], array( $meta_value, $field, $post ) );
						}

						// If value has changed
						if ( $meta_value !== get_post_meta( $post_id, $field['name'], true ) ) {
							// Update database value for field
							update_post_meta( $post_id, $field['name'], $meta_value );
						}

					}

				}
				// Stop as soon as this meta box is found to support the page's
				// current post type
				break;
			}
		}
	}

}
