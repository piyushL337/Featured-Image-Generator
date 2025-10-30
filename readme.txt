=== Featured Image Generator ===
Contributors: piyushjoshi
Tags: featured image, auto generate, image generator, bulk images, unsplash
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically generates featured images for WordPress posts using multiple methods: text-based generation, Unsplash API, or local generation.

== Description ==

Featured Image Generator is a powerful WordPress plugin that automatically generates featured images for your posts. It offers multiple generation methods:

* **Text-based Generation**: Creates images with the post title as text overlay
* **Unsplash API Integration**: Uses free Unsplash API to fetch relevant images based on post title
* **Local Generation**: Generates images without external APIs using GD library
* **Bulk Generation**: Generate featured images for multiple posts at once

= Features =

* Multiple image generation methods
* Bulk featured image generation for existing posts
* Settings page to configure API keys and preferences
* Compatible with latest WordPress version
* Secure and follows WordPress coding standards
* No paid APIs required - uses free Unsplash API

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/featured-image-generator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Featured Image Generator screen to configure the plugin
4. (Optional) Add your Unsplash API key for AI-powered image generation
5. Enable auto-generation on post publish or use bulk generation tool

== Frequently Asked Questions ==

= Do I need an API key? =

No, the plugin works without an API key using local image generation. However, for better quality AI-powered images, you can get a free Unsplash API key.

= How do I get an Unsplash API key? =

1. Visit https://unsplash.com/developers
2. Register for a free account
3. Create a new application
4. Copy your Access Key and paste it in the plugin settings

= Can I generate images for existing posts? =

Yes! Use the bulk generation tool in the plugin settings to generate featured images for posts that don't have one.

== Screenshots ==

1. Plugin settings page
2. Bulk generation interface
3. Example generated featured image

== Changelog ==

= 2.0.0 =
* Complete rewrite with improved functionality
* Added Unsplash API integration
* Added bulk generation feature
* Added settings page with API configuration
* Fixed image attachment creation issues
* Improved security and WordPress compatibility
* Added multiple generation methods

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.0.0 =
Major update with new features and improvements. Backup your site before upgrading.
