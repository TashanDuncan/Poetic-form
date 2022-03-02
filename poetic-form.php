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

        //load js
        add_action('wp_footer', array($this, 'load_scripts'));

        // register rest api
        add_action('rest_api_init', array($this, 'register_rest_api'));


        //send email on publish
        add_action('publish_poetic_form', array($this, 'post_notification'));
    }


    public function create_custom_post_type()
    {
        $args = array(
            'public' => true,
            'has archive' => true,
            'supports' => array('title', 'custom-fields'),
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
        <form id="poetic-contact-form">
            <!-- Name input-->
            <div class="form-floating mb-3">
                <input class="form-control" id="name" name="name" type="text" placeholder="Enter your name..."
                    required />
                <label for="name">Full name</label>
                <div class="invalid-feedback">A name is required.</div>
            </div>
            <!-- Email address input-->
            <div class="form-floating mb-3">
                <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com"
                    required />
                <label for="email">Email address</label>
                <div class="invalid-feedback">An email is required.</div>
                <div class="invalid-feedback">Email is not valid.</div>
            </div>
            <!-- Message input-->
            <div class="form-floating mb-3">
                <textarea class="form-control" id="message" name="message" type="text"
                    placeholder="Enter your message here..." style="height: 10rem" required></textarea>
                <label for="message">Message</label>
                <div class="invalid-feedback">A message is required.
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
            <div class="d-grid"><button class="btn btn-primary btn-xl" id="submitButton" type="submit">Submit</button>
            </div>
        </form>
                    <!-- Submit success message-->
            <!---->
            <!-- This is what your users will see when the form-->
            <!-- has successfully submitted-->
            <div class="d-none" id="submitSuccessMessage">
                <div class="text-center mb-3">
                    <div class="fw-bolder">Submission successful! I will be in touch as soon as possible &#128522; </div>
                </div>
            </div>
    </div>
</div>


<?php }

    public function load_scripts()
    {?>
<script>
var nonce = '<?php echo wp_create_nonce('wp_rest');?>';

(function($) {
    $('#poetic-contact-form').submit(function(event) {

        var success = $('#submitSuccessMessage');
        var formDiv = $('#poetic-contact-form');
        event.preventDefault();

        var form = $(this).serialize();
        console.log(form)

        $.ajax({

            method: 'post',
            url: '<?php echo get_rest_url(null, 'poetic-contact-form/v1/send-email');?>',
            headers: {
                'X-WP-Nonce': nonce
            },
            data: form

        })

        formDiv.addClass('d-none')
        success.removeClass('d-none')

    })
})(jQuery)
</script>
<?php }

public function register_rest_api()
{
    register_rest_route('poetic-contact-form/v1', 'send-email', array(

        'methods' => 'POST',
        'callback' => array($this, 'handle_contact_form')

    ));
}

public function handle_contact_form($data)
{
    $headers = $data->get_headers();
    $params = $data->get_params();
    $nonce = $headers['x_wp_nonce'][0];
    
    echo json_encode($params);
    


    if(!wp_verify_nonce($nonce, 'wp_rest'))
    {
        return new WP_REST_Response('Message not sent', 422);
    }


    $post_id = wp_insert_post([
        'post_type' => 'poetic_form',
        'post_title' => "message from {$params['name']}" ,
        'post_status' => 'publish',
        'meta_input' => array(
            'Email' => $params['email'],
            'Message' => $params['message']
        )
    ]);

    $recipient  = "safiya-06@hotmail.co.uk";
    $subject = "message from {$params['name']}";
    $message = $params['message'];
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        "Reply-To: {$params['name']} <{$params['email']}>"
    );

    $mailResult = false;
    $mailResult = wp_mail($recipient, $subject, $message, $headers);
    echo $mailResult;

    if($post_id)
    {
        return new WP_REST_Response('Thank you for your email', 200);
    }
    //email user with information
    
}


}


new PoeticForm;