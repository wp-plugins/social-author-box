<?php
/*
Plugin Name: Social Author Box
Plugin URI: http://popolo.se/
Description: Creates an "Author Box" Widget with the author's picture, bio and social media links
Author: Leander Lindahl
Author URI: http://popolo.se/
License: GPL2
Version: 1.12
*/

/* Add the new Contact Methods to user profile */
add_filter('user_contactmethods','add_new_contactmethod', 99);

/* Add CSS for the Author Box Display */
add_action('wp_enqueue_scripts', 'pop_authorbox_styles');
wp_enqueue_style( 'prefix-font-awesome', 'http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css', array(), '4.0.3' );

/* Declare the widget */
class PopAuthorBox extends WP_Widget
{
  function PopAuthorBox()
  {
    $widget_ops = array('classname' => 'PopAuthorBox', 'description' => 'Creates an Author Box Widget with the author\'s picture, bio and social media links' );
    $this->WP_Widget('PopAuthorBox', 'Social Author Box', $widget_ops);
  }
 
  function form($instance)
  {
    // Check values
    if( $instance) {
      $facebook = esc_attr($instance['facebook']);
      $twitter = esc_attr($instance['twitter']);
      $googleplus = esc_attr($instance['googleplus']); 
      $linkedin = esc_attr($instance['linkedin']);
    } 
    else {
      $facebook = '';
      $twitter = '';
      $googleplus = ''; 
      $linkedin = '';
      add_option("social_authorbox_facebook", $facebook);
      add_option("social_authorbox_twitter", $twitter);
      add_option("social_authorbox_googleplus", $googleplus);
      add_option("social_authorbox_linkedin", $linkedin);
    }
    ?>
    <p><?php _e('Active social media profiles:', 'wp_widget_plugin'); ?></p>
    <p>
      <input id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="checkbox" value="1" <?php checked( '1', $facebook ); ?> />
      <label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook', 'wp_widget_plugin'); ?></label>
    </p>
    <p>
      <input id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="checkbox" value="1" <?php checked( '1', $linkedin ); ?> />
      <label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('LinkedIn', 'wp_widget_plugin'); ?></label>
    </p>
    <p>
      <input id="<?php echo $this->get_field_id('googleplus'); ?>" name="<?php echo $this->get_field_name('googleplus'); ?>" type="checkbox" value="1" <?php checked( '1', $googleplus ); ?> />
      <label for="<?php echo $this->get_field_id('googleplus'); ?>"><?php _e('Google Plus', 'wp_widget_plugin'); ?></label>
    </p>
    <p>
      <input id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="checkbox" value="1" <?php checked( '1', $twitter ); ?> />
      <label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter', 'wp_widget_plugin'); ?></label>
    </p>    
    <p><i><?php _e('URL to the profiles must be added in the respective User Profiles', 'wp_widget_plugin'); ?></i></p>
    <?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    // Fields
    $instance['facebook'] = strip_tags($new_instance['facebook']);
    update_option("social_authorbox_facebook", strip_tags($new_instance['facebook']));

    $instance['twitter'] = strip_tags($new_instance['twitter']);
    update_option("social_authorbox_twitter", strip_tags($new_instance['twitter']));

    $instance['googleplus'] = strip_tags($new_instance['googleplus']);
    update_option("social_authorbox_googleplus", strip_tags($new_instance['googleplus']));

    $instance['linkedin'] = strip_tags($new_instance['linkedin']);
    update_option("social_authorbox_linkedin", strip_tags($new_instance['linkedin']));
    
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args);   
    echo $before_widget;

    // WIDGET CODE GOES HERE
    $author_box = '<div class="author-box">';

    $author_box .= '<div class="author-photo-wrapper"><span class="author-photo">' . get_avatar(get_the_author_id() ) . '</span></div><div class="author-description">' . author_description() . '</div>' . $author_post_line;

    $author_box .= '<div class="author-social-links"><ul>';
    if ( get_the_author_meta( 'facebook_profile' ) && $instance['facebook'] && $instance['facebook'] == 1 ) {
    $author_box .= '<li class="facebook"><a href="' . get_the_author_meta( 'facebook_profile' ) . '" title="' . get_the_author() . ' på LinkedIn" target="_blank"><i class="fa fa-facebook-square"></i></a></li>';
    }
    if ( get_the_author_meta( 'linkedin_profile' )  && $instance['linkedin'] && $instance['linkedin'] == 1 ) {
    $author_box .= '<li class="linkedin"><a href="' . get_the_author_meta( 'linkedin_profile' ) . '" title="' . get_the_author() . ' på LinkedIn" target="_blank"><i class="fa fa-linkedin-square"></i></a></li>';
    }
    if ( get_the_author_meta( 'google_profile' )  && $instance['googleplus'] && $instance['googleplus'] == 1 ) {
    $author_box .= '<li class="googleplus"><a href="' . get_the_author_meta( 'google_profile' ) . '" title="' . get_the_author() . ' på Google+" target="_blank"><i class="fa fa-google-plus-square"></i></a></li>';
    }
    if ( get_the_author_meta( 'twitter_profile' )  && $instance['twitter'] && $instance['twitter'] == 1 ) {
    $author_box .= '<li class="twitter"><a href="' . get_the_author_meta( 'twitter_profile' ) . '" title="' . get_the_author() . ' på Twitter" target="_blank"><i class="fa fa-twitter-square"></i></a></li>';
    }
    $author_box .= '</ul></div>';
    
    $author_box .= '</div>';

    echo $author_box;
    
    echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("PopAuthorBox");') );

/**
 * Assign CSS 
 */
function pop_authorbox_styles() {
  if (!get_option('css_on_profile')) {
    wp_register_style('pop-authorbox-css', plugins_url('social-authorbox.css', __FILE__) );
    wp_enqueue_style( 'pop-authorbox-css');
  }
}

/** 
 * Adding custom fields to user profile 
 */
function add_new_contactmethod($contactmethods) {
  if ( get_option('social_authorbox_facebook') ) {
    $contactmethods['facebook_profile'] = 'Facebook URL';
  }
  if ( get_option('social_authorbox_linkedin') ) {
    $contactmethods['linkedin_profile'] = 'LinkedIn URL';
  }
  if ( get_option('social_authorbox_googleplus') ) {
    $contactmethods['google_profile'] = 'GooglePlus URL';
  }
  if ( get_option('social_authorbox_twitter') ) {
    $contactmethods['twitter_profile'] = 'Twitter URL';
  }
  
  return $contactmethods;
}

function author_description() {
  global $post;
  $source = get_post_meta($post->ID, 'author_desc', true);
  if ($source) {
    return $source; }
  else {
    return get_the_author_description(); 
  }
}

?>