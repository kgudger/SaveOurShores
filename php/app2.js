/**
 *	"Ajax" function that sends and processes xmlhttp request
 *	@param params is GET request string
*/
function sendfunc(params) {
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
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
          {
              returnedList = (xmlhttp.responseText);
              if ( returnedList != "Collector Entered" ) {
                  returnedList = JSON.parse(xmlhttp.responseText);
                  if ( (typeof (returnedList["Category"]) !== 'undefined') ||
			(typeof (returnedList["Name"]) !== 'undefined') ||
			(typeof (returnedList["Date"]) !== 'undefined') ||
			(typeof (returnedList["Item"]) !== 'undefined') ) {
                      fillForm(returnedList['results']);
                  }
                  else if (typeof (returnedList["place"]) !== 'undefined') {
//                      alert("place is " + returnedList["place"] );
                      fillPlace(returnedList);
				  }
                  else {
//                      alert(returnedList["Top Items"]);
						if( navigator.splashscreen && navigator.splashscreen.hide ) {   // Cordova API detected
							navigator.splashscreen.hide() ;
						} // moved to here so splashscreen stays until really ready
                      fillForm(returnedList);
                  }
              }
          }
	}
//	xmlhttp.open("GET","http://home.loosescre.ws/~keith/SOS/server.php" + '?' + params, true);
	xmlhttp.open("GET","http://www.saveourshores.org/server.php" + '?' + params, true);
	xmlhttp.send(null);
    }
}; // sendfunc

/**
 *	Function to fill form with data from database
 *
 * @param rList is object returned from ajax
 */
function fillForm(rList) {
//	alert(rList);
	var myHTML = "" ;
	var select = document.getElementById('subsubsort');
	while(select.options.length > 0){                
		select.remove(0);		
	}
	option = document.createElement( 'option' );
	option.value = option.text = "";
	select.add( option );
	for (var topKey in rList) {
		option = document.createElement( 'option' );
		option.value = option.text = rList[topKey];
		select.add( option );
//		alert("topKey pid is " + rList['places'][topKey].pid + " and value is " + option.value);
	}
}
// fillForm

/**
 *	Function to fill 3rd select button
 */
function newSubSort() {

	var select = document.getElementById('subsubsort');
	while(select.options.length > 0){                
		select.remove(0);		
	}
	option = document.createElement('option');
	option.value = option.text = "";
	select.add(option);
	var el = document.getElementById('subsort');
	var strOption = el.options[el.selectedIndex].text;
//	alert ("Selected value is " + strOption );

	if ( strOption == 'By Name' ) {
	    var queryString = "command=getName" ;
	    sendfunc(queryString);
	} else if ( strOption == 'By Date' ) {
	    var queryString = "command=getDate" ;
	    sendfunc(queryString);
	} else if ( strOption == 'By Category' ) {
	    var queryString = "command=getCategory" ;
	    sendfunc(queryString);
	} else if ( strOption == 'By Item' ) {
	    var queryString = "command=getItem" ;
	    sendfunc(queryString);
	} else if ( strOption == 'By Location' ) {
	    var queryString = "command=getPlace" + "&latin=0&lonin=0" ;
	    sendfunc(queryString);
	}
}
// newSubSort
	function newSort() {
		var select = document.getElementById('subsort');
		while(select.options.length > 0){                
			select.remove(0);		
		}
		option = document.createElement('option');
		option.value = option.text = "";
		select.add(option);

		var el = document.getElementById('mainsort');
//		alert ("Selected value is " + value );
		var strOption = el.options[el.selectedIndex].text;
		if ( strOption == 'Name' ) {
			option = document.createElement('option');
			option.value = option.text = "By Date";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Category";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Item";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Location";
			select.add(option); 
		} else if ( strOption == 'Date' ) {
			option = document.createElement('option');
			option.value = option.text = "By Name";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Category";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Item";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Location";
			select.add(option); 
		} else if ( strOption == 'Category' ) {
			option = document.createElement('option');
			option.value = option.text = "By Name";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Date";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Item";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Location";
			select.add(option); 
		} else if ( strOption == 'Item' ) {
			option = document.createElement('option');
			option.value = option.text = "By Name";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Date";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Category";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Location";
			select.add(option); 
		} else if ( strOption == 'Location' ) {
			option = document.createElement('option');
			option.value = option.text = "By Name";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Date";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Category";
			select.add(option);
			option = document.createElement('option');
			option.value = option.text = "By Item";
			select.add(option);
		}
	}
// newPlace

/**
 *	Function to create location list with data from database
 *
 * @param rList is object returned from ajax
 */
function fillPlace(rList) {
	var myHTML = "" ;
	var select = document.getElementById('subsubsort');
	var place_no = rList['place'];
//    alert("Place in fillPlace is " + place_no);

	for (var topKey in rList['places']) {
		option = document.createElement( 'option' );
		option.value = option.text = topKey;
		select.add( option );
//		alert("topKey pid is " + rList['places'][topKey].pid + " and value is " + option.value);
	}
	option = document.createElement( 'option' );
	option.value = option.text = "Other";
	select.add( option );
}
// fillPlace
