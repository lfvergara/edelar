<?php
require_once "modules/clienteusuario/model.php";
require_once "modules/clienteusuario/view.php";
require_once 'core/helpers/user.cliente.php';


class ClienteUsuarioController {

	function __construct() {
		$this->model = new ClienteUsuario();
		$this->view = new ClienteUsuarioView();
	}

	function p1_signup_cliente() {
		require_once 'core/helpers/user.cliente.php';
		$correoelectronico = strtolower(trim(filter_input(INPUT_POST, 'correoelectronico')));
		$dni = filter_input(INPUT_POST, 'documento');
				
	    $clienteusuario_id = ClientUser::verificar_correoelectronico($correoelectronico);
	    if ($clienteusuario_id > 0) {
	    	$metodo_registro = ClientUser::verificar_metodo_registro($correoelectronico);
	    } else {
            $array_registro = array('correoelectronico'=>$correoelectronico, 'dni'=>$dni);
	    	$_SESSION["array_registro"] = $array_registro;
	    	header("Location: " . URL_APP . "/sitio/p2_signup_cliente");
	    }
	}

	function p2_signup_cliente() {
		$documento = filter_input(INPUT_POST, 'documento');
		$sexo = filter_input(INPUT_POST, 'sexo');
		$wsdl = "https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl";
		$matrix = "VN2741";
		$user = "ID3_XML";
		$password = "A238F615267903CD125674A450CF1C09";
		$sector = "ID";
		$sucursal = "0";
		$documentNumber = $documento;
		$gender = "M";
		$questionary = 0;

		$array = array('web:matrix'=>$matrix,
					   'web:user'=>$user,
					   'web:password'=>$password,
					   'web:sector'=>$sector,
					   'web:sucursal'=>$sucursal,
					   'web:documentNumber'=>$documentNumber,
					   'web:gender'=>$gender,
					   'web:questionary'=>$questionary);

		$client = new SoapClient("https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl");
		print_r($client);exit;
		$result = $client->__soapCall("obtenerPreguntas", $array);




		print_r($documento);exit;
		//FIXME - Integrar API Veraz para generar bandera de documento
		$flag_dni = true;

		if ($flag_dni == true) {
			//FIXME - Generar variable de sesión con preguntas y respuestas standar
	    	header("Location: " . URL_APP . "/sitio/p3_signup_cliente");
		} else {
			//FIXME - Mostrar cartel de error de DNI
	    	header("Location: " . URL_APP . "/sitio/p2_signup_cliente");
		}
	}

	function p3_signup_cliente() {
		//FIXME - Integrar API Veraz para generar bandera de documento
		$flag_dni = true;

		if ($flag_dni == true) {
			//FIXME - Generar variable de sesión con preguntas y respuestas standar
	    	header("Location: " . URL_APP . "/sitio/p3_signup_cliente");
		} else {
			//FIXME - Mostrar cartel de error de DNI
	    	header("Location: " . URL_APP . "/sitio/p2_signup_cliente");
		}
	}
}
?>
