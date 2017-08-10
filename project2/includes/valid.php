<?php

function cleanup_input($input) {
/* Clean up input: trim, strip tags, htmlspecialchars */
	$input = trim($input);
	$input = strip_tags($input);
	$input = htmlspecialchars($input);
	return $input;
}

function validate_input($input, $field, $minlength, $maxlength) {
	/* Validates form input.

	Parameters:
	$input: a variable that holds a string
	$field: a string
	$minlength, $maxlength: non-negative integers

	*/
	if (empty($input)) {
		$error = "Enter a $field<br>";
	} elseif(strlen($input) < $minlength) {
		$error = "Your $field must be over $minlength characters.<br>";
	} elseif(strlen($input) > $maxlength) {
		$error = "Your $field must be under $maxlength characters.<br>";
	} else {
		$error = "";
	}
	return $error;
}

function validate_url($url) {
/* Validates URL with php's built-in filter, as well as checking to make sure
there is a domain suffix, indicating a valid url (which the filter doesn't check for)

For example: 'http://google' will get past FILTER_VALIDATE_URL, but 'google.com' will not. 
This function makes it so the reverse will be the case. */

	/* List of common domain suffixes and country codes (from Wikipedia's list of internet top-level domains) */
	$domains = array('.com', '.org', '.net', '.edu', '.gov', '.biz', '.info', '.ac', '.ad', '.ae', '.af', '.ag', '.ai', '.al', '.am', '.an', '.ao', '.aq', '.ar', '.as', '.at', '.au', '.aw', '.ax', '.az', '.ba', '.bb', '.bd', '.be', '.bf', '.bg', '.bh', '.bi', '.bj', '.bm', '.bn', '.bo', '.bq', '.br', '.bs', '.bt', '.bv', '.bw', '.by', '.bz', '.ca', '.cc', '.cd', '.cf', '.cg', '.ch', '.ci', '.ck', '.cl', '.cm', '.cn', '.co', '.cr', '.cs', '.cu', '.cv', '.cw', '.cx', '.cy', '.cz', '.dd', '.de', '.dj', '.dk', '.dm', '.do', '.dz', '.ec', '.ee', '.eg', '.eh', '.er', '.es', '.et', '.eu', '.fi', '.fj', '.fk', '.fm', '.fo', '.fr', '.ga', '.gb', '.gd', '.ge', '.gf', '.gg', '.gh', '.gi', '.gl', '.gm', '.gn', '.gp', '.gq', '.gr', '.gs', '.gt', '.gu', '.gw', '.gy', '.hk', '.hm', '.hn', '.hr', '.ht', '.hu', '.id', '.ie', '.il', '.im', '.in', '.io', '.iq', '.ir', '.is', '.it', '.je', '.jm', '.jo', '.jp', '.ke', '.kg', '.kh', '.ki', '.km', '.kn', '.kp', '.kr', '.kw', '.ky', '.kz', '.la', '.lb', '.lc', '.li', '.lk', '.lr', '.ls', '.lt', '.lu', '.lv', '.ly', '.ma', '.mc', '.md', '.me', '.mg', '.mh', '.mk', '.ml', '.mm', '.mn', '.mo', '.mp', '.mq', '.mr', '.ms', '.mt', '.mu', '.mv', '.mw', '.mx', '.my', '.mz', '.na', '.nc', '.ne', '.nf', '.ng', '.ni', '.nl', '.no', '.np', '.nr', '.nu', '.nz', '.om', '.pa', '.pe', '.pf', '.pg', '.ph', '.pk', '.pl', '.pm', '.pn', '.pr', '.ps', '.pt', '.pw', '.py', '.qa', '.re', '.ro', '.rs', '.ru', '.rw', '.sa', '.sb', '.sc', '.sd', '.se', '.sg', '.sh', '.si', '.sj', '.sk', '.sl', '.sm', '.sn', '.so', '.sr', '.ss', '.st', '.su', '.sv', '.sx', '.sy', '.sz', '.tc', '.td', '.tf', '.tg', '.th', '.tj', '.tk', '.tl', '.tm', '.tn', '.to', '.tp', '.tr', '.tt', '.tv', '.tw', '.tz', '.ua', '.ug', '.uk', '.us', '.uy', '.uz', '.va', '.vc', '.ve', '.vg', '.vi', '.vn', '.vu', '.wf', '.ws', '.ye', '.yt', '.yu', '.za', '.zm', '.zr', '.zw');
	// put an http in front of the URL if there isn't one already:
	$url = format_url($url);
	// use the built-in URL filter
	if (filter_var($url, FILTER_VALIDATE_URL)) {
		// if it passes, check for domain suffix
		foreach ($domains as $d) {
			if (strpos($url, $d)) {
				return true;
			}
		}
	} else {
		return false;
	}
}

?>
