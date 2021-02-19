<?php

/**
 * Plugin Name: +change excerpt length using built-in 'settings > writing' admin panel
 * Description: ?Echoing Text in Every Pages
 */

 
##################
# (Backend)
# - Registoration(the custom option)
# - user input, save input to DB
##################

### register, render settings title, field
function render_excerptChanger_section($args){
  echo "<u><i>current length:</i></u>";
}
function render_excerptChanger_lengthField($args){
   $val = get_option('azmok_excerptChanger');
   $expr = isset( $val ) ? esc_attr( $val ) : "";
   $html = "<input type='text' placeholder='Enter Length' name='azmok_excerptChanger-length-field' value='{$expr}' />";
   ##
   // 'name attribute must match against $option_name of 'register_setting($page, $option_$name)
   //   because it automatically identify options value and saved to DB, and enable to
   //   use 'get_option()' to retrieve the custome option's value. 
   
   echo $html;
}

function render_excerptChanger_symbolField($args){
   $symbol = get_option('azmok_excerptChanger-symbol-field');
   $expr = isset( $symbol ) ? esc_attr( $symbol ) : "...";
   $html = "<input type='text' placeholder='Enter 
      symbol' name='azmok_excerptChanger-symbol-field' value='{$expr}' />";
   
   echo $html;
}

function register_excerptChanger(){
   
   ################
   # 'length' for excerpt
   ################
   ###  Register custom option
   register_setting('reading', 'azmok_excerptChanger-length-field');
   //                $page   ,  $option_name
   
   ###  add Section
   add_settings_section(
      'azmok_excerptChanger-section', // in HTML, this value used as 'section's id attribute value 
                                //   In addition, this is used as identifier in PHP.
      
      'Change Excerpt',  // section title in HTML.
      
      'render_excerptChanger_section', // name of callback function to render HTML.
      
      'reading' // built-in settings pages name('general', 'discussion', 'media', 'reading',
                //   'writing', 'misc', 'options', 'privacy')
   );
   
   ###  add Field
   add_settings_field(
      'azmok_excerptChanger-length-field',
         
      'Current Excerpt Length',
      'render_excerptChanger_lengthField',
      'reading',
      'azmok_excerptChanger-section' // parent section id in order to append this field. If not specified, this field will append to 'default' section.
   );
   
   
   
   
   
   ################
   # 'Symbol' for Excerpt
   ################
   register_setting('reading', 'azmok_excerptChanger-symbol-field');
   
   add_settings_field(
      'azmok_excerptChanger-symbol-field',
      'Current symbol',
      'render_excerptChanger_symbolField',
      'reading',
      'azmok_excerptChanger-section'
   )
}
add_action('admin_init', 'register_excerptChanger');



function azmok_registerExcerptChanger_


##################
# (Frontend)
# - chnage excerpt length using DB data(user inputted)
##################
function azmok_changeExcerpt( $excerpt ){
   $optionsLen = get_option('azmok_excerptChanger-length-field');
   $optionsSymbol = get_option('azmok_excerptChanger-symbol-field');
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
   
   return "<p>{$newExcerpt}{$optionsSymbol}</p>";
}
   
   
add_filter('the_excerpt', 'azmok_changeExcerpt', 999);