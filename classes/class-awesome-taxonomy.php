<?php

// Class for creating custom taxonomies
class Awesome_Taxonomy extends Awesome_Base_Type {

	// register_taxonomy() argument defaults
	static $arg_defaults = array(
		'public' => true
	);

	// Taxonomy constructor
	public function __construct( $params ) {
		// Call parent class constructor
		parent::__construct( $params, self::$arg_defaults );

		// Initialize taxonomy immediately
		$this->init();

		// If taxonomy is designated as filterable
		if ( ! empty( $this->filterable ) ) {
			// Make taxonomy filterable
			add_action( 'restrict_manage_posts', array( $this, 'restrict_taxonomy' ), 10 );
		}
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
		$terms = get_terms( $this->id, array(
			'hide_empty' => false
		) );
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
				<?php break; ?>
			<?php endif; ?>
		<?php endforeach;
	}

	// Create labels for custom post type
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
