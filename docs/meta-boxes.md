# Documentation: Meta Boxes

## General usage

You can create a new taxonomy using the `Awesome_Meta_Box` class, the
constructor for which accepts an array of properties. This array accepts a
number of various properties.

* `id`: required; the ID of the meta box
* `title`: required; the displayed title of the meta box
* `post_types`: required; an array of post type IDs with which the meta box is associated
* `context`: optional; the part of the page where the meta box is placed (`normal`, `advanced`, or `side`)
* `priority`: optional; the priority within the context where the meta box should show (`high`, `core`, `default`, or `low`)
* `fields`: optional; an array of data fields that are to be displayed in the meta box

Assuming a new instance of `Awesome_Post_Type` (with an `id` of `movie`) has
already been created:

```
$release_date = new Awesome_Meta_Box( array(
    'id' => 'release_date',
    'title' => 'Release Date',
    'post_types' => array( 'movie' ),
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array()
) );
```

## Fields

As mentioned above, each `Awesome_Meta_Box` instance must have an array of
fields, each of which represents a form field in the meta box. This array can
any number of field, each of which is itself an array.

Each field accepts the following properties:

* `id`: required; the value of the field's `id` attribute
* `name`: optional; the value for the field's `name` attribute. Defaults to the `id` of the field.
* `class`: optional; the value for the field's `class` attribute
* `type`: optional; the type of the field (`text`, `checkbox`, `radio`, `button`, `submit`, `image`, `select`, `textarea`, or `editor`). Defaults to `text`
* `description`: optional; the contents of the `p` element which will precede the field. The `p` element is only prepended if this a value for this property is provided
* `label`: optional; the contents of the `label` element which will precede the field. The `label` element is only prepended if this a value for this property is provided
* `placeholder`: optional; the placeholder text to appear inside the field. Corresponds to the value of the HTML5 `placeholder` attribute.
* `pattern`: optional; the pattern against which the field value will be matched upon submit. Corresponds to the value of the HTML5 `pattern` attribute.
* `required`: optional; a boolean indicating whether or not the field is a required form field. Corresponds to the value of the HTML5 `required` attribute.
* `populate`: optional; a callback function that outputs the custom HTML of the field
* `before`: optional; a string of HTML to precede the field. Defaults to `<p>`
* `after`: optional; a string of HTML to follow the field. Defaults to `</p>`

Also note that you do not need to create or validate a nonce field; Awesome CPT will do so on your behalf.

Working off of the above example:

```
$release_date = new Awesome_Meta_Box( array(
    'id'         => 'release_date',
    'title'      => 'Release Date',
    'post_types' => array( 'movie' ),
    'context'    => 'normal',
    'priority'   => 'high',
    'fields'     => array(
        array(
            'id' => 'release_date',
            'type' => 'text',
            'label' => 'Release Date',
            'placeholder' => 'Enter the movie release date here'
        )
    )
) );
```

### `before` and `after` properties

As briefly mentioned above, the `before` and `after` properties determine what
HTML should precede and follow (respectively) any given field.

```
array(
    'id'          => 'release_date',
    'type'        => 'text',
    'label'       => 'Release Date',
    'placeholder' => 'Enter the movie release date here',
    'before'      => '',
    'after'       => '<br />'
)
```

If you'd prefer to set the default `before` and `after` HTML for all fields,
modify the static `field_defaults` array.

```
Awesome_Meta_Box::$field_defaults['before'] = ''
Awesome_Meta_Box::$field_defaults['after'] = '<br />'
```

### Examples

Below are examples of the various input types you can create. For all of these
types, Awesome CPT will handle the HTML escaping of values on your behalf.

#### Text fields (`<input type='text'>`)

```
array(
    'id'          => 'release_date',
    'type'        => 'text',
    'label'       => 'Release Date',
    'placeholder' => 'Enter the movie release date here'
)
```

#### Textareas (`<textarea>`)

```
array(
    'id'          => 'review',
    'type'        => 'textarea',
    'placeholder' => 'Enter a short review of the movie'
)
```

#### Checkboxes (`<input type='checkbox'>`)

Note that Awesome CPT will automatically add a `hidden` input to allow for the
proper saving of the checkbox value. This default unchecked value is `off`.

```
array(
    'id'      => 'supports_3d',
    'type'    => 'checkbox',
    'label'   => 'Available in 3D?',
    'value'   => 'on',
    'checked' => false
)
```

The following example will output a list of checkboxes for selecting the
languages in which the movie is available.

Note that each field `name` ends in a pair of brackets—this is to ensure the
checked values are properly saved. Also note that the example utilizes the
`before` and `after` properties to

```
array(
    'id'      => 'lang-en',
    'name'    => 'langs[]',
    'type'    => 'checkbox',
    'label'   => 'English',
    'value'   => 'en',
    'checked' => true,
    'before'  => '<h4>Language</h4><p>',
    'after'   => '<br />'
),
array(
    'id'      => 'lang-es',
    'name'    => 'langs[]',
    'type'    => 'checkbox',
    'label'   => 'Spanish',
    'value'   => 'es',
    'before'  => '',
    'after'   => '<br />'
),
array(
    'id'      => 'lang-fr',
    'name'    => 'langs[]',
    'type'    => 'checkbox',
    'label'   => 'French',
    'value'   => 'fr',
    'before'  => '',
    'after'   => '</p>'
)
```

#### Radio buttons (`<input type='radio'>`)

```
array(
    'id'      => 'my_radio_button',
    'type'    => 'radio',
    'label'   => 'My Radio Button',
    'checked' => false
)
```

#### Images (`<input type='image'>`)

```
array(
    'id'    => 'my_radio_button',
    'type'  => 'radio',
    'label' => 'My Radio Button',
    'src'   => get_template_directory_uri() . '/images/myimage.png',
    'alt'   => 'My Image'
)
```

#### Select menus (`<select>`)

```
array(
    'id'          => 'rating',
    'type'        => 'select',
    'label'       => 'Rating',
    'placeholder' => 'Choose the designated rating for this movie',
    'options'     => array(
        array(
            'value'    => 'g',
            'content'  => 'G'
        ),
        array(
            'value'   => 'pg',
            'content' => 'PG',
            'selected' => true
        ),
        array(
            'value'   => 'pg-13',
            'content' => 'PG-13'
        ),
        array(
            'value'   => 'r',
            'content' => 'R'
        )
    )
)
```

##### Custom options

Instead of passing an array of option arrays, you can also pass a function
reference to the `options` property. The referenced function must return an
array in the same form as the one above (an array of option arrays).

See the example below, which modifies the previous example to dynamically
generate a menu of ratings.

```
array(
    'id'          => 'rating',
    'type'        => 'select',
    'label'       => 'Rating',
    'placeholder' => 'Choose the designated rating for this movie',
    'multiple'    => false,
    'options'     => function( $meta_value, $field, $post ) {
        $ratings = array( 'G', 'PG', 'PG-13', 'R' );
        $options = array();
        foreach ( $ratings as $rating ) {
            $options[] = array(
                'value'   => strtolower( $rating ),
                'content' => $rating
            );
        }
        return $options;
    }
)
```

Again, note that the anonymous functions used above are only supported in PHP
5.3 and newer.

#### Date pickers (`<input type='date'>`)

```
array(
    'id'    => 'my_date_picker',
    'type'  => 'date',
    'label' => 'My Date Picker',
    'value' => '2014-01-01'
)
```

#### Color pickers (`<input type='color'>`)

```
array(
    'id'    => 'my_color_picker',
    'type'  => 'color',
    'label' => 'My Color Picker',
    'value' => '#000'
)
```

#### Custom fields

You can also specify custom HTML for your field by passing a function reference
to the `populate` property. This allows for dynamic generation of your field to
fit your needs (such as querying posts or sending a request).

Note that you still need to specify the field's ID in the field array, though
you can access this in your `populate` function for awesome reuse (via the
`$field` parameter).

```
array(
    'id'       => 'release_date',
    'label'    => 'Release Date',
    'populate' => function( $meta_value, $field, $post ) {
        echo "<label for='{$field['id']}'>{$field['label']}</label>";
        echo "<input type='text' value='$meta_value' id='{$field['id']}' />";
    }
)
```

Also note that using the `populate` callback will require you to escape the
variables you use.

#### Filters

You can also pass the value of a meta box field through a filter before being
saved to the database or displayed in the field.

##### `save` filter

The meta value will be run through the `save` filter before the meta data is
saved to the post object.

```
array(
    'id'          => 'release_date',
    'type'        => 'text',
    'label'       => 'Release Date',
    'placeholder' => 'Enter the movie release date here',
    'save'        => function( $meta_value, $field, $post ) {
        // sanitize value somehow
        return $meta_value;
    }
)
```

##### `display` filter

The meta value will be run through the `display` filter before being displayed

```
array(
    'id'          => 'release_date',
    'type'        => 'text',
    'label'       => 'Release Date',
    'placeholder' => 'Enter the movie release date here',
    'display'     => function( $meta_value, $field, $post ) {
        // format value somehow
        return $meta_value;
    }
)
```
