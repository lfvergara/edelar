<?php
require_once "Errores.php";


Class Util {

    public static function comprobarRespuestaDatos($respuesta){
        $haydatos = true;
        $flag_error = 1;
        $r = trim($respuesta);
        $errores = new Errores();
        switch ($r) {
			case $errores->COD_CONEXION:
                $haydatos = false;
                $flag_error = 1;
                break;
            case $errores->COD_NOAUTH_JWT:
                $haydatos = false;
                $flag_error = 2;
                break;
            case $errores->COD_NO_JWT:
                $haydatos = false;
                $flag_error = 3;
                break;
            case $errores->COD_CONEXIONBASE:
                $haydatos = false;
                $flag_error = 4;
                break;
            case $errores->COD_NOAUTH_USER:
                $haydatos = false;
                $flag_error = 5;
                break;
            case $errores->COD_QUERYBASE:
                $haydatos = false;
                $flag_error = 6;
                break;
            case $errores->COD_NO_DATA:
                $haydatos = false;
                $flag_error = 7;
                break;
            case $errores->COD_NIS_INVALIDO:
                $haydatos = false;
                $flag_error = 8;
                break;
            case $errores->COD_DNI_INVALIDO:
                $haydatos = false;
                $flag_error = 9;
                break;
			case $errores->COD_IDCLIENTE_INVALIDO:
                $haydatos = false;
                $flag_error = 10;
    		default:
				if(Util::contiene($errores->COD_CONEXION_SERVLET,$r)){
					$haydatos = false;
					$flag_error = 11;	
				}
    			break;
    	}

        $array_haydatos = array('haydatos'=>$haydatos, 'flag_error'=>$flag_error);
    	return $array_haydatos;
    }

    public static function respuestaJSON($datos){
        $json = "";
        $json = json_encode($datos, JSON_UNESCAPED_UNICODE);
        return $json;
    }
	
	public static function contiene($buscar, $texto){
		return strpos($texto, $buscar) !== false;
	}
}
?>