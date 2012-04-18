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
 


class Radcodes_Lib_Helper_Unit
{
	
	const FACTOR_ML_TO_KM = 1.609344;
	
	const UNIT_MILE = 'ml';
	const UNIT_KILOMETER = 'km';
	
	static public function kilometerToMile($value)
	{
		return $value / self::FACTOR_ML_TO_KM;
	}
	
	static public function mileToKilometer($value)
	{
		return $value * self::FACTOR_ML_TO_KM;
	}

}

