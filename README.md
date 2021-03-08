# SearchWP Modal Search Form

Use SearchWP Modal Search Form to easily integrate an accessible, lightweight modal search form into your WordPress website! SearchWP Modal Search Form **does not require [SearchWP](https://searchwp.com/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform)** but it will utilize SearchWP if it's installed and activated! :thumbsup:

![Examples of SearchWP Modal Search Form](assets/searchwp-modal-form-examples.gif?raw=true "Examples of SearchWP Modal Search Form")

Beyond the information made available in this `README` there is [full documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform) available as well.

### What makes it different than other modals?

The philosophy behind SearchWP Modal Search Form is to make it easy to implement accessible and lightweight modal search forms but perhaps even more important make it a great developer (and user) experience.

SearchWP Modal Search Form directly integrates with WordPress in the way you use it, and the default modal form theme builds upon the styles put in place by your active theme, making the overhead as small as possible. There's a full template loader built in as well, allowing you to _fully customize_ your SearchWP Modal Search Form with minimal hassle.

Check out the [default template](templates/default.php) for an example of how easy it is to customize :nerd_face:

## Installation and Activation

SearchWP Modal Search Form is installed like all WordPress plugins. You can install this plugin using the `Plugins > Add New` entry in the Admin Menu when logged into your WordPress Dashboard and searching for `SearchWP Modal Search Form`.

Alternatively, you can manually install the plugin as well:

1. Download the plugin and extract the files
1. Upload the `searchwp-modal-form` folder to your `~/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add one or more modal form(s) using the available methods

## Usage

SearchWP Modal Search Form makes it easy to implement modal search forms anywhere in your WordPress website. The following methods are built in and available to you:

1. As a [Menu Item](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#menu-item)
1. Using a [Shortcode](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#shortcode)
1. As a [Gutenberg Block](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#block)
1. Within your template(s) [programmatically](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#code)

## Template Customization

SearchWP Modal Search Form integrates a proper template loader for displaying search forms in any number of templates, allowing you to _fully customize_ what the modal looks like per template.

The most straightforward way to create a custom template is as follows:

1. Create a folder named `searchwp-modal-form` within your theme
1. Create a file within that folder named `template.php` (or any name ending in `.php`)
1. Copy the contents of the [default template](templates/default.php) into that file
1. Customize the `SearchWP Modal Form Name` value in the header comment block
1. Make any other customizations you'd like to the markup/style, paying attention to the documentation

There are (jQuery) events that fire when modals are opened and closed. jQuery *is not* a dependency, but if jQuery is loaded the events will fire.

```javascript
// Add a callback when a modal is opened:
jQuery('body').on('searchwpModalOnShow', function(e) {
	// Focus an input in the template.
	e.el.find('input').focus();
});

// Add a callback when a modal is closed:
jQuery('body').on('searchwpModalOnClose', function(e) {
	alert('Modal closed!');
});
```

Please review the [full template documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#templates) for more detailed information.

### Developer notes

Please see the [hooks documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#hooks) to read more about available hooks.

There is a build process for all JavaScript bundles contained within a single command:

`npm run watch`

This will execute four concurrent processes that watch for file changes and subsequently generate the following:

1. Development and Production versions of the SearchWP Modal Search Form bundle
1. Production version of the SearchWP Modal Search Form block
1. Development version of the SearchWP Modal Search Form block

You can run each process individually if you'd prefer:

```sh
# Build development and production versions of SearchWP Modal Search Form bundle.
npm run bundle

# Build development version of SearchWP Modal Search Form block.
npm run blockdev

# Build production version of SearchWP Modal Search Form block.
npm run blockbuild
```
