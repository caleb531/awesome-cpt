<?php

// Class for creating custom meta boxes
class Awesome_Meta_Box extends Awesome_Base_Type {

	// Default property values for all meta box fields
	static $field_defaults = array(
		'type'   => 'text',
		'before' => '<p>',
		'after'  => '</p>'
	);

	// Meta Box constructor
	function __construct( $params ) {
		$this->merge_params( $params );
		$this->nonce_id = "{$this->id}-nonce";
		// Post types array defaults to empty array
		if ( empty( $this->post_types ) ) {
			$this->post_types = array();
		}
		// Fields array defaults to empty array
		if ( empty( $this->fields ) ) {
			$this->fields = array();
		}
		// Callback arguments array defaults to empty array
		if ( empty( $this->callback_args ) ) {
			$this->callback_args = array();
		}
		$this->modify_fields();
		// Add actions to make meta boxes functional
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10 );
	}

	// Modify the fields for this meta box
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

	// Action callback for adding meta box for all given post types
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

	// Output the escaped HTML value
	public function echo_value( $before, $value, $after ) {
		echo $before, esc_html( $value ), $after;
	}

	// Retrieve and output the given HTML attributes
	public function echo_attrs( $field, $attrs ) {
		foreach ( $attrs as $attr ) {
			if ( isset( $field[ $attr ] ) ) {
				$this->echo_value( " {$attr}='", $field[ $attr ], "'" );
			}
		}
	}

	// Echo the attributes standard to all field types
	public function echo_std_attrs( $meta_value, $field, $post ) {
		$this->echo_attrs( $field, array( 'id', 'name', 'class', 'placeholder', 'pattern' ) );
		if ( ! empty( $field['required'] ) ) {
			echo " required";
		}
	}

	// Indicate if the given field is selected/checked or not
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
			) || ( ! $meta_value && ! empty( $field[ $attr ] ) ) );
	}

	// Populate a checkbox/radio field
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
		// Output checkbox
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

	// Populate an image input
	public function populate_image( $meta_value, $field, $post ) {
		echo "<input";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'type', 'value', 'src', 'alt' ) );
		echo " />";
	}

	// Populate a select (dropdown) menu
	public function populate_select( $meta_value, $field, $post ) {
		// Output containing select element
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
				// Accept options array returned from the given callback
				$options = call_user_func_array( $field['options'], array( $meta_value, $field, $post ) );
			} elseif ( is_array( $field['options'] ) ) {
				// Otherwise, use options array if given
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
			// If option should be selected
			if ( $this->is_field_selected( $meta_value, $option, 'selected' ) ) {
				// Mark option as selected
				echo " selected";
			}
			// Output option label
			$this->echo_value( ">", $option['content'], "</option>" );
		}
		echo "</select>";
	}


	// Populate a textarea
	public function populate_textarea( $meta_value, $field, $post ) {
		echo "<textarea";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'rows', 'cols' ) );
		// If meta value is not set for this post
		if ( ! empty( $field['content'] ) && '' === $meta_value ) {
			// Output default value
			$this->echo_value( ">", $field['content'], "</textarea>" );
		} else {
			// Otherwise, output retrieved meta value
			$this->echo_value( ">", $meta_value, "</textarea>" );
		}
	}

	// Populate a custom field created by callback function
	public function populate_custom( $meta_value, $field, $post ) {
		if ( ! empty( $field['populate'] ) ) {
			call_user_func_array( $field['populate'], array( $meta_value, $field, $post ) );
		}
	}

	// Populate a regular input field (text, email, etc.)
	public function populate_input( $meta_value, $field, $post ) {
		// Output input element
		echo "<input";
		$this->echo_std_attrs( $meta_value, $field, $post );
		$this->echo_attrs( $field, array( 'type', 'min', 'max', 'step' ) );
		// If meta value is not set for this post
		if ( ! empty( $field['value'] ) && '' === $meta_value ) {
			// Output default value
			$this->echo_value( " value='", $field['value'], "'" );
		} else {
			// Otherwise, output retrieved meta value
			$this->echo_value( " value='", $meta_value, "'" );
		}
		echo " />";
	}

	// Populate a field label
	public function populate_label( $meta_value, $field, $post ) {
		// Output field label if given
		if ( ! empty( $field['label'] ) ) {
			echo "<label";
			$this->echo_value( " for='", $field['id'], "'" );
			$this->echo_value( ">", $field['label'], "</label>" );
		}
	}

	// Populate the given field for the given post
	public function populate_field( $meta_value, $field, $post ) {
		// If filter callback is given
		if ( ! empty( $field['display'] ) ) {
			// Filter displayed meta value
			$meta_value = call_user_func_array( $field['display'], array( $meta_value, $field, $post ) );
		}
		// If custom callback reference is given
		if ( ! empty( $field['populate'] ) ) {
			// Output field using callback
			$this->populate_custom( $meta_value, $field, $post );
		} else {
			// Output HTML to appear before field
			echo $field['before'];
			// Handle all supported input types
			if ( 'checkbox' === $field['type'] || 'radio' === $field['type'] ) {
				$this->populate_checkbox( $meta_value, $field, $post );
				$this->populate_label( $meta_value, $field, $post );
			} else {
				$this->populate_label( $meta_value, $field, $post );
				if ( 'image' === $field['type'] ) {
					$this->populate_image( $meta_value, $field, $post );
				} elseif ( 'select' === $field['type'] ) {
					$this->populate_select( $meta_value, $field, $post );
				} elseif ( 'textarea' === $field['type'] ) {
					$this->populate_textarea( $meta_value, $field, $post );
				} else {
					$this->populate_input( $meta_value, $field, $post );
				}
			}
			// Output HTML to appear after field
			echo $field['after'];
		}
	}

	// Populate meta box with the defined fields
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

	// Get POST variable from given name (supports brackets)
	public function get_post_var( $name ) {
		$key_matches = array();
		// Retrieve key names from the given name attribute
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

	// Save meta data on post save
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
					if ( $meta_value !== null ) {
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
				break;
			}
		}
	}

}
