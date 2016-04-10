<?php
/**
    MultiCabinet - billing system for WHM panels.
    Copyright (c) 2008, Vladimir M. Andreev. All rights reserved.

    This file is part of MultiCabinet billing system.

    MultiCabinet is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    MultiCabinet is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
**/

if (!defined('iSELF')) { header('Location: index.php'); exit; }

if (null === Filter::$_unicodeEnabled) {
	Filter::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
}

class Filter {

public static $_unicodeEnabled;

//function __construct() {
//}

/**
 * Returns the string $value, removing all but digit characters
 *
 * @param  string $value
 * @return string
 */
public static function digit_filter($value) {
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative 0-9 match
            $pattern = '/[^0-9]/';
        } elseif (extension_loaded('mbstring')) {
            // Filter for the value with mbstring
            $pattern = '/[^[:digit:]]/';
        } else {
            // Filter for the value without mbstring
            $pattern = '/[\p{^N}]/';
        }

        return preg_replace($pattern, '', (string) $value);
}

/**
 * Returns the string $value, removing all but alphabetic and digit characters
 *
 * @param  string $value
 * @return string
 */
public static function alnum_filter($value,$whiteSpace=false) {
	$whiteSpace = $whiteSpace ? '\s' : '';
/*
	if (!self::$_unicodeEnabled) {
		// POSIX named classes are not supported, use alternative a-zA-Z0-9 match
		$pattern = '/[^a-zA-Z0-9' . $whiteSpace . ']/';
	} elseif (extension_loaded('mbstring')) {
		// Unicode safe filter for the value with mbstring
		$pattern = '/[^[:alnum:]'  . $whiteSpace . ']/u';
        } else {
		// Unicode safe filter for the value without mbstring
		$pattern = '/[^\p{L}\p{N}' . $whiteSpace . ']/u';
	}
*/
		$pattern = '/[^a-zA-Z0-9]/';
	return preg_replace($pattern, '', (string) $value);
}

/**
 *
 * Returns the string $value, removing all but alphabetic characters
 *
 * @param  string $value
 * @return string
 */
public static function alpha_filter($value,$whiteSpace=false) {
	$whiteSpace = $whiteSpace ? '\s' : '';
	if (!self::$_unicodeEnabled) {
		// POSIX named classes are not supported, use alternative a-zA-Z match
		$pattern = '/[^a-zA-Z' . $whiteSpace . ']/';
	} elseif (extension_loaded('mbstring')) {
		// Unicode safe filter for the value with mbstring
		$pattern = '/[^[:alpha:]' . $whiteSpace . ']/u';
	} else {
		// Unicode safe filter for the value without mbstring
		$pattern = '/[^\p{L}' . $whiteSpace . ']/u';
	}

}

}