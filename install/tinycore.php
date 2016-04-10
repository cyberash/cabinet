<?php
/*
 *      tinycore.php
 *      
 *      Copyright 2011 Artem Zhirkov <artemz@artemz-desktop>
 *      
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

?>
<?php
require_once("../config.php");
define('iVersion', '1.6.0alpha5'); # Program Version
define('MIN_PHP_VERSION', '5.3.0');
define('MIN_MYSQL_VERSION', '5');
define('iDEBUG', true);
define('DBFILE', 'sql/mc.sql');
define('BR', "<br />");


//yeah, I'm stuped
function __autoload($class_name) {
	$libm = 'classes/class.'.$class_name.'.php';
	if(!file_exists($libm)) trigger_error("can't find $libm", E_USER_ERROR);
	require($libm);
}

//ini_manager::getInstance()->filename = '../../lib/configuration.ini';

function splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;

		// Trim any whitespace.
		

		// Remove comment lines.
		$sql = preg_replace("/\n\/\*[^\n]*|\n#[^\n]*|\n--[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			if ($sql[$i] == ";" && !$in_string) {
				$queries[] = trim(substr($sql, 0, $i));
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}
		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
			$queries[] = trim($sql);
		}

		return $queries;
	}
?>
