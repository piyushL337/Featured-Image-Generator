<?php
/*
*  Plugin Name: Featured Image Generator
*  Description: Automatically generates a featured image for WordPress articles using the article title as the text in the image.
*  Author : PIYUSH JOSHI
*  Github : https://github.com/piyushL337/
*/

// Register a hook to run when a post is published
add_action('publish_post', 'generate_featured_image');

function generate_featured_image($post_id) {
    // Get the title of the post
    $post = get_post($post_id);
    $title = $post->post_title;

    // Create a new image using the title text
    $image = imagecreatetruecolor(600, 200);
    $color = imagecolorallocate($image, 255, 255, 255);
    $font = "arial.ttf";
    $font_size = 14;
    $text_box_width = 600;
    $text_box_height = 200;
    $text_x = 0;
    $text_y = 0;
    $text_box = imagettfbbox($font_size, 0, $font, $title);
    $text_width = $text_box[2] - $text_box[0];
    $text_height = $text_box[7] - $text_box[1];
    $text_x = ($text_box_width - $text_width) / 2;
    $text_y = ($text_box_height - $text_height) / 2;
    imagettftext($image, $font_size, 0, $text_x, $text_y, $color, $font, $title);

    // Save the image to a file
    $file_path = "featured_image_" . $post_id . ".png";
    imagepng($image, $file_path);

    // Set the image as the featured image for the post
    $image_url = plugin_dir_url(__FILE__) . $file_path;
    set_post_thumbnail($post_id, $image_url);
}
