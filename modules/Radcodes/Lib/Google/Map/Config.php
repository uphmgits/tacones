<?php

  class Radcodes_Lib_Google_Map_Config {
    
    static public function get($key, $default=null)
    {
      $values = array();
      
      $values['app_google_maps_api_keys'] = array(
       'localhost' => 'ABQIAAAAn7JlXEBJe1AcvBzQ9vBUHhT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSe4GuT3Knr9hNRzro0oBmg1BBTZA',
       'default' => 'ABQIAAAAn7JlXEBJe1AcvBzQ9vBUHhT2yXp_ZAY8_ufC3CFXhHIE1NvwkxSe4GuT3Knr9hNRzro0oBmg1BBTZA'
      );

      if (isset($values[$key])) {
        return $values[$key];
      }
      throw new Radcodes_Lib_Google_Map_Exception("Radcodes_Lib_Google_Map_Config::get key not exist = $key=");
    }
    
  }