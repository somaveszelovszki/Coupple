<?php

/**
 * This file contains the common php functions
 */

/**
 * explodes full names (separated by spaces) to an array containing single names
 * e.g. 'George Example Washington' will become array('George', 'Example', 'Washington')
 * @param string $name
 * @return string[]
 */
function explodeFullName($name) {
	// removes multiple spaces then explodes text by spaces -> no empty values in result array
	return explode(' ', preg_replace('/\s+/i', ' ', $name));
}

/**
 * Creates URI text from text
 * URI pattern: base url + 'index.php?/controller/method
 * @param string $text
 * @returns string
 */
function createURIText($text) {
	// removes base url and 'index.php?' - with or without question mark
	return base_url() . 'index.php?/' . str_replace(base_url(), '', preg_replace('/index\\.php(\\?)?/', '', $text));
}

/**
 * @param string $dateTime - e.g. '2015-10-20 23-45-01'
 * @return bool|string date string - nice and readable for users - e.g. 'October 20 2015'
 */
function createDateString($dateTime) {
	// first 10 characters of datetime string contains date
	return date("F j Y", strtotime(substr($dateTime, 0, 10)));
}

/**
 * Converts string to fit in MySQL queries as column name -> replaces capital letters with an underscore and lowercase letter
 * e.g. 'relationshipStateId' will become 'relationship_state_id'
 *
 * @param string $columnName
 * @return string
 */
function strToMySQL($columnName) {
	return strtolower(preg_replace('/([A-Z])/', '_$1', $columnName));
}

/**
 * Converts MySQL column name to Javascript object key string  -> replaces an underscore and lowercase letter with capital letter
 * e.g. 'relationship_state_id' will become 'relationshipStateId'
 * @param string $columnName
 * @return string
 */
function strToJavasript($columnName) {
	return preg_replace_callback('/_([a-zA-Z])/', function ($matches) {
		return strtoupper($matches[1]);		// $matches[1] contains letter after '_'
	}, $columnName);
}

/**
 * Toggles object keys between strings that fit to MySQL queries as column names and Javascript object keys
 * -> replaces capital letters with an underscore and lowercase letter
 * e.g. $options['relationshipStateId'] will become $options['relationship_state_id']
 * @param array $object
 * @param string $convertTo
 * @param bool|true $allLevelSearch determines if search should continue to all levels (member objects in object)
 * @return array
 */
function toggleObjectKeyNames($object, $convertTo, $allLevelSearch = true) {
	if (!is_array($object)) {
		return $object;
	}

	foreach ($object as $key => $value) {
		if (is_array($value) && $allLevelSearch) {		// going through all levels if enabled
			$object[$key] = toggleObjectKeyNames($value, $convertTo, true);
		}
		// storing variable
		$temp = $object[$key];
		// deleting from object
		unset($object[$key]);
		//creates new element in object with converted key name
		switch (strtolower($convertTo)) {
			case 'mysql':
				$object[strToMySQL($key)] = $temp;
				break;
			case 'javascript':
				$object[strToJavasript($key)] = $temp;
				break;
		}
	}
	return $object;
}

/**
 * Converts object keys to fit into MySQL query
 * @param Object $object
 * @param bool|true $allLevelSearch determines if search should continue to all levels (member objects in object)
 * @return array
 */
function objectKeysToMySQL($object, $allLevelSearch = true) {
	return toggleObjectKeyNames($object, 'mysql', $allLevelSearch);
}

/**
 * Converts object keys to Javascript style
 * @param array $object
 * @param bool|true $allLevelSearch determines if search should continue to all levels (member objects in object)
 * @return array
 */
function objectKeysToJavascript($object, $allLevelSearch = true) {
	return toggleObjectKeyNames($object, 'javascript', $allLevelSearch);
}

/**
 * Determines whether person1, person2 or both people (couple) logged in
 * @param array $personIds
 * @return null|string
 */
function determineAccessId($personIds) {
	if (is_array($personIds)) {
		switch (count($personIds)) {
			case 0:		// something failed
				return null;
			case 1:		// only one person logged in
				return isset($personIds['1']) ? $personIds['1'] : $personIds['2'];
			case 2:		// both people together logged in (as couple)
				return 'couple';
		}
	}
	return null;
}

function numberStringsToNumbers ($object) {
	if (!is_array($object)) {
		return $object;
	}

	foreach ($object as $key => $value) {
		if (is_array($value)) {		// going through all levels
			$object[$key] = numberStringsToNumbers($value);
		} else {

			/** Regexp matches numbers (e.g. '1', '12.4', '.7')
			 * DOES NOT MATCH: '12.', '', ' '
			 * Regexp explanation:
			 * [\\d]*	->	matches 0 or more digits
			 * [\\.]*	->	matches 0 or more dots
			 * [\\d]+	->	matches 1 or more digits
			 */
			$numberRegexp = '/^[\\d]*[\\.]*[\\d]+$/';

			$object[$key] = preg_match($numberRegexp, $value) ? (int) $value : $value;
		}
	}
	return $object;
}