<?php
/**
 * Plugin Name: Poetic Form
 * Description: Poetic Form plugin
 * Author: Tashan Duncan
 * Author URI: https://tashanducan.com
 * Version: 1.0.0
 * Text Domain: poetic-form
 * 
 */

if( !defined('ABSPATH')) {
    exit;
}

class PoeticForm{

    public function __construct()
    {
        //create custom post type
        add_action('init', array($this, 'create_custom_post_type'));

        //add assets (js, css, ect)
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));

        // add shortcode
        add_shortcode( 'contact-form', array($this, 'load_shortcode'));
    }


    public function create_custom_post_type()
    {
        $args = array(
            'public' => true,
            'has archive' => true,
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability' => 'manage_options',
            'labels' => array(
                'name' => 'Contact Form',
                'singular_name' => 'Contact Form Entry'
            ),
            'menu_icon' => 'dashicons-media-text',
        );

        register_post_type( 'poetic_form', $args);
    }


    public function load_assets()
    {
        wp_enqueue_style( 
            'poetic-form', 
            plugin_dir_url( __FILE__ ) . 'css/poetic-form.css', 
            array(), 
            1, 
            'all'
        );

        wp_enqueue_script(             
            'poetic-form', 
            plugin_dir_url( __FILE__ ) . 'js/poetic-form.js'
            , array(), 
            1, 
             true
            );
    }

    public function load_shortcode()
    {
        return 'hello this is working';
    }

}

new PoeticForm;