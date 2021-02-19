<?php


/**
 * Plugin Name: Class Excerpt Customizer
 * Description: customize excerpt length and symbol from 'Settings > Reading' in admin dashboard.
 */



class Setting{
   private $settingName = "";
   private $targetPage = "general";
   private $section = Null;
   private $field = Null;
   private $sectionIsSet = false;
   private $fieldIsSet = false;
   
   
   function __construct($name){
      $this->settingName = $name;
      $this->section = new Section($name);
      $this->field = new Field($name);
   }
   
   function setTargetPage($pageName){
      $this->targetPage = $pageName;
      $this->section->setTargetPage($pageName);
      $this->field->setTargetPage($pageName);
      
      return $this;
   }
   
   function setSection($assoc){
      $txt = $assoc["text"];
      $renderer =  $assoc["renderer"];
      $section = $this->section;
      
      $section->setText($txt);
      $section->setRenderer($renderer);
      
      // switch flag to 'true'
      $this->sectionIsSet = true;
      
      return $this;
   }
   
   function setField($assoc){
      $txt = $assoc["text"];
      $renderer =  $assoc["renderer"];
      $field = $this->field;
      
      $field->setText($txt);
      $field->setRenderer($renderer);
      
      // switch flag to 'true'
      $this->fieldIsSet = true;
      
      return $this;
   }
   
   function register(){
      $section = $this->section;
      $field = $this->field;
      
      register_setting(
         $this->targetPage,
         $this->settingName
      );
      
      if( $this->sectionIsSet ) $section->register();
      if( $this->fieldIsSet ) $field->register();
      
      return $this;
   }
   
}
   
class Section{
   private $sectionId = "";
   private $txt = "";
   private $renderer = Null;
   private $targetPage = "general";
   
   function __construct($id){
      $this->sectionId = $id  ."-section";
   }
   
   function setText($txt){
      $this->txt = $txt;
   }
   
   function setTargetPage($target){
      $this->targetPage = $target;
   }
   
   function setRenderer($fn){
      $this->renderer = $fn;
   }
   
   function register(){
      add_settings_section(
         $this->sectionId,
         $this->txt,
         $this->renderer,
         $this->targetPage
      );
   }
}

class Field{
   private $fieldId = "";
   private $txt = "";
   private $renderer = Null;
   private $targetPage = "general";
   private $parentSection = "default";
   
   function __construct($id){
      $this->fieldId = $id;
   }
   
   function setText($txt){
      $this->txt = $txt;
   }
   
   function setTargetPage($target){
      $this->targetPage = $target;
   }
   
   function setRenderer($fn){
      $this->renderer = $fn;
   }
   
   function setParentSection($sectionName){
      $this->parentSection = $sectionName;
   }
   
   function register(){
      add_settings_field(
         $this->fieldId,
         $this->txt,
         $this->renderer,
         $this->targetPage,
         $this->parentSection
      );
   }
}


function renderer_section($args){
  echo "<u><i>current length:</i></u>";
}

function renderer_length($args){
   $val = get_option('azmok-excerpt-length');
   $expr = isset( $val ) ? esc_attr( $val ) : 20;
   $html = "<input type='text' placeholder='Enter Length' name='azmok-excerpt-length' value='{$expr}' />";
   
   echo $html;
}

function renderer_symbol($args){
   $symbol = get_option('azmok-excerpt-symbol');
   $expr = isset( $symbol ) ? $symbol : "...";
   $html = "<input type='text' placeholder='Enter symbol' name='azmok-excerpt-symbol' value='{$expr}' />";
   
   echo $html;
}


add_action('admin_init', function(){
###  length
$Setting_excerptLen = new Setting('azmok-excerpt-length');
$Setting_excerptLen
   ->setTargetPage('reading')
   ->setSection([
      "text" => "Current length:",
      "renderer" => 'renderer_section',
   ])
   ->setField([
      "text" => "Current length:",
      "renderer" => 'renderer_length',
   ])
   ->register();

###  symbol
$Setting_excerptSym = new Setting('azmok-excerpt-symbol');
$Setting_excerptSym
   ->setTargetPage('reading')
   ->setField([
      "text" => "Current symbol:",
      "renderer" => 'renderer_symbol',
   ])
   ->register();
      
      
});



##################
# (Frontend)
# - chnage excerpt length using DB data(user inputted)
##################
function azmok_changeExcerpt( $excerpt ){
   $length = get_option('azmok-excerpt-length');
   $symbol = get_option('azmok-excerpt-symbol');
   
   ###  strip tags
   $tagStripped = preg_replace('~</?.+?>~', "", $excerpt);
   
   ###  strip spaces
   $sanitizedTxt = trim($tagStripped);
   
   ###  substringing
   $newExcerpt = mb_substr($sanitizedTxt, 0, $length);
   
   return "<p>{$newExcerpt}{$symbol}</p>";
}
   
add_filter('the_excerpt', 'azmok_changeExcerpt', 999);