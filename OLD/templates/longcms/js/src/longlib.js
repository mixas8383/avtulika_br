/**
 * @package                LongCMS.Site
 * @subpackage	Templates.longcms
 * @copyright        Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
 * @license                GNU General Public License version 2 or later; see LICENSE.txt
 */


/**
 * Alias for console log + backtrace
 * @param vars
 * @param name String
 * @param showTrace Boolean|int
 */

function dump(vars, name, showTrace) {

	// is console exists
	if (typeof console == 'undefined') {
		return false;
	}

	// get type
	if (typeof vars == 'string' || typeof vars == 'array') {
		var type = ' (' + typeof(vars) + ', ' + vars.length + ')';
	} else {
		var type = ' (' + typeof(vars) + ')';
	}

	// wrap in vars quote if string
	if (typeof vars == 'string') {
		vars = '"' + vars + '"';
	}

	// get var name
	if (typeof name == 'undefined') {
		name = '...' + type + ' = ';
	} else {
		name += type + ' = ';
	}

	// is show trace in console
	if (typeof showTrace == 'undefined') {
		showTrace = false;
	}

	// dump var
	console.log(name, vars);

	// show console
	if (showTrace) {
		console.trace();
	}

	return true;
}

/**
 * Cheack IE version
 * @param version
 */

function isIE(version) {
	if (typeof version != 'undefined') {
		return (jQuery.browser.msie && parseInt(jQuery.browser.version) == version);
	} else {
		return jQuery.browser.msie;
	}
}

/**
 * Set number format (as PHP function)
 * @param number String|int|float
 * @param decimals String
 * @param point String
 * @param separator String
 */

function numberFormat(number, decimals, point, separator) {

	if (isNaN(number)) {

		return (null);
	}

	point = point ? point : '.';
	number = new String(number);
	number = number.split('.');

	if (separator) {

		var tmpNumber = new Array();

		for (var i = number[0].length, j = 0; i > 0; i -= 3) {
			var pos = i > 0 ? i - 3 : i;
			tmpNumber[j++] = number[0].substring(i, pos);
		}

		number[0] = tmpNumber.reverse().join(separator);
	}

	if (decimals) {

		number[1] = number[1] ? number[1] : '';
		number[1] = Math.round(parseFloat(number[1].substr(0, decimals) + '.' + number[1].substr(decimals, number[1].length), 10));

		if (isNaN(number[1])) {
			number[1] = '';
		}

		var k = decimals - number[1].toString().length;
		for (var i = 0; i < k; i++) {
			number[1] += '0';
		}
	}

	return (number.join(point));
}

/**
 * Check is variable empty
 * @link http://phpjs.org/functions/empty:392
 * @param mixedVar
 * @return Boolean
 */

function empty(mixedVar) {

	// check simple var
	if (typeof mixedVar === 'undefined' || mixedVar === "" || mixedVar === 0 || mixedVar === "0" || mixedVar === null || mixedVar === false) {
		return true;
	}

	// check object
	if (typeof mixedVar == 'object') {
		for (var key in mixedVar) {
			return false;
		}

		return true;
	}

	return false;
}

/**
 * Count object properties
 * @param object Object
 */

function countProps(object) {
	var count = 0;

	for (var property in object) {

		if (object.hasOwnProperty(property)) {
			count++;
		}
	}

	return count;
}

/**
 * Check value is numeric
 * @param mixedVar
 * @return Boolean
 */

function isNumeric(mixedVar) {
	return (typeof(mixedVar) === 'number' || typeof(mixedVar) === 'string') && mixedVar !== '' && !isNaN(mixedVar);
}


function keepAlive() {
	$.get("index.php");
}

function updateURLParameter(url, param, paramVal) {
	var TheAnchor = null;
	var newAdditionalURL = "";
	var tempArray = url.split("?");
	var baseURL = tempArray[0];
	var additionalURL = tempArray[1];
	var temp = "";

	if (additionalURL) {
		var tmpAnchor = additionalURL.split("#");
		var TheParams = tmpAnchor[0];
		TheAnchor = tmpAnchor[1];
		if (TheAnchor) additionalURL = TheParams;

		tempArray = additionalURL.split("&");

		for (i = 0; i < tempArray.length; i++) {
			if (tempArray[i].split('=')[0] != param) {
				newAdditionalURL += temp + tempArray[i];
				temp = "&";
			}
		}
	} else {
		var tmpAnchor = baseURL.split("#");
		var TheParams = tmpAnchor[0];
		TheAnchor = tmpAnchor[1];

		if (TheParams) baseURL = TheParams;
	}

	if (TheAnchor) paramVal += "#" + TheAnchor;

	var rows_txt = temp + "" + param + "=" + paramVal;
	return baseURL + "?" + newAdditionalURL + rows_txt;}