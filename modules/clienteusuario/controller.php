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
		print_r($_POST);exit;
		$correoelectronico = strtolower(trim(filter_input(INPUT_POST, 'correoelectronico')));
		$contrasena = filter_input(INPUT_POST, 'contrasena');
		$action = filter_input(INPUT_POST, 'action');
		$recaptcha_response = filter_input(INPUT_POST, 'recaptcha_response'); 
				
		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify'; 
		$recaptcha_secret = '6LfHQs8UAAAAAHEmtWL8JtnUQVhrIFaIh0lePVrg'; 
		$recaptcha = $recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response; 
		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response); 
		$recaptcha = json_decode($recaptcha); 

		if ($recaptcha->score >= 0.7 AND $recaptcha->action == 'process') {
		    $clienteusuario_id = ClientUser::verificar_correoelectronico($correoelectronico);

		    if ($clienteusuario_id > 0) {
		    	$metodo_registro = ClientUser::verificar_metodo_registro($correoelectronico);
		    } else {
                $array_registro = array('correoelectronico'=>$correoelectronico, 'contrasena'=>$contrasena, 'estado'=>'incompleto');
		    	$_SESSION["array_registro"] = $array_registro;
		    	header("Location: " . URL_APP . "/sitio/p2_signup_cliente");
		    }
		} else {
		    //FIXME - Mostrar cartel de error de CAPTCHA
		    header("Location: " . URL_APP . "/sitio/p1_signup_cliente");
		}
	}

	function p2_signup_cliente() {
		$documento = filter_input(INPUT_POST, 'documento');
		
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
