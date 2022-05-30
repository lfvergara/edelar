<?php
require_once 'core/helpers/user.cliente.php';
require_once 'modules/clienteusuario/model.php';
require_once 'modules/clienteusuariodetalle/model.php';


class SessionClienteBaseHandler {
    function checkin() {
        $usuario = filter_input(INPUT_POST, 'username');
        //$usuario = 'hu.ce.ro@gmail.com';
        $password = filter_input(INPUT_POST, 'password');
        //$password = 'AlfilerL/9';
        $usuario = strtolower(trim($usuario));
        $user = hash(ALGORITMO_USER, $usuario);
        $clave = hash(ALGORITMO_PASS, $password);
        $hash = hash(ALGORITMO_FINAL, $user . $clave);
        //print_r($hash);exit;
        $clienteusuario_id = ClientUser::verificar_correoelectronico($usuario);
        if ($clienteusuario_id == 0) {
            //ERROR USUARIO NO REGISTRADO
            $_SESSION['login'] = false;
            header("Location: " . URL_APP . "/sitio/errorSignUp/3016");
        } else {
            $cum = new ClienteUsuario();
            $cum->clienteusuario_id = $clienteusuario_id;
            $cum->get();
            $flag_activacion = $cum->clienteusuarioregistro->token_activacion;
            $token = $cum->token;
            
            if ($flag_activacion != 0) {
                //ERROR USUARIO NO ACTIVA DESDE CORREO
                $_SESSION['login'] = false;
                header("Location: " . URL_APP . "/sitio/errorSignUp/163009");
            } else {            
                if ($token != $hash) {
                    #ERROR DE USUARIO/CONTRASEÑA
                    $_SESSION['login'] = false;
                    header("Location: " . URL_APP . "/sitio/errorSignUp/16300902");
                } else {
                    $data_login = array(
                        "clienteusuario-cliente_id"=>$cum->clienteusuario_id,
                        "clienteusuario-apellido"=>$cum->clienteusuariodetalle->apellido,
                        "clienteusuario-nombre"=>$cum->clienteusuariodetalle->nombre,
                        "clienteusuario-documento"=>$cum->clienteusuariodetalle->documento,
                        "clienteusuario-correoelectronico"=>$cum->denominacion,
                        "clienteusuario-telefono"=>$cum->clienteusuariodetalle->telefono);
                    $_SESSION["data-login-clienteusuario"] = $data_login;
                    $_SESSION['login'] = true;
                    header("Location: " . URL_APP . "/sitio/ofivirtual");
                
                }
            } 
        }
    }

    function check_session() {
        if($_SESSION['login'] !== true) {
            $this->checkout();
        }
    }

    function checkout() {
        $_SESSION[] = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $_SESSION['login'] = false;
        header("Location:" . URL_APP . "/sitio/home");
    }
}

function SessionClienteHandler() { return new SessionClienteBaseHandler();}
?>
