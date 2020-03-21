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

require_once("mainpage.php");
//include_once "util.php";

/**
 * Child class of MainPage used for user preferrences page.
 *
 * Implements processData and showContent
 */


class petitionPage extends MainPage {

/**
 * Process the data and insert / modify database.
 *
 * @param $uid is user id passed by reference.
 */

function processData(&$uid) {
$residelist = array("Please Select"=>-1,"I am a registered voter in the County of Santa Cruz."=>1,"I am a registered voter in the City of Santa Cruz."=>2,"I am a registered voter in the City of Capitola."=>3,"I am a registered voter in the City of Scotts Valley."=>4,"I am a registered voter in the City of Watsonville."=>5);
	$name   = $this->formL->getValue("name");
	$email  = $this->formL->getValue("email");
	$street = $this->formL->getValue("street");
	$city   = $this->formL->getValue("city");
	$zip    = $this->formL->getValue("zip");
	$agree  = $this->formL->getValue("check");
	$agree  = $agree[0];
	$lang   = $this->formL->getValue("lang");
	$locale = $this->formL->getValue("reside");
	$locale = array_search($locale,$residelist);

//	$ret = $name . ", " . $email . ", " . $street . ", " . $city . ", " . $zip . ", " . $locale . ", " . $agree . ", " . $lang; 
	if ($lang == "English") {
		$ret = "Thank you for signing our petition.<br>";
	} else {
		$ret = "Gracias por firmar nuestra petición.<br>";
	}	
	return $ret;
}

/**
 * Display the content of the page.
 *
 * @param $title is page title.
 * @param $uid is user id passed by reference.
 */
function showContent($title, &$uid) {
$residelist = array("Please Select"=>-1,"I am a registered voter in the County of Santa Cruz."=>1,"I am a registered voter in the City of Santa Cruz."=>2,"I am a registered voter in the City of Capitola."=>3,"I am a registered voter in the City of Scotts Valley."=>4,"I am a registered voter in the City of Watsonville."=>5);

	$ret .=  "<input type='button' onclick='language()' id='langbut' value='Haga clic para español'></input><br>";
	$ret .=  $this->formL->reportErrors();
//	$ret .=  $this->formL->start('POST', "", 'name="petition"');
	$ret .= '<form method="POST" name="petition" action="">';
	$ret .= <<< EOT
<div id = 'banner'>
<h3>We the undersigned PETITION our local and regional LEGISLATORS to:</h3>
<ul>
  <li>ACT to stop this ocean pollution and climate change accelerator</li>
  <li>PASS LEGISLATION to ban the sale of all single serve beverages in plastic bottles</li>
</ul>
Please Select Where You Live (required)<br>
</div>
EOT;
	$ret .=  $this->formL->makeHidden('lang', "English"); 
	$ret .=  $this->formL->makeSelect('reside', $residelist,-1,"id='rselect'"); 
	$ret .=  "<br>";
	$ret .=  $this->formL->makeTextInput('name');
	$ret .=  '<label id="lname" for="name">Your Name (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeEmailInput('email');
	$ret .=  '<label id="lemail" for="email">Your Email (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeTextInput('street');
	$ret .=  '<label id="lstreet" for="street">Your Street Address (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeTextInput('city');
	$ret .=  '<label id="lcity" for="city">Your City (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeTextInput('zip');
	$ret .=  '<label id="lzip" for="zip">Your Zip Code (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeCheckBoxes('check',array(""=>"Yes"));
	$ret .=  '<label id="agree" for="check">Check here if you agree with this petition (required)</label>';
	$ret .=  "<br>";
	$ret .=  $this->formL->makeButton("Submit","Submit");
	$ret .= $this->formL->finish();

	return $ret;
}
}
?>

