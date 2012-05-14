<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package  Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license  http://www.radcodes.com/license/
 * @version  $Id$
 * @author   Vincent Van <vincent@radcodes.com>
 */
 


class Radcodes_Lib_Helper_Text
{
	/**
	 * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
	 * if the +text+ is longer than +length+.
	 */
	static public function truncate($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
	{
	  if ($text == '')
	  {
	    return '';
	  }
	
	  $mbstring = extension_loaded('mbstring');
	  if($mbstring)
	  {
	   $old_encoding = mb_internal_encoding();
	   @mb_internal_encoding(mb_detect_encoding($text));
	  }
	  $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
	  $substr = ($mbstring) ? 'mb_substr' : 'substr';
	
	  if ($strlen($text) > $length)
	  {
	    $truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
	    if ($truncate_lastspace)
	    {
	      $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
	    }
	    $text = $truncate_text.$truncate_string;
	  }
	
	  if($mbstring)
	  {
	   @mb_internal_encoding($old_encoding);
	  }
	
	  return $text;
	}
	
	/**
	 * Word wrap long lines to line_width.
	 */
	static public function wrap($text, $line_width = 80)
	{
	  return preg_replace('/(.{1,'.$line_width.'})(\s+|$)/s', "\\1\n", preg_replace("/\n/", "\n\n", $text));
	}	
	
	
	static public function slugify($text, $options = array())
	{
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	 
	  // trim
	  $text = trim($text, '-');
	 
	  // transliterate
	  if (function_exists('iconv'))
	  {
	    $text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	  }
	 
	  // lowercase
	  $text = strtolower($text);
	 
	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	 
	  if (empty($text))
	  {
	    return 'n-a';
	  }
	 
	  return $text;
	}
	

}

