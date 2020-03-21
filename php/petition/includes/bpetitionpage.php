<?php
/**
* @file petitionpage.php
* Purpose: creates petition
* Extends MainPage Class
*
* @author Keith Gudger
* @copyright  (c) 2020, Keith Gudger, all rights reserved
* @license    http://opensource.org/licenses/BSD-2-Clause
* @version    Release: 1.0
* @package    SOS
*
* @note Has processData and showContent, 
* main and checkForm in MainPage class not overwritten.
* 
*/

/**
 * Display the content of the page.
 *
 * @param $title is page title.
 * @param $uid is user id passed by reference.
 */
function showContent() {

	$ret .= <<< EOT
<div id="bpetdiv">
	<input type='button' onclick='language()' id='langbut' value='Haga clic para español'></input><br>
	<fieldset id="bpetform" name="petition">
		<div id = 'banner'>
			<h3>We the undersigned PETITION our local and regional LEGISLATORS to:</h3>
			<ul>
				<li>ACT to stop this ocean pollution and climate change accelerator</li>
				<li>PASS LEGISLATION to ban the sale of all single serve beverages in plastic bottles</li>
			</ul>
			
			Please Select Where You Live (required)<br>
		</div>
		<input type="hidden" id="blang" value="English" >
		<select name="reside" id='rselect'>
			<option value="-1" selected>Please Select</option>
			<option value="1">I am a registered voter in the County of Santa Cruz.</option>
			<option value="2">I am a registered voter in the City of Santa Cruz.</option>
			<option value="3">I am a registered voter in the City of Capitola.</option>
			<option value="4">I am a registered voter in the City of Scotts Valley.</option>
			<option value="5">I am a registered voter in the City of Watsonville.</option>
		</select><br>
		<div id="berror">Please Enter Your Data Below<br></div>
		<input type="text" name="name" value="" id="bname">
			<label id="lname" for="name">Your Name (required)</label><br>
		<input type="email" name="email" value="" id="bemail">
			<label id="lemail" for="email">Your Email (required)</label><br>
		<input type="text" name="street" value="" id="bstreet">
			<label id="lstreet" for="street">Your Street Address (required)</label><br>
		<input type="text" name="city" value="" id="bcity">
			<label id="lcity" for="city">Your City (required)</label><br>
		<input type="text" name="zip" value="" id="bzip">
			<label id="lzip" for="zip">Your Zip Code (required)</label><br>
		<input type="checkbox" name="check[]" value="Yes" id="bcheck"> 
			<label id="agree" for="check">Check here if you agree with this petition (required)</label><br>
		<input type="submit" id="bsub" name="Submit" value="Submit" onclick="sendData()">
	</fieldset>
</div>
EOT;

	return $ret;
}
