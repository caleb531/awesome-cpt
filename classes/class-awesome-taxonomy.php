<?php

// Class for creating custom taxonomies
class Awesome_Taxonomy extends Awesome_Base_Type {

	// register_taxonomy() argument defaults
	static $arg_defaults = array(
		'public' => true
	);

	public function __construct( $params ) {

		// Call constructor for base class
		parent::__construct( $params, self::$arg_defaults );

		// Register custom taxonomy immediately
		register_taxonomy(
			$this->id,
			$this->post_types,
			$this->args
		);

		// Make taxonomy filterable if designated as such
		if ( ! empty( $this->filterable ) ) {
			add_action( 'restrict_manage_posts', array( $this, 'restrict_taxonomy' ), 10 );
		}

	}

	// Adds dropdown to filter posts by taxonomy term
	public function restrict_taxonomy() {
		// The post type pertaining to the current Edit screen
		global $typenow;

		$terms = get_terms( $this->id );
		?>

		<?php foreach ( $this->post_types as $post_type_id ): ?>

			<?php $is_empty = empty( $_GET[ $this->id ] ); ?>

			<?php if ( $typenow === $post_type_id ): ?>

				<select name="<?php echo $this->id; ?>" class='postform'>

					<option value=''>View all <?php echo $this->name['plural']; ?></option>

					<?php foreach ( $terms as $term ): ?>

						<?php if ( ! $is_empty && $_GET[ $this->id ] === $term->slug ): ?>

							<option value='<?php echo $term->slug; ?>' selected><?php echo $term->name; ?></option>

						<?php else: ?>

							<option value='<?php echo $term->slug; ?>'><?php echo $term->name; ?></option>

						<?php endif; ?>

					<?php endforeach; ?>

				</select>

				<?php
				// Stop as soon as the current Edit screen's post type is found
				// to be supported by this custom taxonomy
				break;
				?>

			<?php endif; ?>

		<?php endforeach;

	}

	// Creates labels for taxonomy (displayed on taxonomy Edit screens)
	public function create_labels() {

		$labels = array(
			'name'              => sprintf( '%s', $this->title['plural'] ),
			'singular_name'     => sprintf( '%s', $this->title['singular'] ),
			'search_items'      => sprintf( 'Search %s', $this->title['plural'] ),
			'all_items'         => sprintf( 'All %s', $this->title['plural'] ),
			'parent_item'       => sprintf( 'Parent %s', $this->title['singular'] ),
			'parent_item_colon' => sprintf( 'Parent %s:', $this->title['singular'] ),
			'edit_item'         => sprintf( 'Edit %s', $this->title['singular'] ),
			'update_item'       => sprintf( 'Update %s', $this->title['singular'] ),
			'add_new_item'      => sprintf( 'Add New %s', $this->title['singular'] ),
			'new_item_name'     => sprintf( 'New %s', $this->title['singular'] ),
			'menu_name'         => sprintf( '%s', $this->title['plural'] )
		);
		return $labels;

	}

}
