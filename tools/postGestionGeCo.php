<?php
//LIBS ENCRIPT
require_once "common/libs/sha256encript/SHA256Encript.php";
require_once "common/libs/sha256encript/AESEncrypter.php";


class postGestionGeCo {
	function postGestionFunction($array_datos) {
		print_r($array_datos);exit;
		$ip= '137.184.46.15';
		$usuario = 'PROVEEDORWEB_7894265917';
		$clave = 'TY3tTnOj7QTf9HDgsFg9KOYT7k5F85T9VcrjFtp9';
		$secretKey = 'PROVEEDOR_cwcFscHTl6UD9MWsnqUHE17wxXqfvHLmaDC9PfHl_WEB'; 
		$ente = 'GtO9lF1FsExXcrSgq9Kpw3stFkct5mt_E007';
		
		//Generacion de firma
		$sha256 = new SHA256Encript();
		$firma = $sha256->Generate($ip, $secretKey, $ente, $usuario, $clave);

		$data = array();
		$data ["firma"]= $firma;
		$data ["usuario"]= $aes->EncryptString($usuario, $secretKey);
		$data ["clave"]= $aes->EncryptString($clave, $secretKey);
		$data ["ente"]= $ente; //Este dato no debe ir encriptado
		$data ["metodo"]= $aes->EncryptString('registrar_gestion', $secretKey);
		$data ["valor"]= $aes->EncryptString($array_datos, $secretKey);

		//$ch = curl_init("https://geco.edelar.com.ar/api_geco/wsGeco_gestion_externos.php");
		$ch = curl_init("https://geco.edelar.com.ar/api_geco_desa/wsGeco_gestion_externos.php");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$response = curl_exec($ch);
		return $response;
	}	
}
?>