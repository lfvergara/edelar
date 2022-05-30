<?php
require_once 'core/helpers/user.cliente.php';
require_once 'modules/clienteusuario/model.php';
require_once 'modules/clienteusuariodetalle/model.php';


class SessionClienteBaseHandler {
    function checkin() {
        //$usuario = filter_input(INPUT_POST, 'username');
        $usuario = 'hu.ce.ro@gmail.com';
        //$password = filter_input(INPUT_POST, 'password');
        $password = 'AlfilerL/9';
        $usuario = strtolower(trim($usuario));
        $user = hash(ALGORITMO_USER, $usuario);
        $clave = hash(ALGORITMO_PASS, $password);
        $hash = hash(ALGORITMO_FINAL, $user . $clave);
        print_r($hash);exit;
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
                        "clienteusuario-correoelectronico"=>$cum->clienteusuariodetalle->correoelectronico,
                        "clienteusuario-telefono"=>$cum->clienteusuariodetalle->telefono);
                    $_SESSION["data-login"] = $data_login;
                    $_SESSION['login'] = true;
                    header("Location: " . URL_APP . "/sitio/ofivirtual");
                
                }
            } 
        } 
        /*
        else {
            #ERROR DE ACTIVACION
            $_SESSION['login'] = false;
            header("Location: " . URL_APP . "/sitio/home/erActivacion");
        }
        */
    }

    function check_session() {
        if($_SESSION['login'] !== true) {
            $this->checkout();
        }
    }

    function check_admin_level() {
        $level = $_SESSION["data-login"]["usuario-nivel"];
        if ($level != 3) {
            $this->checkout();
        }
    }

    function check_level() {
        $level = $_SESSION["data-login"]["usuario-nivel"];
        if ($level > 1 ) {
            $_SESSION['login'] = true;
        } else {
            $_SESSION['login'] = false;
            exit(header('Location: ' . LOGIN_URI));
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

    function verifica_encuesta_activa($obj_cliente) {
        $select_encuesta = "e.encuesta_id AS ID";
        $from_encuesta = "encuesta e";
        $where_encuesta = "e.activa = 1 LIMIT 1";
        $encuesta_id = CollectorCondition()->get('Encuesta', $where_encuesta, 4, $from_encuesta, $select_encuesta);

        if (is_array($encuesta_id) AND !empty($encuesta_id)) {
            $encuesta_id = $encuesta_id[0]['ID'];
            $em = new Encuesta();
            $em->encuesta_id = $encuesta_id;
            $em->get();
        } else {
            $em = NULL;
        }

        if (is_null($em)) {
            header("Location: " . URL_APP . "/autogestion/home");
        } else {
            $clienteusuario_id = $_SESSION["data-login"]["clienteusuario-clienteusuario_id"];
            $cum = new ClienteUsuario();
            $cum->clienteusuario_id = $clienteusuario_id;
            $cum->get();
            $clienteusuariodetalle_id = $cum->clienteusuariodetalle->clienteusuariodetalle_id;

            $cudm = new ClienteUsuarioDetalle();
            $cudm->clienteusuariodetalle_id = $clienteusuariodetalle_id;
            $cudm->get();
            $encuesta = $cudm->encuesta;

            if ($encuesta == 1) {
                header("Location: " . URL_APP . "/autogestion/home");
            } else {
                header("Location: " . URL_APP . "/autogestion/encuesta/{$encuesta_id}");
            }
        }
    }
}

function SessionClienteHandler() { return new SessionClienteBaseHandler();}
?>
