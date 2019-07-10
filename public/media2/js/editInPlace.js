/*
 * Another In Place Editor - a jQuery edit in place plugin
 *
 * Copyright (c) 2009 Dave Hauenstein
 *
 * License:
 * This source file is subject to the BSD license bundled with this package.
 * Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
 * If you did not receive a copy of the license, and are unable to obtain it,
 * email davehauenstein@gmail.com,
 * and I will send you a copy.
 *
 * Project home:
 * http://code.google.com/p/jquery-in-place-editor/
 *
 */
$(document).ready(function(){
	
	// All examples use the commit to function interface for ease of demonstration.
	// If you want to try it against a server, just comment the callback: and 
	// uncomment the url: lines.
	
	// The most basic form of using the inPlaceEditor
	$("#editme1").editInPlace({
		callback: function(unused, enteredText) { return enteredText; },
		params: "name=david",
		url: "http://com.examplesite.www/users",
		show_buttons: true,
		success: function(response) {
			// stuff
			alert('success with response: ' + response);
		}
	});
});