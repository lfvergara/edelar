<?php
require_once dirname(__FILE__)."/clases/HttpHelper.php";
require_once dirname(__FILE__)."/clases/Util.php";
require_once dirname(__FILE__)."/deuda.php";
require_once dirname(__FILE__)."/cliente.php";
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!empty($argv[1]) && !empty($argv[2])){
$busqueda = $argv[1];
$valor = $argv[2];
if ($busqueda == "dni"){
	$http = new HttpHelper("WsExternos");
	$http->setUser("externo01");
	$http->setPw("092348230947256");
	
	//$http->setProtocolo("http");
	//$http->setServer("localhost");
	//$http->setPuerto("7080");
	$http->Debug(true);
	$http->regen();
	
	$http->setServlet("GetDeuda");
	$http->agregarPeticion("dni", $valor);
	$http->setMetodo(Peticionable::POST);
	$http->ejecutar();
}
if ($busqueda == "nis"){
	$http = new HttpHelper("WsExternos");
	$http->setUser("externo01");
	$http->setPw("092348230947256");
	
	//$http->setProtocolo("http");
	//$http->setServer("localhost");
	//$http->setPuerto("7080");
	$http->Debug(true);
	$http->regen();
	
	$http->setServlet("GetDeuda");
	$http->agregarPeticion("nis", $valor);
	$http->setMetodo(Peticionable::POST);
	$http->ejecutar();
}

$array_respuesta = Util::comprobarRespuestaDatos($http->respuesta);
$flag_haydatos = $array_respuesta['haydatos'];
$flag_error = $array_respuesta['flag_error'];

if ($flag_haydatos == 1){
	$cliente = null;
	$deudas = array();
	if (!empty($http->respuesta->respuesta[1]->cliente)){
		$deudas_std = $http->respuesta->respuesta[0];
		$cliente_std = $http->respuesta->respuesta[1]->cliente;
		$cliente = wsCliente::CastStd($cliente_std);
		$deudas = array();
		foreach ($deudas_std as $deuda_std) {
			$deuda = wsDeuda::CastStd($deuda_std);
			$deudas[] = $deuda;
		}
	}
	else{
		$cliente_std = $http->respuesta->respuesta[0]->cliente;
		$cliente = wsCliente::CastStd($cliente_std);
	}
	var_dump($deudas);
	var_dump($cliente);
	$datos = array('cliente' => $cliente , 'deudas' => $deudas);
	echo Util::respuestaJSON($datos);
}
else{
	$error_texto="";
	switch ($flag_error) {
			case 1:
				$error_texto = 'Error de conexion con el servidor.';
				break;
			case 2:
				$error_texto = 'Error token invalido.';
				break;
			case 3:
				$error_texto = 'Error no se envio token.';
				break;
			case 4:
				$error_texto = 'Error de conexión a base de datos';
				break;
			case 5:
				$error_texto = 'Error usuario de ws invalido.';
				break;
			case 6:
				$error_texto = 'Error consulta a base (query invalida).';
				break;
			case 7:
				$error_texto = 'No hay datos.';
				break;
			case 8:
				$error_texto = 'Por favor ingrese un NIS válido.';
				break;
			case 9:
				$error_texto = 'Por favor ingrese un DOCUMENTO válido.';
				break;
			case 10:
				$error_texto = 'Por favor ingrese un ID de Cliente válido.';
				break;
			case 11:
				$error_texto = 'Error de conexion con el servidor.';
				break;
			default:
				$error_texto = 'Error desconocido';
				break;
		}
	echo $error_texto;
}

}

?>