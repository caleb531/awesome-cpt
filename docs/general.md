# General

## Getting Started

To use Awesome CPT on your site, you have two options:

1. Install Awesome CPT from the WordPress repository (coming soon)
2. Download the `awesome-cpt` directory from GitHub and upload it to your site's `plugins` directory

Alternatively, instead of installing Awesome CPT as a plugin, you may include Awesome CPT into any of your existing PHP files like so:


```
require_once dirname( __FILE__ ) . '/awesome-cpt/awesome-cpt.php'
```

## Detecting Awesome CPT

You can detect the presence of Awesome CPT using the `AWESOME_CPT` constant:

```
if ( defined( 'AWESOME_CPT' ) ) {
    // your code here
}
```

## Classes

Awesome CPT consists of three classes meant for use by developers:

* `Awesome_Post_Type`
* `Awesome_Taxonomy`
* `Awesome_Meta_Box`

If you are an expert WordPress developer, you might be pleased to know that you are not required to use *all* of these classes. These classes are totally independent in that they do not depend on each other to function properly.

For instance, if you need a specialized meta box setup, you can code your meta boxes using the native WordPress API, while still using Awesome CPT for post types and taxonomies.

## [Read about post types](post-types.md)
