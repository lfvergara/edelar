<?php
require_once "modules/clienteusuario/model.php";
require_once "modules/clienteusuario/view.php";
require_once "modules/sitio/view.php";
require_once 'core/helpers/user.cliente.php';
require_once 'modules/clienteusuarioregistro/model.php';
require_once 'modules/clienteusuariodetalle/model.php';


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
		$correoelectronico = filter_input(INPUT_POST, 'correoelectronico');
		$telefono = filter_input(INPUT_POST, 'telefono');
		$wsdl = "https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl";
		$matrix = "VN2741";
		$user = "ID3_XML";
		$password = "A238F615267903CD125674A450CF1C09";
		$sector = "ID";
		$sucursal = 0;
		$documentNumber = $documento;
		$gender = $sexo;
		$questionary = 10467;

		$array = array("matrix"=>$matrix,
					   "user"=>$user,
					   "password"=>$password,
					   "sector"=>$sector,
					   "sucursal"=>$sucursal,
					   "documentNumber"=>$documentNumber,
					   "gender"=>$gender,
					   "questionary"=>$questionary);

		$client = new SoapClient("https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl");
		$result = $client->__soapCall("obtenerPreguntas", array($array));
		print_r($result->return->requestResult->error);exit;
		$error_id = $result->return->requestResult->error->id;
		$error_txt = $result->return->requestResult->error->descripcion;

		if (is_null($error_id) OR empty($error_id) OR $error_id == '') {
			$sv = new SitioView();
			$sv->p3_signup_cliente($result, $questionary, $correoelectronico, $telefono);
		} else {			
	    	header("Location: " . URL_APP . "/sitio/errorSignUp/{$error_id}");
		}
	}

	function p3_signup_cliente() {
		$wsdl = "https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl";
		$matrix = "VN2741";
		$user = "ID3_XML";
		$password = "A238F615267903CD125674A450CF1C09";
		$sector = "ID";
		$sucursal = 0;
		
		$lote = filter_input(INPUT_POST, 'lote');
		$questionary = filter_input(INPUT_POST, 'cuestionario');
		$preguntas = $_POST['pregunta'];
		$respuestas = array();
		$array = array("matrix"=>$matrix,
					   "user"=>$user,
					   "password"=>$password,
					   "sector"=>$sector,
					   "sucursal"=>$sucursal,
					   "lote"=>$lote,
					   "idCuestionario"=>$questionary);

		foreach ($preguntas as $clave=>$valor) {
			$array_temp = array("id"=>$valor, "name"=>'', "questionId"=>$clave);
			$array["answers"][] = $array_temp;
		}

		$client = new SoapClient("https://online.org.veraz.com.ar/WsIDValidator/services/idvalidator?wsdl");
		$result = $client->__soapCall("enviarRespuestas", array($array));
		
		$error_id = $result->return->requestResult->error->id;
		$error_txt = $result->return->requestResult->error->descripcion;
			//print_r($result->return->requestResult->integrantes->nombre);exit;
		if (is_null($error_id) OR empty($error_id) OR $error_id == '') {
			$correoelectronico = filter_input(INPUT_POST, 'correoelectronico');
			$telefono = filter_input(INPUT_POST, 'telefono');
			$documento = $result->return->requestResult->integrantes->documento;
			$denominacion = $result->return->requestResult->integrantes->nombre;
			$score = $result->return->requestResult->integrantes->score;
			$corte = $result->return->requestResult->integrantes->valor;
			$referencia = $result->return->requestResult->integrantes->referencia;
			

			//FIX ME AGREGAR IF
				$nueva_contrasena = substr(uniqid('', true), -8);
                $token_activacion = substr(uniqid('', true), -8);

                $user = hash(ALGORITMO_USER, $correoelectronico);
                $clave = hash(ALGORITMO_PASS, $nueva_contrasena);
                $hash = hash(ALGORITMO_FINAL, $user . $clave);

                $denominacion_partes = explode(',', $denominacion);
                $cudm = new ClienteUsuarioDetalle();
                $cudm->apellido = $denominacion_partes[0];
		        $cudm->nombre = $denominacion_partes[1];
		        $cudm->documento = $documento;
		        $cudm->telefono = $telefono;
		        $cudm->save();
		        $clienteusuariodetalle_id = $cudm->clienteusuariodetalle_id;

				$curm = new ClienteUsuarioRegistro();
				$curm->fecha_registro = date('Y-m-d');
				$curm->fecha_activacion = '0000-00-00';
				$curm->token_activacion = $token_activacion;
				$curm->proveedor = 'Equifax';
				$curm->uid = $referencia;
				$curm->save();
		        $clienteusuarioregistro_id = $curm->clienteusuarioregistro_id;

		        $cum = new ClienteUsuario();
		        $cum->denominacion = $correoelectronico;
		        $cum->token = $hash;
		        $cum->clienteusuariodetalle = $clienteusuariodetalle_id;
		        $cum->clienteusuarioregistro = $clienteusuarioregistro_id;
		        $cum->save();

	    		header("Location: " . URL_APP . "/sitio/p4_signup_cliente");
			if ($score >= $corte) {
			} else {
	    		header("Location: " . URL_APP . "/sitio/errorSignUpRespuestas");
			}
		} else {			
	    	header("Location: " . URL_APP . "/sitio/errorSignUp/{$error_id}");
		}
	}
}
?>
