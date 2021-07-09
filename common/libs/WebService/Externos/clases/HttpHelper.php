<?php
require_once "PeticionableInterface.php";
require_once "Peticion.php";
require_once "JwtProtocolo.php";
require_once "Util.php";


Class HttpHelper implements Peticionable {

	private $SERVER = "ws.edelar.com.ar";
	private $PROTOCOLO = "https";
	private $PUERTO = "58921";
	private $user_agent = "EdelarWS 1.1";
	private $servicio_nombre = "";
	private $url_conexion;
	private $servlet = "";
	private $metodo = Peticionable::POST;
	public  $respuesta;
	private $respuesta_raw;
	private $cliente_curl;
	private $peticiones;
	private $debug;
	private $user = "";
	private $pass = "";
	private $CA_PATH = "";

	public function __construct($s){
		$this->servicio_nombre = $s;
		$this->url_conexion = $this->PROTOCOLO."://".$this->SERVER.":".$this->PUERTO."/".$this->servicio_nombre."/";
		$this->peticiones = array();
		$this->setCaPath('/srv/websites/www.edelar.com.ar/application/web/app/common/libs/WebService/ca/EdelarCA.pem');
		$debug = false;
	}

	public function regen(){
		$this->url_conexion = $this->PROTOCOLO."://".$this->SERVER.":".$this->PUERTO."/".$this->servicio_nombre."/";
		$this->peticiones = array();
	}

	public function setServer($sv){
		$this->SERVER = $sv;
	}
	
	public function setCaPath($path){
		$this->CA_PATH = $path;
	}
	
	public function setUser($u){
		$this->user = $u;
	}
	
	public function setPw($p){
		$this->pass = $p;
	}
	
	public function Debug($d){
		$this->debug = $d;
	}

	public function setProtocolo($proto){
		$this->PROTOCOLO = $proto;
	}

	public function setPuerto($puerto){
		$this->PUERTO = $puerto;
	}

	public function agregarPeticion($parametro, $valor){
		$peticion = new Peticion();
		$peticion->setParametro($parametro);
		$peticion->setValor($valor);
		$peticiones = array_push($this->peticiones,$peticion);
	}

	public function limpiarPeticiones(){
		$this->peticiones = array();
	}

	public function setServlet($serv){
		$this->servlet = $serv;
	}

	public function setMetodo($m){
		$this->metodo = $m;
	}

	public function ejecutar(){
		$this->cliente_curl = curl_init();
		$jwt = new JwtProtocolo();
		$token = $jwt->autenticar($this->user, $this->pass);
		curl_setopt_array($this->cliente_curl,array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_USERAGENT => $this->user_agent, CURLOPT_TIMEOUT => 5000, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSLVERSION=> 6, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_CAINFO => $this->CA_PATH, CURLOPT_VERBOSE => $this->debug));
		curl_setopt($this->cliente_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"auth-token: $token"));
		switch ($this->metodo) {
			case Peticionable::POST:
				$this->enviarPost();
				break;
			case Peticionable::GET:
				$this->enviarGet();
				break;
			case Peticionable::DELETE:
				$this->enviarPut();
				break;
			case Peticionable::PUT:
				$this->enviarDelete();
				break;
			default:
				break;
		}

	}

	public function ejecutarContoken($token){
		$this->cliente_curl = curl_init();
		curl_setopt_array($this->cliente_curl,array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_USERAGENT => $this->user_agent, CURLOPT_TIMEOUT => 5000, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSLVERSION=> 6, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_CAINFO => $this->CA_PATH, CURLOPT_VERBOSE => $this->debug));
		curl_setopt($this->cliente_curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"auth-token: $token"));
		switch ($this->metodo) {
			case Peticionable::POST:
				$this->enviarPost();
				break;
			case Peticionable::GET:
				$this->enviarGet();
				break;
			case Peticionable::DELETE:
				$this->enviarPut();
				break;
			case Peticionable::PUT:
				$this->enviarDelete();
				break;
			default:
				break;
		}

	}

	public function enviarPost(){
		$p = $this->getPeticionString($this->peticiones);
		$this->respuesta_raw = "";
		$cantidad = count($this->peticiones);
		curl_setopt_array($this->cliente_curl,array(CURLOPT_URL => $this->url_conexion.$this->servlet, CURLOPT_POST => true));
		curl_setopt($this->cliente_curl,CURLOPT_POST, $cantidad);
		curl_setopt($this->cliente_curl,CURLOPT_POSTFIELDS, $p);
		$jwt = new JwtProtocolo();
		$this->respuesta_raw = curl_exec($this->cliente_curl);
		if (curl_error($this->cliente_curl)) {
			$this->respuesta_raw = "ERROR_CON";
		}
		$this->cerrarConexion();
		$datos = null;
		$array_respuesta = Util::comprobarRespuestaDatos($this->respuesta_raw);
		$flag_haydatos = $array_respuesta['haydatos'];
		if ($flag_haydatos){
			$this->respuesta_raw = $jwt->descifar($this->respuesta_raw);
		}
		error_reporting(0);
		if ($this->respuesta_raw->datos) {
			$datos = json_decode($this->respuesta_raw->datos, false, 512, JSON_UNESCAPED_UNICODE);
		} else {
			$this->respuesta = $this->respuesta_raw;
			return true;
		}
		error_reporting(1);
		$this->respuesta = $datos;
	}

	public function enviarGet(){
		$p = $this->getPeticionString($this->peticiones);
		$this->respuesta_raw = "";
		if (!empty($p)){
			curl_setopt($this->cliente_curl,CURLOPT_URL,$this->url_conexion.$this->servlet."?".$p);
		}
		else{
			curl_setopt($this->cliente_curl,CURLOPT_URL,$this->url_conexion.$this->servlet);
		}
		$jwt = new JwtProtocolo();
		$this->respuesta_raw = curl_exec($this->cliente_curl);
		if (curl_error($this->cliente_curl)) {
			$this->respuesta_raw = "ERROR_CON";
		}
		$this->cerrarConexion();
		$datos = null;
		$array_respuesta = Util::comprobarRespuestaDatos($this->respuesta_raw);
		$flag_haydatos = $array_respuesta['haydatos'];
		if ($flag_haydatos){
			$this->respuesta_raw = $jwt->descifar($this->respuesta_raw);
		}
		error_reporting(0);
		if ($this->respuesta_raw->datos) {
			$datos = json_decode($this->respuesta_raw->datos, false, 512, JSON_UNESCAPED_UNICODE);
		} else {
			$this->respuesta = $this->respuesta_raw;
			return true;
		}
		error_reporting(1);
		$this->respuesta = $datos;
	}

	private function getPeticionString($peticiones){
		$p = "";
		$cantidad = count($peticiones);
		$i = 1;
		foreach ($peticiones as $peticion) {
			if ($i<$cantidad){
				$p = $p.$peticion->getParametro()."=".urlencode($peticion->getValor())."&";
			}
			else{
				$p = $p.$peticion->getParametro()."=".urlencode($peticion->getValor());
			}
			$i++;
		}
		return $p;
	}

	public function enviarPut(){

	}

	public function enviarDelete(){

	}

	private function cerrarConexion(){
		curl_close($this->cliente_curl);
	}

}
?>