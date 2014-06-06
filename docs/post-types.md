# Documentation: Post Types

## Basic syntax

You can create a new custom post type using the `PostType` class. The constructor for which accepts an array containing properties specific to Awesome CPT (these include most of the properties documented below).

Ever post type requires an ID (lowercase letters and underscores only) and the singular and plural variants of the post type name (letters and spaces only). The singular and plural names should be lowercase *in most cases* (special casing is mentioned in the next section).

```
$movie = new Awesome_Post_Type( array(
    'id'   => 'movie',
    'name' => array(
        'singular' => 'movie',
        'plural'   => 'movies'
    )
) );
```

```
$small_group = new Awesome_Post_Type( array(
    'id'   => 'small_group',
    'name' => array(
        'singular' => 'small group',
        'plural'   => 'small groups'
    )
) );
```

Note that internally, the custom post type is initialized when WordPress is initialized (via the `init` action, with a priority of 10).

## Names

Awesome CPT will automatically generate capitalized and title variants based on the `name` array you provide (using `ucfirst()` and `ucwords()`, respectively). For instance, the above `small_group` post type is equivalent to the following:

```
$small_group = new Awesome_Post_Type( array(
    'id'   => 'small_group',
    'name' => array(
        'singular' => 'small group',
        'plural'   => 'small groups'
    ),
    'title' => array(
        'singular' => 'Small Group',
        'plural'   => 'Small Groups'
    ),
    'cap_name' => array(
        'singular' => 'Small group',
        'plural'   => 'Small groups'
    )
) );
```

In most cases, you can let Awesome CPT correctly generate these other name variants. However, for names which require special casing, you may specify these variants yourself.

```
$tv_show = new Awesome_Post_Type( array(
    'id' => 'tv_show',
    'name' => array(
        'singular' => 'TV show',
        'plural'   => 'TV show'
    ),
    'title' => array(
        'singular' => 'TV Show',
        'plural'   => 'TV Show'
    ),
    'cap_name' => array(
        'singular' => 'TV show',
        'plural'   => 'TV show'
    )
) );
```

## Arguments

Awesome CPT also accepts an array of arguments (the same arguments array passed to `register_post_type`) via the `args` property:

```
$movie = new Awesome_Post_Type( array(
    'id'   => 'movie',
    'name' => array(
        'singular' => 'movie',
        'plural'   => 'movies'
    ),
    'args' => array(
        'menu_icon'     => 'dashicons-video-alt2',
        'menu_position' => 20,
        'has_archive'   => 'movies'
    )
) );
```

You can read about these possible arguments via the [WordPress Codex](http://codex.wordpress.org/Function_Reference/register_post_type#Arguments).

Note that all Awesome CPT post types are made public by default (in contrast to the normal WordPress default for `public`).

## Labels

CTP Classes will automatically create labels for your post type based on the `name`, `title`, and `cap_name` arrays. For instance, the first `small_group` example will generate the following labels:

```
'name'               =>  'Small Groups'
'singular_name'      =>  'Small Group'
'add_new'            =>  'Add New'
'add_new_item'       =>  'Add New Small Groups'
'edit_item'          =>  'Edit Small Groups'
'new_item'           =>  'New Small Groups'
'all_items'          =>  'All Small Groups'
'view_item'          =>  'View Small Groups'
'search_items'       =>  'Search Small Groups'
'not_found'          =>  'No small groups found'
'not_found_in_trash' =>  'No small groups found in the Trash'
'parent_item_colon'  => 'Parent Small Groups:',
'menu_name'          => 'Small Groups'
```

However, you can override any of these by specifying them in the `labels` array:

```
$small_group = new Awesome_Post_Type( array(
    'id' => 'tv_show',
    'name' => array(
        'singular' => 'small group',
        'plural'   => 'small groups'
    )
    'args' => array(
        'labels' => array(
            'menu_name' => 'My Small Groups'
        )
    )
) );
```

## Columns

You can add columns to a custom post type's admin screen using the `add_columns()` method. The method accepts an array as its only argument, which in turn accepts a variable number of arrays. Each of these arrays contains properties for each column.

The properties of each column include:

* `id`: required; the ID of the column
* `title`: required; the title of the column to appear in the column header
* `populate`: required; a function which should echo the column value for each post. It receives the post ID as its only parameter.

```
$movie = Awesome_Post_Type( array(
    'id' => 'movie',
    'name' => array(
        'singular' => 'movie',
        'plural'   => 'movies'
    )
) );
$tv_show->add_columns( array(
  array(
      'id'       => 'release_date',
      'title'    => 'Release Date',
      'populate' => function( $post_id ) {
          echo get_post_meta( $post_id, 'release_date', true );
      }
  ),
  array(
      'id'       => 'poster',
      'title'    => 'Poster',
      'populate' => function( $post_id ) {
          echo get_the_post_thumbnail( $post_id, 'thumbnail' );
      }
  )
) );
```

Note that the anonymous functions used above are only supported in PHP 5.3 and newer. For older versions, define your function beforehand, and set the `populate` property to the function's name as a string.

```
...
'populate' => 'my_function_name'
...
```

### Sortable columns

To make a column sortable, simply specify the `meta_key` and `orderby` properties for any given column. The value for the `meta_key` property must be the ID of some meta data stored on the post. The `orderby` property is the generic name of the data by which you are sorting (as shown in the page URL).

```
array(
    'id'       => 'release_date',
    'title'    => 'Release Date',
    'populate' => function( $post_id ) {
        echo get_post_meta( $post_id, 'release_date', true );
    },
    'meta_key' => 'release_date',
    'orderby'  => 'release_date',
    'numeric'  => false
)
```

Setting the `numeric` property's value to `true` will sort the column numerically rather than alphabetically. Contrary to the example above, you only need to list the property if its value is `true`.

### Messages

One of the unique features of Awesome CPT is its ability to automatically generate action messages for your post type. These messages appear when you publish, schedule, or update a post.

If you wish to override these messages, Awesome CPT allows you to hook directly into the `post_updated_messages` filter, like so:

```
function my_post_updated_messages( $messages ) {
    // extend the $messages array here
    return $messages;
}
$movie = new Awesome_Post_Type( array(
    'id'   => 'movie',
    'name' => array(
        'singular' => 'movie',
        'plural'   => 'movies'
    ),
    'post_updated_messages' => 'my_post_updated_messages'
) );
```

## [Read about taxonomies](taxonomies.md)