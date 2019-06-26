=== SearchWP Modal Search Form ===
Contributors: jchristopher
Tags: search, modal, form, searchform, SearchWP
Requires at least: 5.0
Tested up to: 5.0
Requires PHP: 7.2
Stable tag: 1.0

Quickly and easily insert modal search forms into Menus, as a Block, or directly within theme templates.

== Description ==
Use SearchWP Modal Search Form to easily integrate a modal search form into your WordPress website! SearchWP Modal Search Form **does not require [SearchWP](https://searchwp.com/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform)** but it will utilize SearchWP if it\'s installed and activated! 👍

Contributions welcome on GitHub! [https://github.com/jchristopher/searchwp-modal-form/](https://github.com/jchristopher/searchwp-modal-form/)

Beyond the information made available in this `README` there is [full documentation](https://searchwp.com/extensions/modal-form/?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform) available as well.

== What makes it better than other modals? ==

The philosophy behind SearchWP Modal Search Form is to make it easy to implement modal search forms but perhaps even more important make it a great developer experience.

SearchWP Modal Search Form directly integrates with WordPress in the way you use it, and the default modal form theme builds upon the styles put in place by your active theme, making the overhead as small as possible. There\'s a full template loader built in as well, allowing you to _fully customize_ your SearchWP Modal Search Form with minimal hassle.

Check out the [default template](https://github.com/jchristopher/searchwp-modal-form/blob/master/templates/default.php) for an example of how easy it is to customize 🤓

== Template Customization ==

SearchWP Modal Search Form integrates a proper template loader, allowing you to _fully customize_ your modal search form.

The most straightforward way to create a custom template is as follows:

1. Create a folder named `searchwp-modal-form` within your theme
1. Create a file within that folder named `template.php` (or any name ending in `.php`)
1. Copy the contents of the [default template](https://github.com/jchristopher/searchwp-modal-form/blob/master/templates/default.php) into that file
1. Customize the `SearchWP Modal Form Name` value in the header comment block
1. Make any other customizations you\'d like to the markup/style, paying attention to the documentation

Please review the [full template documentation](https://searchwp.com/extensions/modal-form/#templates?utm_source=wordpressorg&utm_medium=link&utm_content=readme&utm_campaign=modalform) for more detailed information.

== Installation ==
SearchWP Modal Search Form is installed like all WordPress plugins. You can install this plugin using the `Plugins > Add New` entry in the Admin Menu when logged into your WordPress Dashboard and searching for `SearchWP Modal Search Form`.

Alternatively, you can manually install the plugin as well:

1. Download the plugin and extract the files
1. Upload the `searchwp-modal-form` folder to your `~/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Add one or more modal form(s) using the available methods

== Screenshots ==
1. SearchWP Modal Search Form adapts to your theme
2. SearchWP Modal Search Form adapts to your theme
3. SearchWP Modal Search Form adapts to your theme
4. Insert modal triggers directly within your Menus
5. Insert modal triggers as Gutenberg Blocks

== Changelog ==
*1.0*
Initial release
