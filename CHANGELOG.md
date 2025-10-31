# Changelog

All notable changes to the Featured Image Generator plugin will be documented in this file.

## [2.0.0] - 2024

### Added
- **Free API Integration**: Unsplash API support for AI-powered image generation
  - Free tier: 50 requests/hour
  - Automatic fallback to local generation if API fails
  
- **Settings Page**: Complete admin interface at Settings → Featured Image Generator
  - API key configuration for Unsplash
  - Test API connection button
  - Choose generation method (Local, Text-based, or Unsplash)
  - Auto-generate toggle for new posts
  
- **Bulk Generation Tool**:
  - Generate featured images for all posts without images
  - AJAX-based with real-time progress tracking
  - Progress bar and detailed log
  
- **Multiple Generation Methods**:
  1. **Local Generation**: Creates unique gradient images based on post ID
     - No external dependencies
     - Privacy-friendly
     - Fast and reliable
  
  2. **Text-Based Generation**: Creates images with post title overlay
     - Customizable colors
     - Automatic text wrapping
     - Font fallback system (TrueType → built-in GD fonts)
     - Multibyte character support (UTF-8)
  
  3. **Unsplash API**: Fetches relevant professional images
     - Based on post title keywords
     - High-quality photography
     - Landscape orientation
     - Automatic fallback to local generation
  
- **WordPress Plugin Standards**:
  - readme.txt for WordPress plugin directory
  - Proper plugin headers with metadata
  - uninstall.php for cleanup
  - index.php files for security (prevent directory browsing)
  - Translation-ready with text domain
  
- **Security Enhancements**:
  - Nonce verification on all AJAX requests
  - Capability checks (manage_options required)
  - Input sanitization and validation
  - Secure file uploads via WordPress API
  - No SQL injection vulnerabilities

### Changed
- **Complete Rewrite**: Rebuilt plugin from ground up with OOP approach
- **File Handling**: Images now saved to WordPress uploads directory (not plugin directory)
- **Attachment Creation**: Fixed to use attachment ID instead of URL
- **Font System**: Added fallback from TrueType fonts to built-in GD fonts
- **Image Dimensions**: Standardized to 1200x630 (optimal for social media)
- **Code Structure**: Implemented singleton pattern with proper WordPress hooks

### Fixed
- Fixed `set_post_thumbnail()` receiving URL instead of attachment ID
- Fixed font file path issues (was hardcoded to non-existent arial.ttf)
- Fixed images being saved in plugin directory (bad practice)
- Fixed lack of attachment metadata generation
- Added multibyte character support for international characters
- Added truncation for words exceeding maximum width
- Fixed redundant font path validation
- Corrected macOS Arial font path

### Technical Details
- PHP Version: 7.4+
- WordPress Version: 5.0+ (tested up to 6.7)
- Required Extensions: GD library, cURL
- Architecture: Singleton pattern, OOP
- AJAX: jQuery-based with nonce verification
- Localization: Translation-ready

## [1.0.0] - Previous

### Initial Release
- Basic featured image generation with hardcoded settings
- Simple text overlay functionality

---

## Upgrade Notes

### From 1.x to 2.0
This is a major rewrite. The plugin will continue to work, but you'll need to:
1. Visit Settings → Featured Image Generator to configure preferences
2. Optionally add Unsplash API key for better images
3. Use bulk generation tool for existing posts without featured images

No data will be lost during the upgrade. All existing featured images remain intact.
