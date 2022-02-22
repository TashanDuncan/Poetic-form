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
        add_shortcode( 'poetic-form', array($this, 'load_shortcode'));
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
            , array('jquery'), 
            1, 
             true
            );
    }

    public function load_shortcode()
    {?>
                <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
            <div class="col-lg-6">
                <!-- * * * * * * * * * * * * * * *-->
                <!-- * * Poetic Contact Form * *-->
                <!-- * * * * * * * * * * * * * * *-->
                <form id="contactForm">
                    <!-- Name input-->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="name" name="name" type="text" placeholder="Enter your name..." required/>
                        <label for="name">Full name</label>
                        <div class="invalid-feedback">A name is required.</div>
                    </div>
                    <!-- Email address input-->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" required/>
                        <label for="email">Email address</label>
                        <div class="invalid-feedback">An email is required.</div>
                        <div class="invalid-feedback">Email is not valid.</div>
                    </div>
                    <!-- Message input-->
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="message" name="message" type="text" placeholder="Enter your message here..."
                            style="height: 10rem" required></textarea>
                        <label for="message">Message</label>
                        <div class="invalid-feedback">A message is required.
                        </div>
                    </div>
                    <!-- Submit success message-->
                    <!---->
                    <!-- This is what your users will see when the form-->
                    <!-- has successfully submitted-->
                    <div class="d-none" id="submitSuccessMessage">
                        <div class="text-center mb-3">
                            <div class="fw-bolder">Form submission successful!</div>
                        </div>
                    </div>
                    <!-- Submit error message-->
                    <!---->
                    <!-- This is what your users will see when there is-->
                    <!-- an error submitting the form-->
                    <div class="d-none" id="submitErrorMessage">
                        <div class="text-center text-danger mb-3">Error sending message!</div>
                    </div>
                    <!-- Submit Button-->
                    <div class="d-grid"><button class="btn btn-primary btn-xl" id="submitButton"
                            type="submit">Submit</button></div>
                </form>
            </div>
        </div>
        

    <?php }

}

new PoeticForm;