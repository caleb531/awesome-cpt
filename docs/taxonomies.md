# Taxonomies

## General usage

You can create a new taxonomy using the `Awesome_Taxonomy` class. The constructor for which accepts two required arguments:

1. An array containing properties specific to Awesome CPT (these include most of the properties documented below)
2. The arguments array that is normally passed to WordPress's [`register_taxonomy()`](https://codex.wordpress.org/Function_Reference/register_taxonomy) function

Assuming a new instance of `Awesome_Post_Type` (with an `id` of `movie`) has already been created:

```
$genre = new Awesome_Taxonomy( array(
    'id'   => 'genre',
    'name' => array(
        'singular' => 'genre',
        'plural'   => 'genres'
    ),
    'post_types' => array( 'movie' )
), array(
    'hierarchical' => true
) );
```

As you can see, the `post_types` array should contain the `id` of each post type you want to associate with the taxonomy.

## Filterable taxonomies

Awesome CPT allows you to easily make any taxonomy filterable via the `filterable` property. Setting its value to `true` will add a dropdown menu to your post type admin screen, from which you can filter your posts by taxonomy.

To work off the above example of a `genre` taxonomy:

```
$genre = new Awesome_Taxonomy( array(
    'id'   => 'genre',
    'name' => array(
        'singular' => 'genre',
        'plural'   => 'genres'
    ),
    'post_types' => array( 'movie' ),
    'filterable' => true
), array(
    'hierarchical' => true
) );
```

[Read about meta boxes](meta-boxes.md)