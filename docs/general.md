# Documentation: General

## Getting Started

To use Awesome CPT on your site, download the `awesome-cpt` directory from GitHub and upload it to your site's `plugins` directory. You can download the entire repository using the following:

```
git clone https://github.com/caleb531/awesome-cpt.git
```

Alternatively, instead of installing Awesome CPT as a plugin, you may include Awesome CPT into any of your existing PHP files like so:


```
require_once dirname( __FILE__ ) . '/awesome-cpt/awesome-cpt.php'
```

## Detecting Awesome CPT

You can detect the presence of Awesome CPT using the `AWESOME_CPT` constant:

```
if ( defined( 'AWESOME_CPT' ) ) {
    // define custom post post types, taxonomies, meta boxes
} else {
    // inform user that Awesome CPT is missing
}
```

## Classes

Awesome CPT consists of three classes meant for use by developers:

* `Awesome_Post_Type`
* `Awesome_Taxonomy`
* `Awesome_Meta_Box`

If you are an expert WordPress developer, you might be pleased to know that you are not required to use *all* of these classes. These classes are completely independent from one another, meaning that they do not depend on each other to function properly.

For instance, if you need a specialized meta box setup, you can code your meta boxes using the native WordPress API, while still using Awesome CPT for your custom post types and taxonomies.

## [Read about post types](post-types.md)
