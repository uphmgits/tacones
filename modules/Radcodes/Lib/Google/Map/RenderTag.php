<?php

/**
 * 
 * Copyright (c) 2008 Fabrice Bernhard
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * 
 * Class that compiles some functions stolen from sfWidget.class.php
 * @copyright Fabien Potencier
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 
   */
   
  class Radcodes_Lib_Google_Map_RenderTag
  {
    /**
     * Renders a HTML tag.
     *
     * @param string $tag         The tag name
     * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     *
     * @param string An HTML tag string
     */
    static public function render($tag, $attributes = array())
    {
      if (empty($tag))
      {
        return '';
      }
  
      return sprintf('<%s%s />', $tag, self::attributesToHtml($attributes));
    }
  
    /**
     * Renders a HTML content tag.
     *
     * @param string $tag         The tag name
     * @param string $content     The content of the tag
     * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     *
     * @param string An HTML tag string
     */
    static public function renderContent($tag, $content = null, $attributes = array())
    {
      if (empty($tag))
      {
        return '';
      }
  
      return sprintf('<%s%s>%s</%s>', $tag, self::attributesToHtml($attributes), $content, $tag);
    }
  
    /**
     * Escapes a string.
     *
     * @param  string $value  string to escape
     * @return string escaped string
     */
    static public function escapeOnce($value)
    {
      $value = is_object($value) ? $value->__toString() : (string) $value;
  
      return self::fixDoubleEscape(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
  
    /**
     * Fixes double escaped strings.
     *
     * @param  string $escaped  string to fix
     * @return string single escaped string
     */
    static public function fixDoubleEscape($escaped)
    {
      return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
    }
  
    /**
     * Converts an array of attributes to its HTML representation.
     *
     * @param  array  $attributes An array of attributes
     *
     * @return string The HTML representation of the HTML attribute array.
     */
    static public function attributesToHtml($attributes)
    {
  
      return implode('', array_map(array('Radcodes_Lib_Google_Map_RenderTag', 'attributesToHtmlCallback'), array_keys($attributes), array_values($attributes)));
    }
  
    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * @param  string $k  The attribute key
     * @param  string $v  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    static protected function attributesToHtmlCallback($k, $v)
    {
      return is_null($v) || '' === $v ? '' : sprintf(' %s="%s"', $k, self::escapeOnce($v));
    }
  }