<?php
/**
 * Plugin Name: Featured Image Generator
 * Plugin URI: https://github.com/piyushL337/Featured-Image-Generator
 * Description: Automatically generates featured images for WordPress posts using multiple methods: text-based generation, Unsplash API, or local generation. Supports bulk generation.
 * Version: 2.0.0
 * Author: Piyush Joshi
 * Author URI: https://github.com/piyushL337/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: featured-image-generator
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 6.7
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FIG_VERSION', '2.0.0');
define('FIG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FIG_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Featured Image Generator Class
 */
class Featured_Image_Generator {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Auto-generate on post publish if enabled
        add_action('publish_post', array($this, 'auto_generate_on_publish'), 10, 2);
        
        // AJAX handlers for bulk generation
        add_action('wp_ajax_fig_bulk_generate', array($this, 'ajax_bulk_generate'));
        add_action('wp_ajax_fig_test_api', array($this, 'ajax_test_api'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Featured Image Generator', 'featured-image-generator'),
            __('Featured Image Generator', 'featured-image-generator'),
            'manage_options',
            'featured-image-generator',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('fig_settings_group', 'fig_settings', array($this, 'sanitize_settings'));
        
        add_settings_section(
            'fig_general_section',
            __('General Settings', 'featured-image-generator'),
            array($this, 'render_general_section'),
            'featured-image-generator'
        );
        
        add_settings_section(
            'fig_api_section',
            __('API Settings', 'featured-image-generator'),
            array($this, 'render_api_section'),
            'featured-image-generator'
        );
        
        // Auto-generate setting
        add_settings_field(
            'auto_generate',
            __('Auto Generate', 'featured-image-generator'),
            array($this, 'render_auto_generate_field'),
            'featured-image-generator',
            'fig_general_section'
        );
        
        // Generation method
        add_settings_field(
            'generation_method',
            __('Generation Method', 'featured-image-generator'),
            array($this, 'render_generation_method_field'),
            'featured-image-generator',
            'fig_general_section'
        );
        
        // Unsplash API key
        add_settings_field(
            'unsplash_api_key',
            __('Unsplash Access Key', 'featured-image-generator'),
            array($this, 'render_unsplash_api_key_field'),
            'featured-image-generator',
            'fig_api_section'
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['auto_generate'])) {
            $sanitized['auto_generate'] = (bool) $input['auto_generate'];
        }
        
        if (isset($input['generation_method'])) {
            $valid_methods = array('local', 'text', 'unsplash');
            $sanitized['generation_method'] = in_array($input['generation_method'], $valid_methods) 
                ? $input['generation_method'] 
                : 'local';
        }
        
        if (isset($input['unsplash_api_key'])) {
            $sanitized['unsplash_api_key'] = sanitize_text_field($input['unsplash_api_key']);
        }
        
        return $sanitized;
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('fig_settings_group');
                do_settings_sections('featured-image-generator');
                submit_button(__('Save Settings', 'featured-image-generator'));
                ?>
            </form>
            
            <hr>
            
            <h2><?php _e('Bulk Generate Featured Images', 'featured-image-generator'); ?></h2>
            <p><?php _e('Generate featured images for posts that don\'t have one.', 'featured-image-generator'); ?></p>
            
            <div id="fig-bulk-generate-container">
                <button type="button" class="button button-primary" id="fig-bulk-generate-btn">
                    <?php _e('Generate for All Posts Without Images', 'featured-image-generator'); ?>
                </button>
                <div id="fig-bulk-progress" style="display:none; margin-top: 15px;">
                    <p><strong><?php _e('Progress:', 'featured-image-generator'); ?></strong> <span id="fig-progress-text">0/0</span></p>
                    <progress id="fig-progress-bar" value="0" max="100" style="width: 100%; height: 30px;"></progress>
                    <div id="fig-progress-log" style="margin-top: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;"></div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render general section description
     */
    public function render_general_section() {
        echo '<p>' . __('Configure how featured images should be generated.', 'featured-image-generator') . '</p>';
    }
    
    /**
     * Render API section description
     */
    public function render_api_section() {
        echo '<p>' . __('Configure API keys for external image services. Get your free Unsplash API key at:', 'featured-image-generator') . ' <a href="https://unsplash.com/developers" target="_blank">https://unsplash.com/developers</a></p>';
    }
    
    /**
     * Render auto-generate field
     */
    public function render_auto_generate_field() {
        $options = get_option('fig_settings', array());
        $checked = isset($options['auto_generate']) ? $options['auto_generate'] : false;
        ?>
        <label>
            <input type="checkbox" name="fig_settings[auto_generate]" value="1" <?php checked($checked, true); ?>>
            <?php _e('Automatically generate featured image when a post is published', 'featured-image-generator'); ?>
        </label>
        <?php
    }
    
    /**
     * Render generation method field
     */
    public function render_generation_method_field() {
        $options = get_option('fig_settings', array());
        $method = isset($options['generation_method']) ? $options['generation_method'] : 'local';
        ?>
        <select name="fig_settings[generation_method]" id="fig_generation_method">
            <option value="local" <?php selected($method, 'local'); ?>><?php _e('Local Generation (No API)', 'featured-image-generator'); ?></option>
            <option value="text" <?php selected($method, 'text'); ?>><?php _e('Text-based Generation', 'featured-image-generator'); ?></option>
            <option value="unsplash" <?php selected($method, 'unsplash'); ?>><?php _e('Unsplash API (AI-powered)', 'featured-image-generator'); ?></option>
        </select>
        <p class="description">
            <?php _e('Choose how to generate featured images. Local and text-based work without API keys.', 'featured-image-generator'); ?>
        </p>
        <?php
    }
    
    /**
     * Render Unsplash API key field
     */
    public function render_unsplash_api_key_field() {
        $options = get_option('fig_settings', array());
        $api_key = isset($options['unsplash_api_key']) ? $options['unsplash_api_key'] : '';
        ?>
        <input type="text" name="fig_settings[unsplash_api_key]" value="<?php echo esc_attr($api_key); ?>" 
               class="regular-text" placeholder="<?php _e('Enter your Unsplash Access Key', 'featured-image-generator'); ?>">
        <p class="description">
            <?php _e('Required only if using Unsplash API generation method.', 'featured-image-generator'); ?>
        </p>
        <button type="button" class="button" id="fig-test-api-btn" style="margin-top: 5px;">
            <?php _e('Test API Connection', 'featured-image-generator'); ?>
        </button>
        <span id="fig-api-test-result" style="margin-left: 10px;"></span>
        <?php
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_featured-image-generator' !== $hook) {
            return;
        }
        
        wp_enqueue_script(
            'fig-admin-script',
            FIG_PLUGIN_URL . 'assets/admin.js',
            array('jquery'),
            FIG_VERSION,
            true
        );
        
        wp_localize_script('fig-admin-script', 'figAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fig_ajax_nonce'),
            'strings' => array(
                'testing' => __('Testing...', 'featured-image-generator'),
                'success' => __('Connection successful!', 'featured-image-generator'),
                'error' => __('Connection failed. Please check your API key.', 'featured-image-generator'),
                'generating' => __('Generating...', 'featured-image-generator'),
                'complete' => __('Bulk generation complete!', 'featured-image-generator'),
                'error_occurred' => __('An error occurred. Please try again.', 'featured-image-generator'),
            )
        ));
    }
    
    /**
     * Auto-generate featured image on post publish
     */
    public function auto_generate_on_publish($post_id, $post) {
        $options = get_option('fig_settings', array());
        
        // Check if auto-generate is enabled
        if (empty($options['auto_generate'])) {
            return;
        }
        
        // Check if post already has a featured image
        if (has_post_thumbnail($post_id)) {
            return;
        }
        
        // Avoid infinite loops
        if (defined('FIG_GENERATING')) {
            return;
        }
        define('FIG_GENERATING', true);
        
        // Generate the featured image
        $this->generate_featured_image($post_id);
    }
    
    /**
     * Generate featured image for a post
     */
    public function generate_featured_image($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }
        
        $options = get_option('fig_settings', array());
        $method = isset($options['generation_method']) ? $options['generation_method'] : 'local';
        
        $image_path = null;
        
        switch ($method) {
            case 'unsplash':
                $image_path = $this->generate_from_unsplash($post);
                break;
            case 'text':
                $image_path = $this->generate_text_image($post);
                break;
            case 'local':
            default:
                $image_path = $this->generate_local_image($post);
                break;
        }
        
        if ($image_path && file_exists($image_path)) {
            $attachment_id = $this->create_attachment($image_path, $post_id);
            if ($attachment_id) {
                set_post_thumbnail($post_id, $attachment_id);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate image from Unsplash API
     */
    private function generate_from_unsplash($post) {
        $options = get_option('fig_settings', array());
        $api_key = isset($options['unsplash_api_key']) ? $options['unsplash_api_key'] : '';
        
        if (empty($api_key)) {
            // Fallback to local generation
            return $this->generate_local_image($post);
        }
        
        // Use post title as search query
        $query = urlencode(sanitize_text_field($post->post_title));
        
        // Unsplash API endpoint
        $url = "https://api.unsplash.com/photos/random?query={$query}&orientation=landscape";
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Client-ID ' . $api_key,
            ),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            // Fallback to local generation
            return $this->generate_local_image($post);
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['urls']['regular'])) {
            $image_url = $data['urls']['regular'];
            
            // Download the image
            $image_data = wp_remote_get($image_url, array('timeout' => 30));
            
            if (!is_wp_error($image_data)) {
                $image_content = wp_remote_retrieve_body($image_data);
                
                $upload_dir = wp_upload_dir();
                $filename = 'featured-image-' . $post->ID . '-' . time() . '.jpg';
                $filepath = $upload_dir['path'] . '/' . $filename;
                
                file_put_contents($filepath, $image_content);
                return $filepath;
            }
        }
        
        // Fallback to local generation
        return $this->generate_local_image($post);
    }
    
    /**
     * Generate text-based image
     */
    private function generate_text_image($post) {
        $title = $post->post_title;
        
        // Image dimensions
        $width = 1200;
        $height = 630;
        
        // Create image
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bg_color = imagecolorallocate($image, 41, 128, 185); // Blue background
        $text_color = imagecolorallocate($image, 255, 255, 255); // White text
        
        // Fill background
        imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
        
        $font_path = $this->get_font_path();
        
        // Check if we have a TrueType font
        if (is_string($font_path) && file_exists($font_path)) {
            // Use TrueType font
            $max_width = $width - 100;
            $font_size = 40;
            $wrapped_text = $this->wrap_text($title, $font_size, $max_width, $font_path);
            
            // Calculate text position (centered)
            $lines = explode("\n", $wrapped_text);
            $line_height = $font_size + 20;
            $total_height = count($lines) * $line_height;
            $y = ($height - $total_height) / 2 + $font_size;
            
            // Draw each line of text
            foreach ($lines as $line) {
                $bbox = imagettfbbox($font_size, 0, $font_path, $line);
                $text_width = $bbox[2] - $bbox[0];
                $x = ($width - $text_width) / 2;
                
                imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_path, $line);
                $y += $line_height;
            }
        } else {
            // Fallback to built-in font
            $lines = $this->wrap_text_builtin($title, $width - 40);
            $line_height = 20;
            $total_height = count($lines) * $line_height;
            $y = ($height - $total_height) / 2;
            
            foreach ($lines as $line) {
                $text_width = imagefontwidth(5) * strlen($line);
                $x = ($width - $text_width) / 2;
                imagestring($image, 5, $x, $y, $line, $text_color);
                $y += $line_height;
            }
        }
        
        // Save image
        $upload_dir = wp_upload_dir();
        $filename = 'featured-image-' . $post->ID . '-' . time() . '.png';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        imagepng($image, $filepath);
        imagedestroy($image);
        
        return $filepath;
    }
    
    /**
     * Generate local image without text
     */
    private function generate_local_image($post) {
        $width = 1200;
        $height = 630;
        
        $image = imagecreatetruecolor($width, $height);
        
        // Generate a gradient or pattern based on post ID
        $seed = $post->ID;
        srand($seed);
        
        $color1_r = rand(50, 200);
        $color1_g = rand(50, 200);
        $color1_b = rand(50, 200);
        
        $color2_r = rand(50, 200);
        $color2_g = rand(50, 200);
        $color2_b = rand(50, 200);
        
        // Create gradient
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = $color1_r + ($color2_r - $color1_r) * $ratio;
            $g = $color1_g + ($color2_g - $color1_g) * $ratio;
            $b = $color1_b + ($color2_b - $color1_b) * $ratio;
            
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
        
        // Save image
        $upload_dir = wp_upload_dir();
        $filename = 'featured-image-' . $post->ID . '-' . time() . '.png';
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        imagepng($image, $filepath);
        imagedestroy($image);
        
        return $filepath;
    }
    
    /**
     * Wrap text to fit within max width (for TrueType fonts)
     */
    private function wrap_text($text, $font_size, $max_width, $font_path) {
        $words = explode(' ', $text);
        $lines = array();
        $current_line = '';
        
        foreach ($words as $word) {
            $test_line = $current_line . ($current_line ? ' ' : '') . $word;
            $bbox = imagettfbbox($font_size, 0, $font_path, $test_line);
            $text_width = $bbox[2] - $bbox[0];
            
            if ($text_width <= $max_width) {
                $current_line = $test_line;
            } else {
                if ($current_line) {
                    $lines[] = $current_line;
                }
                $current_line = $word;
            }
        }
        
        if ($current_line) {
            $lines[] = $current_line;
        }
        
        return implode("\n", $lines);
    }
    
    /**
     * Wrap text for built-in fonts
     */
    private function wrap_text_builtin($text, $max_width) {
        $char_width = imagefontwidth(5);
        $max_chars = floor($max_width / $char_width);
        
        $words = explode(' ', $text);
        $lines = array();
        $current_line = '';
        
        foreach ($words as $word) {
            $test_line = $current_line . ($current_line ? ' ' : '') . $word;
            
            if (strlen($test_line) <= $max_chars) {
                $current_line = $test_line;
            } else {
                if ($current_line) {
                    $lines[] = $current_line;
                }
                $current_line = $word;
            }
        }
        
        if ($current_line) {
            $lines[] = $current_line;
        }
        
        return $lines;
    }
    
    /**
     * Get font path
     */
    private function get_font_path() {
        // Try to use system fonts
        $font_paths = array(
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', // Linux
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', // Linux alternative
            '/System/Library/Fonts/Helvetica.ttc', // macOS
            '/System/Library/Fonts/Arial.ttf', // macOS alternative
            'C:\Windows\Fonts\arial.ttf', // Windows
            'C:\Windows\Fonts\verdana.ttf', // Windows alternative
        );
        
        foreach ($font_paths as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        // Return null if no TrueType font available
        return null;
    }
    
    /**
     * Create attachment from file
     */
    private function create_attachment($filepath, $post_id) {
        $filename = basename($filepath);
        $upload_dir = wp_upload_dir();
        
        $filetype = wp_check_filetype($filename);
        
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $filepath, $post_id);
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $filepath);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            
            return $attachment_id;
        }
        
        return false;
    }
    
    /**
     * AJAX handler for bulk generation
     */
    public function ajax_bulk_generate() {
        check_ajax_referer('fig_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'featured-image-generator')));
        }
        
        // Get posts without featured images
        $args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $posts = get_posts($args);
        $total = count($posts);
        $processed = 0;
        $success = 0;
        
        foreach ($posts as $post) {
            if ($this->generate_featured_image($post->ID)) {
                $success++;
            }
            $processed++;
            
            // Send progress update
            if ($processed % 5 === 0 || $processed === $total) {
                wp_send_json_success(array(
                    'processed' => $processed,
                    'total'     => $total,
                    'success'   => $success,
                    'message'   => sprintf(
                        __('Processed %d of %d posts (%d successful)', 'featured-image-generator'),
                        $processed,
                        $total,
                        $success
                    )
                ));
            }
        }
        
        wp_send_json_success(array(
            'processed' => $processed,
            'total'     => $total,
            'success'   => $success,
            'complete'  => true,
            'message'   => sprintf(
                __('Complete! Generated %d featured images for %d posts.', 'featured-image-generator'),
                $success,
                $total
            )
        ));
    }
    
    /**
     * AJAX handler for testing API connection
     */
    public function ajax_test_api() {
        check_ajax_referer('fig_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'featured-image-generator')));
        }
        
        $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key is required', 'featured-image-generator')));
        }
        
        // Test Unsplash API
        $url = 'https://api.unsplash.com/photos/random?query=test';
        
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Client-ID ' . $api_key,
            ),
            'timeout' => 15,
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        $code = wp_remote_retrieve_response_code($response);
        
        if ($code === 200) {
            wp_send_json_success(array('message' => __('API connection successful!', 'featured-image-generator')));
        } else {
            wp_send_json_error(array('message' => __('API connection failed. Please check your API key.', 'featured-image-generator')));
        }
    }
}

// Initialize the plugin
function featured_image_generator_init() {
    return Featured_Image_Generator::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'featured_image_generator_init');
