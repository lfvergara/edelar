<?php
//LIBS ENCRIPT
require_once "common/libs/sha256encript/SHA256Encript.php";
require_once "common/libs/sha256encript/AESEncrypter.php";


class getDeuda {
	function getDeuda($metodo, $valor) {
		$ip= '144.217.5.1';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		//Definicion de metodo y valor
		//$metodo = 'nis';
		//$valor = '5050150';
		//Creacion de array de envio con los datos encriptados
		$aes = new AESEncrypter();
		$data = array();
		$data['firma'] = $firma;
		print_r($data);exit;
		$data['usuario'] = $aes->EncrypString($usuario, $secretKey);
		$data['clave'] = $aes->EncrypString($clave, $secretKey);
		$data['ente'] = $ente; //Este dato no debe ir encriptado
		$data['metodo'] = $aes->EncrypString($metodo, $secretKey);
		$data['valor'] = $aes->EncrypString($valor, $secretKey);
		//Implementacion cURL
		$ch = curl_init("https://servicios.edelar.com.ar/api_servicios_desa/wsServicios_deuda.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}
}

function getDeuda() { return new getDeuda(); }
?>