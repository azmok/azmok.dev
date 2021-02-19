<?php

/**
 * Plugin Name+: AZM Echo Text
 * Description?: Echoing Text in Every Pages
 */

 

### register, render settings title, field
function render_excerptChanger_title( $args){
  echo "<u><i>current length:</i></u>";
}
function render_excerptChanger_field( $args){
   $val = get_option('azm_excerptChanger');
   $expr = isset( $val ) ? esc_attr( $val ) : "";
   $html = "<input type='text' placeholder='Enter Length' name='azm_excerptChanger' value='{$expr}' />";
   
   echo $html;
}


function register_excerptChanger(){
   register_setting('reading', 'azm_excerptChanger');
   //                $page   ,  $option_name
   
   add_settings_section(
      'excerptChanger-section', // In HTML, this value used as 'section's id attribute value 
                                // In addition, this is used as identifier in PHP
      'Change Excerpt Length',  // Section title in HTML
      'render_excerptChanger_title', // name of callback function to render HTML
      'reading' // built-in settings pages name('general', 'discussion', 'media', 'reading',
                //   'writing', 'misc', 'options', 'privacy')
   );
   
   add_settings_field(
      'excerptChanger-field',
         
      'Current Excerpt Length',
      'render_excerptChanger_field',
      'reading',
      'excerptChanger-section' // parent section id in order to append this field. If not specified, this field will append to 'default' section.
   );
}
add_action('admin_init', 'register_excerptChanger');



###

function azm_chnageExcerptLength( $excerpt ){
   $optionsLen = get_option('azm_excerptChanger');
   $excerptLen = mb_strlen($excerpt);
   
   // echo '<br>$excerpt<br>'.  htmlspecialchars($excerpt);
   
   ###  strip tags
   $tagStripped = preg_replace('~</?.+?>~', "", $excerpt);
   
   ###  strip spaces
   $sanitizedTxt = trim($tagStripped);
   
   ###  substringing
   $newExcerpt = mb_substr($sanitizedTxt, 0, $optionsLen);
   
   ###############################
   ### Implementarion Plan.1  ####
   # nre-surfix tags previously stripped 
   #
   
   return "<p>{$newExcerpt}</p>";
}
   
   
add_filter('the_excerpt', 'azm_chnageExcerptLength', 999);

