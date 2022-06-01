<?php
//LIBS ENCRIPT
require_once "common/libs/sha256encript/SHA256Encript.php";
require_once "common/libs/sha256encript/AESEncrypter.php";


class getDatosV10 {
	function getDeudaFunction($metodo, $valor) {
		$ip= '137.184.46.15';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		//Creacion de array de envio con los datos encriptados
		$aes = new AESEncrypter();
		$data = array();
		$data['firma'] = $firma;
		$data['usuario'] = $aes->EncryptString($usuario, $secretKey);
		$data['clave'] = $aes->EncryptString($clave, $secretKey);
		$data['ente'] = $ente; //Este dato no debe ir encriptado
		$data['metodo'] = $aes->EncryptString($metodo, $secretKey);
		$data['valor'] = $aes->EncryptString($valor, $secretKey);
		
		
		//Implementacion cURL DEUDA
		//$ch = curl_init("https://servicios.edelar.com.ar/api_servicios_desa/wsServicios_deuda.php");
		$ch = curl_init("https://servicios.edelar.com.ar/api_servicios/wsServicios_deuda.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}

	function getClienteFunction($metodo, $valor) {
		$ip= '137.184.46.15';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		//Creacion de array de envio con los datos encriptados
		$aes = new AESEncrypter();
		$data = array();
		$data['firma'] = $firma;
		$data['usuario'] = $aes->EncryptString($usuario, $secretKey);
		$data['clave'] = $aes->EncryptString($clave, $secretKey);
		$data['ente'] = $ente; //Este dato no debe ir encriptado
		$data['metodo'] = $aes->EncryptString($metodo, $secretKey);
		$data['valor'] = $aes->EncryptString($valor, $secretKey);
		
		//Implementacion cURL DEUDA
		$ch = curl_init("https://servicios.edelar.com.ar/api_servicios_desa/wsServicios_cliente.php");
		print_r($ch);exit;
		//$ch = curl_init("https://servicios.edelar.com.ar/api_servicios/wsServicios_cliente.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}

	function getSuministrosFunction($metodo, $valor) {
		$ip= '137.184.46.15';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		//Creacion de array de envio con los datos encriptados
		$aes = new AESEncrypter();
		$data = array();
		$data['firma'] = $firma;
		$data['usuario'] = $aes->EncryptString($usuario, $secretKey);
		$data['clave'] = $aes->EncryptString($clave, $secretKey);
		$data['ente'] = $ente; //Este dato no debe ir encriptado
		$data['metodo'] = $aes->EncryptString($metodo, $secretKey);
		$data['valor'] = $aes->EncryptString($valor, $secretKey);
		
		
		//Implementacion cURL DEUDA
		//$ch = curl_init("https://servicios.edelar.com.ar/api_servicios_desa/wsServicios_deuda.php");
		$ch = curl_init("https://servicios.edelar.com.ar/api_servicios/wsServicios_suministros.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}

	function getSuministroFunction($metodo, $valor) {
		$ip= '137.184.46.15';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		//Creacion de array de envio con los datos encriptados
		$aes = new AESEncrypter();
		$data = array();
		$data['firma'] = $firma;
		$data['usuario'] = $aes->EncryptString($usuario, $secretKey);
		$data['clave'] = $aes->EncryptString($clave, $secretKey);
		$data['ente'] = $ente; //Este dato no debe ir encriptado
		$data['metodo'] = $aes->EncryptString($metodo, $secretKey);
		$data['valor'] = $aes->EncryptString($valor, $secretKey);
		
		
		//Implementacion cURL DEUDA
		//$ch = curl_init("https://servicios.edelar.com.ar/api_servicios_desa/wsServicios_deuda.php");
		$ch = curl_init("https://servicios.edelar.com.ar/api_servicios/wsServicios_suministro.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}
}

//function getDeuda() { return new getDeuda(); }
?>
