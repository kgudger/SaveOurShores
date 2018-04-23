/*
 * Please see the included README.md file for license terms and conditions.
 */
/** @file app.js
 *	Purpose:  contains all of the javascript for the index file
 *
 * @author Keith Gudger
 * @copyright  (c) 2015, Keith Gudger, all rights reserved
 * @license    http://opensource.org/licenses/BSD-2-Clause
 * @version    Release: 1.1
 * @package    SaveOurShores
 *
 */
	var Version = "1.2";
	var currentLatitude = 0;
	var currentLongitude = 0;
	var options = {			// Intel GPS options
        timeout: 5000,
        maximumAge: 20000,
        enableHighAccuracy: true
	};
	var Places = [];
	var Stuff = {}; // Object / associative array to hold which item gets updated

	var cats_done = false;
	var event_done = false;
	var place_done = false;
    var queryString = "";


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
/*	  navigator.notification.alert(
	    "Please enter your name before submitting, thanks.",  // message
	    null,         // callback
	    'Alert',            // title
	    'OK'                  // buttonName
	);*/
        alert("Please enter your name before submitting, thanks.");
    } else if ( place == "Please Choose" ) {
		alert("Please select a location before submitting, thanks.");
    } else if ( event == "0" ) {
		alert("Please select an Event type before submitting, thanks.");
		console.log("Event type is " + event);
	} else if ( eml == "" ) {
		alert("Please enter your email before submitting, thanks.");
	} else {
        var val = document.getElementById("event-field").value;
        var out = document.getElementById("eventin");
        out.value = val;
        val = document.getElementById("email-field").value;
        out = document.getElementById("emailin");
        out.value = val;
        queryString = $('#trashform').serialize(); // now global variable
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
		for (var Key in obj) {
			for (var innerKey in obj[Key]) {
				if ( obj[Key][innerKey] != 0 ) {
					if ( innerKey == "name-in" ) {
						myHTML+= "<tr><td>" + "User Name" +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "datein" ) {
//						console.log( "In datein" );
						myHTML+= "<tr><td>" + "Date" +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					} else if ( innerKey == "emailin" ) {
						myHTML+= "<tr><td>" + "Email" +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>" +
							"<tr><th>Item</th><th class='fright'>Amount</tr>";
					} else if ( i > 5 ) {
						console.log("i is " + i);
						myHTML+= "<tr><td>" + innerKey.replace(/-/g, ' ') +
							"</td><td class='fright'>" + obj[Key][innerKey] + "</td></tr>";
					}
					console.log( innerKey.replace(/-/g, " ") + "=" + obj[Key][innerKey] );
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

/**
 *	reallySendData function, called at final 'submit'
 */
function reallySendData() {
    queryString = "command=send&" + queryString;
    sendfunc(queryString);
    document.getElementById("trashform").reset()
    splashclick('http://www.saveourshores.org/leaderboard/');
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
                  else {
//                      alert(returnedList["Top Items"]);
						if (Object.keys(returnedList).length === 0 && 
								returnedList.constructor === Object &&
								test) {
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
	  xmlhttp.open("POST","http://www.saveourshores.org/server.php", true);
      xmlhttp.setRequestHeader ("Accept", "text/plain");
	  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xmlhttp.send(params);

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
    for (var topKey in rList) {
		myHTML+= '<li data-role="collapsible" data-inset="false" data-iconpos="right" class="setwidth"><h2 class="catheader">' + topKey + '</h2>';
//		if ( topKey == "OTHER" ) {
/*			myHTML += '<div class="item_field"> <label for "' + topKey + '"> <input data-role="none" type="number" class="right25" oninput = "other_change('+"'"+topKey+"'"+')" id="' + topKey + '" value="0" name="' + topKey + '" > <a href="#" class="blue_back ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="other_minus_one(' + "'" + topKey + "'" + ')"></a> <a href="#" class="blue_back ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="other_plus_one(' + "'" + topKey + "'" + ')"></a>';
			myHTML += '<select name="'+topKey+'-field" id="'+topKey+'-field" data-inline="true" onChange="changeOther('+"'"+topKey+"'"+')"></select>';
			Stuff[topKey] = "";
//		}
//        $('#formData').append(myHTML);
        document.getElementById('formData').innerHTML+= myHTML;
/*        if ( topKey != "OTHER" ) {*/
		for (var innerKey in rList[topKey]) {
			var iVal = rList[topKey][innerKey] ;
/*			myHTML+= '<li class="item_field"> <label for "' + iVal + '"> <input data-role="none" type="number" class="right25" id="' + iVal + '" value="0" name="' + iVal + '" >' + innerKey + '<a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="minus_one(' + "'" + iVal + "'" + ')"></a> <a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="plus_one(' + "'" + iVal + "'" + ')"></a></label></li>';*/
//			myHTML+= '<div class="item_field"><div class="item_name">'+ innerKey + '</div><div class="fright"><div class="fleft"> <input class="left25" data-role="none" type="number" id="' + iVal + '" value="0" name="' + iVal + '" ></div><div class="fright item_right"><a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="minus_one(' + "'" + iVal + "'" + ')"></a> <a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="plus_one(' + "'" + iVal + "'" + ')"></a></div></div></div>';
			iValNew = innerKey.replace(/ /g, "-") ;
			myHTML+= '<div class="item_field"><div class="item_name">'+ innerKey + '</div><div class="fright"><div class="fleft"> <input class="left25" data-role="none" type="number" onclick="this.select()" id="' + iValNew + '" value="0" name="' + iVal + '" ></div><div class="fright item_right"><a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-minus ui-btn-icon-notext ui-btn-b ui-mini" onclick="minus_one(' + "'" + iValNew + "'" + ')"></a> <a href="#" class="blue_back button_right ui-shadow ui-btn ui-corner-all ui-btn-inline ui-icon-plus ui-btn-icon-notext ui-btn-b ui-mini" onclick="plus_one(' + "'" + iValNew + "'" + ')"></a></div></div></div>';
		}
//				document.getElementById('formData').innerHTML+= myHTML;
    /*} else {
			var fieldname = topKey+'-field';
			var select = document.getElementById(fieldname);
			option = document.createElement( 'option' );
			option.value = 'empty';
			option.text = 'Please Select';
			select.add( option );
			for (var innerKey in rList[topKey]) {
				option = document.createElement( 'option' );
				option.value = rList[topKey][innerKey];
				option.text = innerKey;
				select.add( option );
				newHtml+= '<input id="' + rList[topKey][innerKey] + '" type="hidden" name="' + rList[topKey][innerKey] + '" value="0">';
			}
//		}
    }*/
		myHTML += "</li>";
//		document.getElementById('formData').innerHTML+= newHtml;
	}
	myHTML += "</ul>";
	document.getElementById('formData').innerHTML+= myHTML;
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
			option.value = option.text = "Please Choose"
		} else {
			option.value = option.text = "Automatic"
		}
		select.add( option );
		select.options[select.selectedIndex].value = option.value
	}
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
	var option = document.createElement( 'option' );
	option.value = 0;
	option.text = "Please Select";
	select.add( option );
    for (var topKey in rList['results']) {
//		console.log("Topkey in fillEvent is " + topKey);
//		console.log("Innerkey in fillEvent is " + rList['results'][topKey]);
		option = document.createElement( 'option' );
		option.value = topKey ;
		option.text = rList['results'][topKey];
		select.add( option );
    }
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
				window.open(url,'_system');
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
	function newPlace() {
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
