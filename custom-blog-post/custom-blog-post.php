<?php
/**
* Plugin Name: Custom Blog Post
* Plugin URI: https://www.redcircle.biz
* Description: Add the ability to add custom form to sumbit posts from a page.
* Version: 1.0
* Author: Michael Burbage
* Author URI: https://www.redcircle.biz
**/

wp_register_style( 'customblogpost', '/wp-content/plugins/custom-blog-post/css/custom-blog-post.css' );

wp_enqueue_style('customblogpost');

add_shortcode( 'custom_blog_post', 'custom_blog_post' );

function custom_blog_post() {
    if ( is_admin()){
        return;
    }else{
        custom_blog_post_if_submitted();
    ?>
        <div id="postbox">
            <form id="new_post" name="new_post" method="post">

            <label for="title">Title</label><br />
                <input type="text" id="title" value="" tabindex="1" size="20" name="title" />
            

            
                <label for="content">Post Content</label><br />
                

                <?php 

                    $settings = array(
                        'textarea_name' => 'wpeditor',
                        'media_buttons' => true,
                        'tinymce' => array(
                            'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
                                'bullist,blockquote,|,justifyleft,justifycenter' .
                                ',justifyright,justifyfull,|,link,unlink,|' .
                                ',spellchecker,wp_fullscreen,wp_adv'
                        )
                    );

                    wp_editor( '', 'wpeditor', $settings );
                
                ?>
            <br/>

            <?php //wp_dropdown_categories( 'show_option_none=Category&tab_index=4&taxonomy=category' ); ?>

            <?php wp_nonce_field( 'wps-frontend-post' ); ?>

            <input type="submit" value="Publish" tabindex="6" id="submit" name="submit" />
            
            </form>
        </div>
    <?php
    }
}
function custom_blog_post_if_submitted() {
    // Stop running function if form wasn't submitted
    if ( !isset($_POST['title']) ) {
        return;
    }

    // Check that the nonce was set and valid
    if( !wp_verify_nonce($_POST['_wpnonce'], 'wps-frontend-post') ) {
        echo 'Did not save because your form seemed to be invalid. Sorry';
        return;
    }

    // Do some minor form validation to make sure there is content
    if (strlen($_POST['title']) < 3) {
        echo 'Please enter a title. Titles must be at least three characters long.';
        return;
    }
    if (strlen($_POST['content']) < 100) {
        echo 'Please enter content more than 100 characters in length';
        return;
    }
    //$postAuthor = get_the_author_meta( 'ID' );

    // Add the content of the form to $post as an array
    $post = array(
        'post_title'    => $_POST['title'],
        'post_content'  => $_POST['wpeditor'],
        'post_category' => array(20), // $_POST['cat']
        'post_status'   => 'publish',   // Could be: publish
        'post_type' 	=> 'post' // Could be: `page` or your CPT
    );
    wp_insert_post($post);

    echo 'Saved your post successfully! :)';
}