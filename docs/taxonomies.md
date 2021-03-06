# Documentation: Taxonomies

## General usage

You can create a new taxonomy using the `Awesome_Taxonomy` class. The
constructor for which accepts two required arguments:

1. An array containing properties specific to Awesome CPT (these include most of the properties documented below)
2. The arguments array that is normally passed to WordPress's [`register_taxonomy()`](https://codex.wordpress.org/Function_Reference/register_taxonomy) function

Assuming a new instance of `Awesome_Post_Type` (with an `id` of `movie`) has
already been created:

```
$movie_genre = new Awesome_Taxonomy( array(
    'id'   => 'movie_genre',
    'name' => array(
        'singular' => 'genre',
        'plural'   => 'genres'
    ),
    'post_types' => array( 'my_movie' ),
    'args' => array(
        'hierarchical'      => true,
        'show_admin_column' => true
    )
);
```

As you can see, the `post_types` array should contain the `id` of each post type
you want to associate with the taxonomy.

Note that internally, the custom taxonomy is initialized when WordPress is
initialized (via the `init` action, with a priority of 10).

## Filterable taxonomies

Awesome CPT allows you to easily make any taxonomy filterable via the
`filterable` property. Setting its value to `true` will add a dropdown menu to
your post type admin screen, from which you can filter your posts by taxonomy.

To work off the above example of a `genre` taxonomy:

```
$movie_genre = new Awesome_Taxonomy( array(
    'id'   => 'movie_genre',
    'name' => array(
        'singular' => 'genre',
        'plural'   => 'genres'
    ),
    'post_types' => array( 'my_movie' ),
    'filterable' => true,
    'args' => array(
        'hierarchical' => true
    )
);
```

## Contextual help menus

You can add contextual help menus for your custom taxonomy

```
$movie_genre = new Awesome_Taxonomy( array(
    'id'   => 'movie_genre',
    'name' => array(
        'singular' => 'genre',
        'plural'   => 'genres'
    ),
    'help_menus' => array(
        array(
            // Edit screen that lists all movies
            'screen' => 'edit-movie_genre',
            'tabs'   => array(
                array(
                    'id'      => 'genre_overview',
                    'title'   => 'Overview',
                    'content' => '<p>You can use genres to group related movies.</p>'
                )
            ),
            'sidebar' => '<p><strong>For more information:</strong></p><p><a href="https://wordpress.org/support/" target="_blank">Support Forums</a></p>'
        )
    )
) );
```

## [Read about meta boxes](meta-boxes.md)
