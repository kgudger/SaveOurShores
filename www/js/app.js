/*
 * Please see the included README.md file for license terms and conditions.
 */
/** @file app.js
 *	Purpose:  contains all of the javascript for the index file
 *
 * @author Keith Gudger
 * @copyright  (c) 2015, Keith Gudger, all rights reserved
 * @license    http://opensource.org/licenses/BSD-2-Clause
 * @version    Release: 1.5.3
 * @package    SaveOurShores
 *
 */
	var Version = "1.5.3";
	var currentLatitude = 0;
	var currentLongitude = 0;
	var options = {			// Intel GPS options
        timeout: 5000,
        maximumAge: 20000,
        enableHighAccuracy: true
	};
	var Places = [];
	var Stuff = {}; // Object / associative array to hold which item gets updated

    var SummaryWords = {    // words for the summary page
        uname: "User Name",
        date:  "Date",
        location: "Location",
        email: "Email",
        hours: "Total Hours",
        adults: "Adults",
        youth:  "Youth",
        area:   "Area Cleaned",
        percent: "Percent Area Cleaned"
    }; // object gets filled when language changes

    var ErrorWords = {    // words for entry errors
        name: "Please enter your name before submitting, thanks.",
        email: "Please enter your email before submitting, thanks.",
        event: "Please select an Event type before submitting, thanks.",
        location: "Please select a location before submitting, thanks."
    }; // object gets filled when language changes

	var cats_done = false;
	var event_done = false;
	var place_done = false;
    var queryString = "";
	var recognition;
	var inList = [];


// The function below is an example of the best way to "start" your app.
// This example is calling the standard Cordova "hide splashscreen" function.
// You can add other code to it or add additional functions that are triggered
// by the same event or other events.

function onAppReady() {
    fillName("name-field");

    queryString = "command=getCats";
    sendfunc(queryString,true);
    queryString = "command=getEvent";
    sendfunc(queryString);
    ready();
}
/*
document.addEventListener('deviceready', function () {
  if (navigator.notification) { // Override default HTML alert with native dialog
      window.alert = function (message) {
          navigator.notification.alert(
              message,    // message
              null,       // callback
              "Notice", // title
              'OK'        // buttonName
          );
      };
  }
}, false);
*/
document.addEventListener("app.Ready", onAppReady, false) ;
/*
if(typeof intel === 'undefined') {
    document.addEventListener( "DOMContentLoaded", ready, false );
} else {
	document.addEventListener("intel.xdk.device.ready",onDeviceReady,false);
}
*/
//Success callback
/**
 *	function to set current latitude and longitude from GPS
 *	@param p is passed from intel library function
 */
var suc = function(p) {
//  console.log("geolocation success", 4);
//Draws the map initially
	currentLatitude = p.coords.latitude;
	currentLongitude = p.coords.longitude;
};
/**
 *	fail function for intel gps routine - does nothing 
 */    
var fail = function() {
 console.log("Geolocation failed. \nPlease enable GPS in Settings.", 1);
};

// document.addEventListener("deviceready", onAppReady, false) ;
// document.addEventListener("onload", onAppReady, false) ;

// The app.Ready event shown above is generated by the init-dev.js file; it
// unifies a variety of common "ready" events. See the init-dev.js file for
// more details. You can use a different event to start your app, instead of
// this event. A few examples are shown in the sample code above. If you are
// using Cordova plugins you need to either use this app.Ready event or the
// standard Crordova deviceready event. Others will either not work or will
// work poorly.

// NOTE: change "dev.LOG" in "init-dev.js" to "true" to enable some console.log
// messages that can help you debug Cordova app initialization issues.

/** 
*	Fires when intel code says device is ready
*/
var onDeviceReady=function(){
	try {
        if (intel.xdk.geolocation !== null) {
            intel.xdk.geolocation.watchPosition(suc, fail, options);
//  console.log("geolocation !== null", 4);
        }
    } catch(e) { 
        alert(e.message);
        console.log("geo watch failed",1);
    }
};

/**
 *	Fires when DOM page loaded
 */
function ready() {
    if (navigator.geolocation) {
		var location_timeout = setTimeout("defaultPosition()", 2000);
		// changed to 2 seconds 
        navigator.geolocation.getCurrentPosition(
			function(pos) { clearTimeout(location_timeout); showPosition(pos); },
			function(err) {
				clearTimeout(location_timeout);
				console.warn('ERROR(' + err.code + '): ' + err.message);
				defaultPosition()
			},
			options
		);
    }
    else {
		console.warn("Geolocation failed. \nPlease enable GPS in Settings.", 1);
		defaultPosition();
	}
	$('#date-field').datepick({dateFormat: 'yyyy-mm-dd',
		onClose: function(dates) { setDate(dates); }
	});
	var currentDate = new Date()
	var day = currentDate.getDate()
	var month = currentDate.getMonth() + 1
	if(day < 10) {
		day = '0' + day;
	} 
	if( month < 10 ) {
		month = '0' + month ;
	} 
	var year = currentDate.getFullYear()
	var ndate = year + '-' + month + '-' + day;
    var out = document.getElementById("datein");
    var dout = document.getElementById("date-field");
    out.value = dout.value = ndate ;

}

	/**
	 *	set date function for date field
	 */
	function setDate(dates) {
        var out = document.getElementById("datein");
		var selected = ''; 
		for (var i = 0; i < dates.length; i++) { 
			selected += ',' + $.datepick.formatDate('yyyy-mm-dd',dates[i]); 
		} 
//		alert('Selected dates are: ' + selected.substring(1)); 
        out.value = selected.substring(1) ;
    }


/** 
 *	sets current latitude and longitude from ready() function
 */
function showPosition(position) {
//	console.log('In showPosition');
    currentLatitude = position.coords.latitude;
	currentLongitude = position.coords.longitude;
    var latid = document.getElementById("latin");
    var lonid = document.getElementById("lonin");
    latid.value = currentLatitude;
    lonid.value = currentLongitude;
    console.log("Lat is " + latid.value + " Lon is " + lonid.value);
    var queryString = "command=getPlace" + "&latin=" + currentLatitude + "&lonin=" + currentLongitude ;
    sendfunc(queryString);
};

/** 
 *	on fail from ready, default position
 */
function defaultPosition() {
//	console.warn('ERROR(' + err.code + '): ' + err.message);
	console.log('In defaultPosition');
//	alert("defaultPosition");
    var queryString = "command=getPlace" + "&latin=" + currentLatitude + "&lonin=" + currentLongitude ;
	sendfunc(queryString);
}


/**
	 *	onclick function for "minus" button
	 */
	function minus_one(elt) {
        var val = document.getElementById(elt);
        if (val.value > 0) val.value--;
    }

	/**
	 *	onclick function for "plus" button
	 */
	function plus_one(elt) {
        var val = document.getElementById(elt);
        val.value++;
    }

/**
	 *	onclick function for "other minus" button
	 */
	function other_minus_one(elt) {
        var val2 = document.getElementById(elt);
        var val = document.getElementById(Stuff[elt]);
        if( val != null) {
			if (val.value > 0) val.value--;
			val2.value = val.value;
//			alert ("value is " + val.value + " value 2 is " + val2.value);
		}
    }

	/**
	 *	onclick function for "other plus" button
	 */
	function other_plus_one(elt) {
        var val = document.getElementById(Stuff[elt]);
        var val2 = document.getElementById(elt);
        if( val != null) {
	        val.value++;
			val2.value = val.value;
//			alert ("value is " + val.value + " value 2 is " + val2.value);
		}
    }

	/**
	 *	oninput function for "other" input
	 */
	function other_change(field_name) {
        var val = document.getElementById(Stuff[field_name]);
        var val2 = document.getElementById(field_name);
        if( val != null) {
	        val.value = val2.value;
//			alert ("value is " + val.value + " value 2 is " + val2.value);
		}
		else {
			val2.value = 0;
		}
    }

	/**
	 *	onblur function for name field
	 */
	function fillName(elt) {
        var val = document.getElementById(elt).value;
        var out = document.getElementById("name-in");
        out.value = val;
		var eml = document.getElementById("emailin").value
//		alert ("User name is " + val + " and Email is " + eml);
		if ( (eml != "") && (val != "") ) {
			checkUname();
		}
    }

	/**
	 *	onchange function for email select
	 */
	function newEmail(elt) {
		var val = document.getElementById(elt).value;
        var out = document.getElementById("emailin");
        out.value = val;
        var queryString = "command=getTally" + "&emailin=" + val ;
        sendfunc(queryString);
		var name = document.getElementById("name-in").value
//		alert ("User name is " + name + " and Email is " + val);
		if ( (name != "") &&  (val != "") ) {
			checkUname();
		}
	}
// newEmail

	/** 
	 * This function pings the server to make sure that the user name is unique
	 * If it's not, the user name gets updated after call.
	 */
	 function checkUname() {
		var eml = document.getElementById("emailin").value
		var name = document.getElementById("name-in").value
//		alert ("User name is " + name + " and Email is " + eml);
        var queryString = "command=checkUname" + "&namein=" + name + "&emailin=" + eml ;
        sendfunc(queryString);
	}
	
	/**
	 *	onblur function for date field
	 */
	function fillDate(elt) {
        var val = document.getElementById(elt).value;
        var out = document.getElementById("datein");
        out.value = val ;
        console.log("date is " + out.value);
    }
/*
$(document).on('pagebeforeshow', '#dataCard', function(){       
$( "#splashscreen" ).panel( "open"); });
*/
/**
 *	sendData function, called at 'submit'
 */
function sendData() {
    var out = document.getElementById("name-in").value;
    var eml = document.getElementById("emailin").value;
    var place = document.getElementById("place-field").value;
    var event = document.getElementById("event-field").value;
//    alert("Location selection is " + place);
    if ( out == "" ) {
  	if (navigator.notification) { // Override default HTML alert with native dialog
          navigator.notification.alert(
	          ErrorWords['name'],  // message
              null,       // callback
              "Notice", // title
              'OK'        // buttonName
          );
        } else
          alert(ErrorWords['name']);
    } else if ( place == "Please Choose / Por favor seleccione" ) {
        if (navigator.notification) { // Override default HTML alert with native dialog
            navigator.notification.alert(
                                         ErrorWords['location'],  // message
                                         null,       // callback
                                         "Notice", // title
                                         'OK'        // buttonName
                                         );
        } else
		alert(ErrorWords['location']);
    } else if ( event == "0" ) {
        if (navigator.notification) { // Override default HTML alert with native dialog
            navigator.notification.alert(
                                         ErrorWords['event'],  // message
                                         null,       // callback
                                         "Notice", // title
                                         'OK'        // buttonName
                                         );
        } else
		alert(ErrorWords['event']);
		console.log("Event type is " + event);
	} else if ( eml == "" ) {
        if (navigator.notification) { // Override default HTML alert with native dialog
            navigator.notification.alert(
                                         ErrorWords['email'],  // message
                                         null,       // callback
                                         "Notice", // title
                                         'OK'        // buttonName
                                         );
        } else
		alert(ErrorWords['email']);
	} else {
        var val = document.getElementById("event-field").value;
        var out = document.getElementById("eventin");
        out.value = val;
        val = document.getElementById("email-field").value;
        out = document.getElementById("emailin");
        out.value = val;
        val = document.getElementById("hours-field").value;
        out = document.getElementById("hoursin");
        out.value = val;
        val = document.getElementById("adults-field").value;
        out = document.getElementById("adultsin");
        out.value = val;
        val = document.getElementById("youth-field").value;
        out = document.getElementById("youthin");
        out.value = val;
        val = document.getElementById("percent-field").value;
        out = document.getElementById("percentin");
        out.value = val;
/*        val = document.getElementById("recycle").value;
        out = document.getElementById("precyclein");
        out.value = val;
        val = document.getElementById("trash").value;
        out = document.getElementById("ptrashin");
        out.value = val; */
        queryString = $('#trashform').serialize(); // now global variable
        let tqueryString = "command=Calc&" + queryString;
        sendfunc(tqueryString);
//        console.log (queryString);
		$.mobile.changePage("#summary", "fade");
/*
        queryString = "command=send&" + queryString;
        sendfunc(queryString);
//    alert(queryString);
        document.getElementById("trashform").reset()
//		alert("Before Change Page");
//		$(":mobile-pagecontainer").pagecontainer("change", "submitted.html", { transition: "fade" });
//		$( "#submit-page" ).panel().panel( "open" );
//		$.mobile.changePage( $("#submit-page"));
*/    }
}

$(document).on("pagecontainerbeforeshow", function () {
    var activePage = $.mobile.pageContainer.pagecontainer("getActivePage");
    var activePageId = activePage[0].id;
    if (activePageId == "summary") {
//		alert("Switched to Summary");
		var inputs = $("#trashform :input");
		var obj = $.map(inputs, function(n, i)
		{
			var o = {};
			o[n.id] = $(n).val();
			return o;
		});
        tbl = document.getElementById("summaryData");
        myHTML = "<table width=95%>" ;
//		console.log( obj );
        var i = 0;
        let itemlist = true;
		for (var Key in obj) {
			for (var innerKey in obj[Key]) {
				if ( obj[Key][innerKey] != 0 ) {
					if ( innerKey == "name-in" ) {
						myHTML+= "<tr><td>" + SummaryWords['uname'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "datein" ) {
//						console.log( "In datein" );
						myHTML+= "<tr><td>" + SummaryWords['date'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "emailin" ) {
						myHTML+= "<tr><td>" + SummaryWords['email'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "placein" ) {
						myHTML+= "<tr><td>" + SummaryWords['location'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "hoursin" ) {
						myHTML+= "<tr><td>" + SummaryWords['hours'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "adultsin" ) {
						myHTML+= "<tr><td>" + SummaryWords['adults'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "youthin" ) {
						myHTML+= "<tr><td>" + SummaryWords['youth'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "areain" ) {
						myHTML+= "<tr><td>" + SummaryWords['area'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "percentin" ) {
						myHTML+= "<tr><td>" + SummaryWords['percent'] +
							"</td><td class='fright'>" + obj[Key][innerKey] + "%</td></tr>";
					} else if ( !(innerKey.endsWith("in")) ) { // this is a problem waiting to happen
						if (itemlist) {
							myHTML+= "<tr><th>" + SummaryWords['item'] + "</th><th class='fright'>" + SummaryWords['amount'] + "</tr>";
							itemlist = false;
						}
						console.log("i is " + i);
						myHTML+= "<tr><td>" + innerKey.replace(/_/g, ' ') +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					}
					console.log( innerKey.replace(/_/g, " ") + "=" + obj[Key][innerKey] );
				}
			}
			i++ ;
		}
		myHTML+= "</table>";
		tbl.innerHTML = myHTML;
//		console.log(obj);
//		myHTML += "</ul>";
//		document.getElementById('formData').innerHTML+= myHTML;
	}
});

var window_alert = null; // window for alert after submit before return

/**
 *	reallySendData function, called at final 'submit'
 */
function reallySendData() {
    let val = document.getElementById("trash").value;
    queryString += "&ptrashin=" + val;
	val = document.getElementById("recycle").value;
    queryString += "&precyclein=" + val;
    queryString = "command=send&" + queryString;
    let pval = document.getElementById("endMessage");
    pval.innerHTML = "<br>Please wait<br>while we send<br>your data.";
	$.mobile.changePage("#pleasewait", "fade");
    
    sendfunc(queryString);
    document.getElementById("trashform").reset();
    extra_fields_reset(); // resets fields not in trashform
/*    window_alert = window.open('','');
    window_alert.document.write('<h2 style="text-align: center;"><br>Please wait<br>while we send<br>your data.</h2>');
//    window_alert = window.open('images/App-Submit-Wait-Slide.png',"_self")
    window_alert.focus() ;*/
	
    splashclick('https://saveourshores.org/leaderboard/');
}

/**
 *	extra_fields_reset function, called at final 'submit'
 *  resets hours-field, adults-field, youth-field, area-field
 */
function extra_fields_reset() {
    let val = document.getElementById("hours-field")
    val.value = 0;
    val = document.getElementById("adults-field");
    val.value = 0;
    val = document.getElementById("youth-field");
    val.value = 0;
}

/**
 *	"Ajax" function that sends and processes xmlhttp request
 *	@param params is POST request string
 */
function sendfunc(params,test) {
    if (test === undefined) {
        test = false;
    }
    var xmlhttp;
	try {
	   xmlhttp=new XMLHttpRequest();
    } catch(e) {
        xmlhttp = false;
        console.log(e);
    }
	if (xmlhttp) {
        xmlhttp.onreadystatechange=function()
		{
		  console.log("readyState=" + xmlhttp.readyState + " status=" + 
					xmlhttp.status + " message " + xmlhttp.statusText);
		  if (xmlhttp.readyState==4)
		  {  if ( (xmlhttp.status==200) || (xmlhttp.status==0) )
            {
              returnedList = (xmlhttp.responseText);
              if ( returnedList != "Collector Entered" ) {
				  if (returnedList == undefined || 
						returnedList == null || returnedList == "") {
					returnedList = '{}';
				  }
                  returnedList = JSON.parse(returnedList);
                  if (typeof (returnedList["trash"]) !== 'undefined') {
                    var val = document.getElementById("trash")
                    val.value = returnedList["trash"];
                    val = document.getElementById("recycle")
                    val.value = returnedList["recycle"];
                    if (window_alert != null) {
                      window_alert.close();
                    }
                    let pval = document.getElementById("endMessage");
                    pval.innerHTML = "<br>Thank you<br>for keeping<br>our beaches clean!<br>Gracias por mantener<br>nuestras playas limpias";
                  }
                  else if (typeof (returnedList["place"]) !== 'undefined') {
//                      alert("place is " + returnedList["place"] );
                      fillPlace(returnedList);
                      place_done = true;
                      checkAndHide();
				  }
                  else if (typeof (returnedList["Event"]) !== 'undefined') {
//                      alert("Event is " + returnedList["Event"] );
                      fillEvent(returnedList);
                      event_done = true;
                      checkAndHide();
				  }
                  else if (typeof (returnedList["uname"]) !== 'undefined') {
//                    alert("Uname is " + returnedList["uname"] );
					var val = document.getElementById("name-in")
                    val.value = returnedList["uname"];
                    val = document.getElementById("name-field")
                    val.value = returnedList["uname"];

				  }
                  else if (typeof (returnedList["ctrash"]) !== 'undefined') {
                    var val = document.getElementById("trash")
                    val.value = returnedList["ctrash"];
                    val = document.getElementById("recycle")
                    val.value = returnedList["crecycle"];
				  }
                  else if (typeof (returnedList["language"]) !== 'undefined') {
                          fillFormLang(returnedList);
                  }  
                  else if (typeof (returnedList["help"]) !== 'undefined') {
                          fillText(returnedList);
                          checkAndHide();
                  }  
                  else {
//                      alert(returnedList["Top Items"]);
						if (Object.keys(returnedList).length === 0 && 
								returnedList.constructor === Object &&
								test) {
                            if (navigator.notification) { // Override default HTML alert with native dialog
                                navigator.notification.alert(
                                                             "We don't seem to have internet, please turn on Wifi or cellular data",  // message
                                                             null,       // callback
                                                             "Notice", // title
                                                             'OK'        // buttonName
                                                             );
                            } else
							alert("We don't seem to have internet, please turn on Wifi or cellular data");
						} else {

						  if( navigator.splashscreen && navigator.splashscreen.hide ) {   // Cordova API detected
							navigator.splashscreen.hide() ;
						  } // moved to here so splashscreen stays until really ready
                          fillForm(returnedList);
                          cats_done = true;
                          if (test)
                              checkAndHide();
                        }  
                  }
              }
            } else { // in case there is an internet failure
//			  alert("We don't seem to have internet, please turn on Wifi or cellular data");
		  }
		}
	  }
//	xmlhttp.open("GET","http://home.loosescre.ws/~keith/SOS/server.php" + '?' + params, true);
//	xmlhttp.open("GET","http://www.saveourshores.org/server.php" + '?' + params, true);
//	xmlhttp.send(null);
	  xmlhttp.open("POST","https://saveourshores.org/server.php", false);
      xmlhttp.setRequestHeader ("Accept", "text/plain");
	  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xmlhttp.send(encodeURI(params));
    }
}; // sendfunc

/**
 *	Function to hide splash screen if seen before and
 *  all Ajax if completed.
 */
function checkAndHide() {
	var before = "";
	if(typeof(window.localStorage) != 'undefined'){ 
		before = window.localStorage.getItem ("SOSbefore"); 
	} 
//	var before = getCookie("SOSbefore");
//	alert("In checkAndHide before is " + before);
   	if ( cats_done && place_done && event_done && (before != ""))
   	{
		hideSplash();
		var out = document.getElementById("Version");
		out.innerHTML = "Version " + Version;
//    alert("Version is " + Version);
	}
}

/**
 *	Function to fill form with data from database
 *
 * @param rList is object returned from ajax
 */
function fillForm(rList) {
    var myHTML = '<ul data-role="collapsible-set">';
    var option;
//	var newHtml = "<div>" ;
    let i = 0;
    for (var topKey in rList) {
		myHTML+= '<li data-role="collapsible" data-inset="false" data-iconpos="right" class="setwidth"><h2 class="catheader" id="olist_'+ i + '">' + topKey + '</h2>';
		i+= 1;

		for (var innerKey in rList[topKey]) {
			var iVal = rList[topKey][innerKey] ; // db key value

			iValNew = innerKey.replace(/ /g, "_") ;
			inList.push(iValNew);
			myHTML+= '<div class="item_field"><div class="item_name" id="' + iVal + '">'+ innerKey + '</div><div class="fright"><div class="fleft"> <input class="left25" data-role="none" type="number" onclick="this.select()" id="' + iValNew + '" value="0" name="' + iVal + '" ></div><div class="fright item_right"><a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="minus_one(' + "'" + iValNew + "'" + ')"></a> <a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="plus_one(' + "'" + iValNew + "'" + ')"></a></div></div></div>';
		}
		myHTML += "</li>";
	}
	myHTML += "</ul>";
	var fdata = document.getElementById('formData') ;
	if (fdata) 
		fdata.innerHTML+= myHTML;
}
// fillForm

/**
 *	Function to create location list with data from database
 *
 * @param rList is object returned from ajax
 */
function fillPlace(rList) {
    var myHTML = "" ;
    var select = document.getElementById('place-field');
    var place_no = rList['place'];
//    alert("Place in fillPlace is " + place_no);
    if ( place_no == 0 ) { // no place found 
		option = document.createElement('option');
		if ( ( currentLatitude == 0 ) || ( currentLongitude == 0 ) ) {
			option.value = option.text = "Please Choose / Por favor seleccione"
		} else {
			option.value = option.text = "OK"
		}
		select.add( option );
		select.options[select.selectedIndex].value = option.value
	}
	Places = [];
    for (var topKey in rList['places']) {
		option = document.createElement( 'option' );
		option.value = option.text = topKey;
		select.add( option );
		Places.push({
						name:  topKey,
						lat:   rList['places'][topKey].lat,
						lon:   rList['places'][topKey].lon
					});
//		alert("topKey pid is " + rList['places'][topKey].pid + " and value is " + option.value);
		if ( rList['places'][topKey].pid == place_no ) {
			select.value=option.value ;
			var latid = document.getElementById("latin");
			var lonid = document.getElementById("lonin");
			latid.value = rList['places'][topKey].lat ;
			lonid.value = rList['places'][topKey].lon ;
			
		}
/*        for (var innerKey in rList[topKey]) {
            var iVal = rList[topKey][innerKey] ;
            myHTML = '<div class="item_field"> <label for "' + iVal + '"> <input data-role="none" type="number" class="right25" id="' + iVal + '" value="0" name="' + iVal + '" > <a href="#" class="blue_back ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="minus_one(' + "'" + iVal + "'" + ')"></a> <a href="#" class="blue_back ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="plus_one(' + "'" + iVal + "'" + ')"></a>' + innerKey + '</label></div>';
        document.getElementById('formData').innerHTML+= myHTML;*/
//        }
    }
	changeSel(select.value,'place-field');
	var val = document.getElementById("placein")
    val.value = select.value;
 
}
// fillPlace

/**
 *	Function to create Event list with data from database
 *
 * @param rList is object returned from ajax
 */
function fillEvent(rList) {
    var myHTML = "" ;
    var select = document.getElementById('event-field');
    removeOptions(select);
	var option = document.createElement( 'option' );
	option.value = 0;
	option.text = "Please Select/Por favor seleccione";
	select.add( option );
//	select.value = '0';
    for (var topKey in rList['results']) {
//		console.log("Topkey in fillEvent is " + topKey);
//		console.log("Innerkey in fillEvent is " + rList['results'][topKey]);
		option = document.createElement( 'option' );
		option.value = topKey ;
		option.text = rList['results'][topKey];
		select.add( option );
    }
    select.selectedIndex = "0";
}
// fillEvent

/**
 *	Function to create Event list with data from database
 *
 * @param rList is object returned from ajax
 */
function changeOther(field_name) {
    var myHTML = "" ;
    var select = document.getElementById(field_name+"-field");
//    other_change() ;
    Stuff[field_name] = select.options[select.selectedIndex].value;
    var val = document.getElementById(Stuff[field_name]);
    var val2 = document.getElementById(field_name);
    if( val != null) {
	    val2.value = val.value;
//		alert ("value is " + val.value + " value 2 is " + val2.value);
	}
}
// changeOther()

	/**
	 *	onclick function for web addresses
	 */
		function splashclick(url) {
//            alert("Typeof is " + typeof(intel));
            if (typeof (intel) === 'undefined') 
				window.open(url,'_system', 'location=yes');
			else
				intel.xdk.device.launchExternal(url);
		};
/**
 * Function to change from splash page to main page.
 */
function hideSplash() {
	var before = "";
	if(typeof(window.localStorage) != 'undefined'){ 
		before = window.localStorage.getItem ("SOSbefore"); 
	} 
//	alert("In hidesplash before is " + before);
	switch(before) {
		case "" :
//			document.cookie = "SOSbefore=1";
			window.localStorage.setItem("SOSbefore",1);
			document.getElementById('splashimage').src='images/App-Welcome-Screen-Slide-2.png'
			break;
		case "1":
//			document.cookie = "SOSbefore=2";
			window.localStorage.setItem("SOSbefore",2);
			document.getElementById('splashimage').src='images/App-Welcome-Screen-Slide-3.png'
			break;
		case "2":
		default:
			$.mobile.changePage("#dataCard", "fade");
			break;
	}
}
// 37.0067 -121.97
	/**
	 *	onclick function for location select
	 */
	function newPlace(text,id) {
		var select = document.getElementById('place-field');
		var value = select.value;
//		alert ("Selected value is " + value );
		for ( var i = 0; i < Places.length; i++ ) {
			if ( Places[i].name == value ) {
//				alert ("Place found is " + Places[i].name);
				var latid = document.getElementById("latin");
				var lonid = document.getElementById("lonin");
				latid.value = Places[i].lat ;
				lonid.value = Places[i].lon ;
			}
		}
		changeSel(text,id);
		var val = document.getElementById("placein")
		val.value = select.value;
	}
// newPlace

/** function to get a cookie by name
 *  param cname is cookie name to look for
 *  returns cookie value
 */
function getCookie(cname) {
	var name = window.localStorage.getItem(cname);
/*    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";*/
	if ( (typeof name === 'undefined') || (name == null) ) {
		name = "";
	}
	return name;
}

/** function to change the language of the app
 *  param is the language value from the select
 */
function changeLang(chosen) {
  console.log(chosen);
  queryString = "command=getCatLang&lang=" + chosen;
  sendfunc(queryString,true);
  queryString = "command=getText&lang=" + chosen;
  sendfunc(queryString,true);
  queryString = "command=getEvent&lang=" + chosen;
  sendfunc(queryString);
  ready();
  // let's setup all of the select fields in one loop
  const array1 = ['percent-field', 'event-field'];
  for (const id of array1) {
    let sel = document.getElementById(id);
    let index=sel.selectedIndex;
    if (index < 0) index=0;
    changeSel(sel.options[index].text,id)
  }
  if (chosen == "spanish")
    document.getElementById("helpi").text = "Ayuda";
  else
    document.getElementById("helpi").text = "Help";
}

/** function to change the percentage area cleaned and display it
 *  param is the percentage value from the select
 */
function changeSel(chosen,id) {
  console.log(chosen);
  id = id + "-button";
  console.log(id);
  let btn = document.getElementById(id);
  if (btn != null) {
	  let newspan = document.createElement("span");
	  const sp1_content = document.createTextNode(chosen);
	  newspan.appendChild(sp1_content); // created new span with new text
	  let child = btn.childNodes[0];
	  // Replace existing node sp2 with the new span element sp1
	  btn.replaceChild(newspan, child);
  }
}
/**
 *	Function to fill form with data from database
 *  after changing the language
 * @param rList is object returned from ajax
 */
function fillFormLang(rList) {
    var myHTML = '';
    var option;
    let i = 0;
    for (var topKey in rList) {
        let category = document.getElementById("olist_" + i); // h2 element of category
        if (category != null) {
            let subcat = category.getElementsByClassName("ui-btn")[0];
            if (subcat != null) {
                let txtin = subcat.innerHTML ;
                txtin = txtin.split('<span'); // grab up to <span and split
                txtin[0]=topKey + "<span";     // add <span back in
                subcat.innerHTML = txtin.join(''); // join together
            }
    		for (var innerKey in rList[topKey]) {
    			let iVal    = rList[topKey][innerKey] ; // db key value
			    let iValNew = innerKey.replace(/ /g, "_") ; // new inner id value
    	        let val = document.getElementById(iVal); // div element w/ name
    	        if (val != null) {
    	            val.innerHTML = innerKey; // sets new name for item
    	            // below changes input field name and plus and minus onclicks
    	            // so that the summary page displays the new lang name
    	            let valpar = val.parentNode; // val parent, no id
    	            let valdiv = valpar.childNodes[1]; // next node, fright
    	            let val2dv = valdiv.childNodes[0]; // first child, fleft
       	            if (val2dv != null) { // should be input
       	                let vall2i = val2dv.childNodes[0].nextSibling; // left25 input
                        vall2i.id = iValNew; // give a new id
                        let valr2a = valdiv.childNodes[1]; // 2nd child, fright
                        let valr20 = valr2a.childNodes[0]; // should be minus
                        valr20.setAttribute('onclick', 'minus_one(' + "'" + iValNew + "'" + ')');
                        let valr21 = valr2a.childNodes[1]; 
                        let valr21s = valr21.nextSibling;  // should be plus
                        valr21s.setAttribute('onclick', 'plus_one(' + "'" + iValNew + "'" + ')');
                    }
                }
            }
        }
        i+= 1;
    }
}

/**
 *	Function to change the app text with data from database
 *  after changing the language
 * @param rList is object returned from ajax
 */
function fillText(rList) {
    for (var topKey in rList) {
        if ( topKey == "SummaryWords" ) {
            let sWords = rList['SummaryWords'];
            SummaryWords = sWords;
        } else if ( topKey == "ErrorWords" ) {
            let eWords = rList['ErrorWords'];
            ErrorWords = eWords;
        } else {
            let textf = document.getElementById(topKey); // div element of text
            if (textf != null) {
                textf.innerHTML = rList[topKey];
            }
        }
    }
}

/**
 *  Function to remove all elements from a select list
 *  @param selectElement is the elements selected by id
 */
function removeOptions(selectElement) {
   var i, L = selectElement.options.length - 1;
   for(i = L; i >= 0; i--) {
      selectElement.remove(i);
   }
}


var Small = {
    'zero': 0,
    'one': 1,
    'two': 2,
    'three': 3,
    'four': 4,
    'five': 5,
    'six': 6,
    'seven': 7,
    'eight': 8,
    'nine': 9,
    'ten': 10,
    'eleven': 11,
    'twelve': 12,
    'thirteen': 13,
    'fourteen': 14,
    'fifteen': 15,
    'sixteen': 16,
    'seventeen': 17,
    'eighteen': 18,
    'nineteen': 19,
    'twenty': 20,
    'thirty': 30,
    'forty': 40,
    'fifty': 50,
    'sixty': 60,
    'seventy': 70,
    'eighty': 80,
    'ninety': 90
};

function text2num(s) {
    return Small[s];
}

function IsNumeric(val) {
    return !isNaN(val);
}

