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
 


class Radcodes_Lib_Helper_Date
{
	/**
	 * Retrieve info for inputted +timestamp+
	 */
	static public function archive($timestamp, $key = null)
	{
		
    $time = time();
    $ltime = localtime($timestamp, TRUE);
    $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
    $ltime["tm_year"] = $ltime["tm_year"] + 1900;
    
    // LESS THAN A YEAR AGO - MONTHS
    if( $timestamp+31536000>$time )
    {
      $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
      $date_end = mktime(0, 0, 0, $ltime["tm_mon"]+1, 1, $ltime["tm_year"]);
      $label = date('F Y', $timestamp);
      $type = 'month';
    }
    // MORE THAN A YEAR AGO - YEARS
    else
    {
      $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
      $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]+1);
      $label = date('Y', $timestamp);
      $type = 'year';
    }
    
    $info = array(
          'type' => $type,
          'label' => $label,
          'date_start' => $date_start,
          'date_end' => $date_end,
    );
		
	  if ($key !== null)
	  {
	  	return $info[$key];
	  }
	  
	  return $info;
	}
	
}

