<?php
require_once 'modules/clienteusuario/model.php';


class ClientUser {
	static function verificar_correoelectronico($correoelectronico) {
	    $sql = "SELECT cu.clienteusuario_id AS CUID 
				FROM clienteusuario cu 
				WHERE cu.denominacion = ?";
	    $datos = array($correoelectronico);
        $result = execute_query($sql, $datos);
        $clienteusuario_id = (is_array($result) AND !empty($result)) ? $result[0]['CUID'] : 0;
		return $clienteusuario_id;
	}

	static function get_flag_activacion($clienteusuario_id) {
		$cum = new ClienteUsuario();
		$cum->clienteusuario_id = $clienteusuario_id;
		$cum->get();
		print_r($cum);exit;
		$flag_activacion = $cum->clienteusuarioregistro->token_activacion;
		if ($token_activacion == 0) {
			return 1;
		} else {
			return 0;
		}

	    
        return $result[0]['validacion'];
	}


	/*




	static function verificar_metodo_registro($clienteusuario_id) {
	    $sql = "SELECT cumr.proveedor AS PROVEEDOR 
				FROM clienteusuariometodoregistro cumr 
				WHERE cumr.clienteusuario_id = ?";
	    $datos = array($clienteusuario_id);
        $result = execute_query($sql, $datos);
        $proveedor = (is_array($result) AND !empty($result)) ? $result[0]['PROVEEDOR'] : 'DEBE REGISTRARSE';
		return $proveedor;
	}

	static function get_flag_usuario($usuario) {
	    $sql = "SELECT 
	    			clienteusuario_id
	    		FROM 
	    			clienteusuario 
	    		WHERE 
	    			denominacion = ?";
	    $datos = array($usuario);
        $result = execute_query($sql, $datos);
        if (isset($result[0]['clienteusuario_id']) AND !empty($result) AND $result != 0) {
			return $result[0]['clienteusuario_id'];
        } else {
			return 0;
        }
	}

	


	static function get_clienteusuariodetalle_id($hash) {
	    $sql = "SELECT 
	    			clienteusuariodetalle_id
	    		FROM 
	    			clienteusuariodetalle 
	    		WHERE 
	    			token = ?";
	    $datos = array($hash);
        $result = execute_query($sql, $datos);
        if (isset($result[0]['clienteusuariodetalle_id']) AND !empty($result) AND $result != 0) {
			return $result[0]['clienteusuariodetalle_id'];
        } else {
			return 0;
        }
	}

	static function get_clienteusuario_id($clienteusuariodetalle_id) {
	    $sql = "SELECT 
	    			clienteusuario_id
	    		FROM 
	    			clienteusuario 
	    		WHERE 
	    			clienteusuariodetalle = ?";
	    $datos = array($clienteusuariodetalle_id);
        $result = execute_query($sql, $datos);
        if (isset($result[0]['clienteusuario_id']) AND !empty($result) AND $result != 0) {
			return $result[0]['clienteusuario_id'];
        } else {
			return 0;
        }
	}
	*/
}

function ClientUser() {return new ClientUser();}
?>