=== SearchWP Modal Search Form ===
Contributors: jchristopher
Tags: search, modal, form, searchform, SearchWP
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 7.0
Stable tag: 0.4.1

Quickly and easily insert modal search forms into Menus, as a Block, or directly within theme templates.

== Description ==
Use SearchWP Modal Search Form to easily integrate an accessible, lightweight modal search form into your WordPress website! SearchWP Modal Search Form **does not require [SearchWP](https://searchwp.com/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform)** but it will utilize SearchWP if it's installed and activated! 👍

Contributions are welcome on GitHub! [https://github.com/jchristopher/searchwp-modal-search-form/](https://github.com/jchristopher/searchwp-modal-search-form/)

Beyond the information made available in this `README` there is [full documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform) available as well.

== What makes it different than other modals? ==

The philosophy behind SearchWP Modal Search Form is to make it easy to implement accessible and lightweight modal search forms but perhaps even more important make it a great developer (and user) experience.

SearchWP Modal Search Form directly integrates with WordPress in the way you use it, and the default modal form theme builds upon the styles put in place by your active theme, making the overhead as small as possible. There's a full template loader built in as well, allowing you to _fully customize_ your SearchWP Modal Search Form with minimal hassle.

Check out the [default template](https://github.com/jchristopher/searchwp-modal-search-form/blob/master/templates/default.php) for an example of how easy it is to customize 🤓

== Adding modal forms to your site ==

SearchWP Modal Search Form makes it easy to implement modal search forms anywhere in your WordPress website. The following methods are built in and available to you:

1. As a [Menu Item](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#menu-item)
1. Using a [Shortcode](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#shortcode)
1. As a [Gutenberg Block](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#block)
1. Within your template(s) [programmatically](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#code)

== Template Customization ==

SearchWP Modal Search Form integrates a proper template loader for displaying search forms in any number of templates, allowing you to _fully customize_ what the modal looks like per template.

The most straightforward way to create a custom template is as follows:

1. Create a folder named `searchwp-modal-form` within your theme
1. Create a file within that folder named `template.php` (or any name ending in `.php`)
1. Copy the contents of the [default template](https://github.com/jchristopher/searchwp-modal-search-form/blob/master/templates/default.php) into that file
1. Customize the `SearchWP Modal Form Name` value in the header comment block
1. Make any other customizations you\'d like to the markup/style, paying attention to the documentation

There are (jQuery) events that fire when modals are opened and closed. jQuery *is not* a dependency, but if jQuery is loaded the events will fire.

`// Add a callback when a modal is opened:
jQuery('body').on('searchwpModalOnShow', function(e) {
	// Focus an input in the template.
	e.el.find('input').focus();
});

// Add a callback when a modal is closed:
jQuery('body').on('searchwpModalOnClose', function(e) {
	alert('Modal closed!');
});`

Please review the [full template documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#templates) for more detailed information.

== Installation ==
SearchWP Modal Search Form is installed like all WordPress plugins. You can install this plugin using the `Plugins > Add New` entry in the Admin Menu when logged into your WordPress Dashboard and searching for `SearchWP Modal Search Form`.

Alternatively, you can manually install the plugin as well:

1. Download the plugin and extract the files
1. Upload the `searchwp-modal-form` folder to your `~/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Add one or more modal form(s) using the available methods

== Frequently Asked Questions ==

= Documentation? =

Of course! [View full documentation](https://searchwp.com/extensions/modal-form/)

= How do I add a modal form to my website? =

There are multiple ways to add modal form triggers to your site. You can add as many as you'd like wherever you'd like:

1. As a [Menu Item](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#menu-item)
1. Using a [Shortcode](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#shortcode)
1. As a [Gutenberg Block](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#block)
1. Within your template(s) [programmatically](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#code)

= How do I customize the modal? =

Please review the [full template documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform#templates) for detailed information.

== Screenshots ==
1. SearchWP Modal Search Form adapts to your theme
2. SearchWP Modal Search Form adapts to your theme
3. SearchWP Modal Search Form adapts to your theme
4. Insert modal triggers directly within your Menus
5. Insert modal triggers as Gutenberg Blocks

== Changelog ==

*0.4.1*
- PHP 8 compatibility

*0.4.0*
- Fixes issue with character encoding in some cases
- Updates dependencies
- Updates bundler and associated NPM commands

*0.3.4*
- Fixes Error when using SearchWP 4

*0.3.3*
- Fixes Error in some cases introduced in 0.3.2

*0.3.2*
- SearchWP 4.0 compatibility (when it becomes available)
- Fixes issue with HTML output of Menu items

*0.3.1*
- Removes unused stylesheet enqueue
- Updates dependencies

*0.3*
- Updates dependencies
- Adds jQuery events when modals open and close

*0.2.3*
- Fixes regression introduced in 0.2.2 that prevented search with Enter key

*0.2.2*
- Fixes an issue with modal trigger not working in some cases

*0.2.1*
- Fixes `$this` context Fatal error

*0.2*
- Adds support for `class` argument
- Fixes issue with `button` type

*0.1*
Initial release
