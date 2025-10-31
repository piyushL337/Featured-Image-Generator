# Featured Image Generator

A powerful WordPress plugin that automatically generates featured images for your posts using multiple methods.

## Features

✨ **Multiple Generation Methods**
- **Local Generation**: Creates gradient-based images without any external APIs
- **Text-Based Generation**: Creates images with post title as text overlay
- **Unsplash API**: Fetches relevant images from Unsplash (free API)

🚀 **Bulk Generation**
- Generate featured images for all posts without images in one click
- Progress tracking with detailed logs
- AJAX-based processing for better performance

⚙️ **Easy Configuration**
- Simple settings page in WordPress admin
- API key management with connection testing
- Auto-generate option for new posts
- Choose your preferred generation method

🔒 **Secure & WordPress Standard**
- Follows WordPress coding standards
- Proper nonce verification
- Capability checks for admin functions
- Sanitized inputs and secure file handling

## Installation

1. Upload the plugin files to `/wp-content/plugins/featured-image-generator/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings → Featured Image Generator** to configure
4. (Optional) Add your Unsplash API key for AI-powered images
5. Enable auto-generation or use the bulk generation tool

## Getting an Unsplash API Key (Free)

1. Visit [Unsplash Developers](https://unsplash.com/developers)
2. Register for a free account
3. Create a new application (Demo apps are free with 50 requests/hour)
4. Copy your **Access Key**
5. Paste it in the plugin settings

## Usage

### Auto-Generate on Publish
1. Go to **Settings → Featured Image Generator**
2. Check "Automatically generate featured image when a post is published"
3. Choose your preferred generation method
4. Save settings
5. New posts will automatically get featured images

### Bulk Generate for Existing Posts
1. Go to **Settings → Featured Image Generator**
2. Scroll to "Bulk Generate Featured Images" section
3. Click "Generate for All Posts Without Images"
4. Watch the progress and wait for completion

### Generation Methods

**Local Generation (No API Required)**
- Creates unique gradient images based on post ID
- No external dependencies
- Fast and reliable
- Good for privacy-focused sites

**Text-Based Generation**
- Creates images with post title as text
- Customizable colors and layout
- No API required
- Great for consistent branding

**Unsplash API (Requires Free API Key)**
- Fetches high-quality relevant images
- Based on post title keywords
- Professional photography
- 50 requests/hour on free tier

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- GD Library (for local image generation)
- cURL (for API requests)

## Compatibility

- ✅ Tested up to WordPress 6.7
- ✅ Compatible with Classic Editor
- ✅ Compatible with Gutenberg/Block Editor
- ✅ Works with most WordPress themes

## Security

The plugin follows WordPress security best practices:
- Nonce verification for all AJAX requests
- Capability checks (only administrators can access settings)
- Input sanitization and validation
- Secure file uploads to WordPress media library
- No direct file system access outside WordPress structure

## Support

For issues, questions, or contributions:
- GitHub: [https://github.com/piyushL337/Featured-Image-Generator](https://github.com/piyushL337/Featured-Image-Generator)
- Issues: [Report a bug](https://github.com/piyushL337/Featured-Image-Generator/issues)

## Changelog

### Version 2.0.0
- Complete rewrite with improved architecture
- Added Unsplash API integration
- Added bulk generation feature
- Added settings page with API configuration
- Fixed image attachment creation
- Improved security and WordPress compatibility
- Added three generation methods
- Proper WordPress plugin structure
- Added readme.txt for WordPress.org

### Version 1.0.0
- Initial release

## License

GPL v2 or later - [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Credits

Developed by [Piyush Joshi](https://github.com/piyushL337/)

Images provided by [Unsplash](https://unsplash.com) (when using Unsplash API method)
