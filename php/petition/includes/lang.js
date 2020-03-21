/** @file lang.js
 *	Purpose:  contains javascript for changing the language
 *
 * @author Keith Gudger
 * @copyright  (c) 2020, Keith Gudger, all rights reserved
 * @license    http://opensource.org/licenses/BSD-2-Clause
 * @version    Release: 1.2.4
 * @package    SaveOurShores
 *
 */

	var langarray= {English:"Spanish",Spanish:"English"};
	var errarray= {English:"Ingrese sus datos a continuación <br>",
		Spanish:"Please Enter Your Data Below<br>"};
	var butarray= {English:"Click for English",Spanish:"Haga clic para español"};
	var headarray= {Spanish:
	"<h3>We the undersigned PETITION our local and regional LEGISLATORS to:</h3><ul>  <li>ACT to stop this ocean pollution and climate change accelerator</li>  <li>PASS LEGISLATION to ban the sale of all single serve beverages in plastic bottles</li></ul>Please Select Where You Live (required)<br>",
	English:
	"<h3>Nosotros los abajo firmantes SOLICITAMOS nuestros LEGISLADORES locales y regionales que:</h3><ul>  <li>ACTUEN para acabar con este contaminador del océano y acelerador del cambio climático</li>  <li>APRUEBEN LEGISLACIÓN que prohibe la venta de bebidas en botellas de plástico de uso individual</li></ul>Seleccione dónde vive (requerido)<br>"};
	var namearray= {English:"Nombre (requerido)",Spanish:"Your Name (required)"};
	var emailarray= {English:"Tu correo electrónico (requerido)",Spanish:"Your Email (required)"};
	var streetarray= {English:"Dirección (requerido)",Spanish:"Your Street Address (required)"};
	var cityarray= {English:"Ciudad (requerido)",Spanish:"Your City (required)"};
	var ziparray= {English:"Código postal Zip (requerido)",Spanish:"Your Zip Code (required)"};
	var agreearray={English:"Marque aquí si está de acuerdo con esta petición (requerido)", 
	Spanish:"Check here if you agree with this petition (required)"};
	var rselarray= {English:{"0":"Por favor seleccione",
								"1":"Estoy registrado para votar en el Condado de Santa Cruz.",
								"2":"Estoy registrado para votar en la Ciudad de Santa Cruz.",
								"3":"Estoy registrado para votar en Ciudad de de Capitola.",
								"4":"Estoy registrado para votar en Ciudad de de Scotts Valley.",
								"5":"Estoy registrado para votar en Ciudad de de Watsonville."},
					Spanish:{"0":"Please Select",
								"1":"I am a registered voter in the County of Santa Cruz.",
								"2":"I am a registered voter in the City of Santa Cruz.",
								"3":"I am a registered voter in the City of Capitola.",
								"4":"I am a registered voter in the City of Scotts Valley.",
								"5":"I am a registered voter in the City of Watsonville."}};
	var subarray= {English:"Enviar",Spanish:"Submit"};

	function language() {

		var lang= document.getElementById('blang').value;
//		alert("Language Clicked is " + lang);
		document.getElementById('blang').value = langarray[lang];
		document.getElementById('langbut').value = butarray[lang];
		document.getElementById('banner').innerHTML = headarray[lang];
		document.getElementById('lname').innerHTML = namearray[lang];
		document.getElementById('lemail').innerHTML = emailarray[lang];
		document.getElementById('lstreet').innerHTML = streetarray[lang];
		document.getElementById('lcity').innerHTML = cityarray[lang];
		document.getElementById('lzip').innerHTML = ziparray[lang];
		document.getElementById('agree').innerHTML = agreearray[lang];
		var rsel = document.getElementById('rselect').options;
		for( var key in rsel ) {
			if ( !isNaN(key) ) {
				rsel[key].innerHTML = rselarray[lang][key];
			}
		}
		document.getElementById('bsub').value = subarray[lang];
		document.getElementById('berror').innerHTML = errarray[lang];
	}

/**
 *	sendData function, called at 'submit'
 */
function sendData() {
	var errtxt = "We Found Some Errors<br>";
	var lang   = document.getElementById('blang').value;
	var reside = document.getElementById('rselect').value;
	var rsel = "";
	if ( reside == "-1") {
		reside = 0;
	} else {
		rsel = rselarray[langarray[lang]][reside];
	}
	var name   = document.getElementById('bname').value ;
	var email  = document.getElementById('bemail').value ;
	var street = document.getElementById('bstreet').value ;
	var city   = document.getElementById('bcity').value;
	var zip    = document.getElementById('bzip').value ;
	var agree  = document.getElementById('bcheck').checked;
	var values = {};
	values.name = name;
	values.email = email;
	values.street = street;
	values.city = city;
	values.zip = zip;
	values.agree = agree;
	values.language = lang;
	values.locale = rsel;
	let jvals = JSON.stringify(values);
//	alert(jvals);
	queryString = "data=" + jvals;
	sendfunc(queryString);
}

/**
 *	"Ajax" function that sends and processes xmlhttp request
 *	@param params is POST request string
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
		  if (xmlhttp.readyState==4)
		  {  if ( (xmlhttp.status==200) || (xmlhttp.status==0) )
            {
				returnedList = (xmlhttp.responseText);
				// alert(returnedList);
				var berr = document.getElementById('berror') ; 
				berr.innerHTML = returnedList;
            } else { // in case there is an internet failure
//			  alert("We don't seem to have internet, please turn on Wifi or cellular data");
		    }
		  }
		}
	  xmlhttp.open("POST","https://saveourshores.org/wp-content/plugins/bpetition/includes/bserver.php", true);
      xmlhttp.setRequestHeader ("Accept", "text/plain");
	  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xmlhttp.send(params);
    }
}; // sendfunc

