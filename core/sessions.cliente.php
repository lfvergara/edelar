<?php
//require_once 'core/helpers/user.cliente.php';
//require_once 'modules/cliente/model.php';
//require_once 'modules/clienteusuario/model.php';
//require_once 'modules/clienteusuariodetalle/model.php';


class SessionClienteBaseHandler {
    function checkin() {
        $usuario = filter_input(INPUT_POST, 'usuario');
        $password = filter_input(INPUT_POST, 'contrasena');
		$usuario = strtolower(trim($usuario));
        $user = hash(ALGORITMO_USER, $usuario);
        $clave = hash(ALGORITMO_PASS, $password);
        $hash = hash(ALGORITMO_FINAL, $user . $clave);

        $flag_usuario = ClientUser::get_flag_usuario($usuario);

        if ($flag_usuario == 0) {
            #ERROR NO REGISTRADO
            $_SESSION['login'] = false;
            header("Location: " . URL_APP . "/sitio/home/erUsuario");
        } else {
            $clienteusuariodetalle_id = ClientUser::verify_correoelectronico($usuario);

            if ($clienteusuariodetalle_id == 0) {
                #ERROR INCONSISTENCIA CORREOS
                header("Location: " . URL_APP . "/sitio/home/erCorreo");
            } else {
                $flag_activacion = ClientUser::get_flag_activacion($clienteusuariodetalle_id);

                if ($flag_activacion == 0) {
                    #ENVÍA CORREO DE ACTIVACION
                    require_once 'core/helpers/emailHelper.php';

                    $nueva_contrasena = substr(uniqid('', true), -8);
                    $clave_activacion = substr(uniqid('', true), -8);

                    $user = hash(ALGORITMO_USER, $usuario);
                    $clave = hash(ALGORITMO_PASS, $nueva_contrasena);
                    $hash = hash(ALGORITMO_FINAL, $user . $clave);
                    $cudm = new ClienteUsuarioDetalle();
                    $cudm->clienteusuariodetalle_id = $clienteusuariodetalle_id;
                    $cudm->get();
                    $cudm->token = $hash;
                    $cudm->validacion = $clave_activacion;
                    $cudm->fecha_inscripcion = date('Y-m-d');
                    $cudm->save();

                    $emailHelper = new EmailHelper();
                    $emailHelper->envia_activacion($usuario, $nueva_contrasena, $clave_activacion);
                    header("Location: " . URL_APP . "/sitio/home/acUsuario");
                } else {
                    if ($flag_activacion == 1) {
                        $clienteusuariodetalle_id = ClientUser::get_clienteusuariodetalle_id($hash);
                        $clienteusuario_id = ClientUser::get_clienteusuario_id($clienteusuariodetalle_id);

                        if ($clienteusuario_id == 0) {
                            #ERROR DE USUARIO/CONTRASEÑA
                            $_SESSION['login'] = false;
                            header("Location: " . URL_APP . "/sitio/home/erCredencial");
                        } else {

                            $cum = new ClienteUsuario();
                            $cum->clienteusuario_id = $clienteusuario_id;
                            $cum->get();

                            $cm = new Cliente();
                            $cm->cliente_id = $cum->clienteusuariodetalle->cliente_id;
                            $cm->get();

                            $data_login = array(
                                "cliente-cliente_id"=>$cm->cliente_id,
                                "cliente-apellido"=>$cm->apellido,
                                "cliente-nombre"=>$cm->nombre,
                                "clienteusuario-clienteusuario_id"=>$cum->clienteusuario_id,
                                "clienteusuario-denominacion"=>$cum->denominacion);
                            $_SESSION["data-login"] = $data_login;
                            $_SESSION['login'] = true;

                            $this->verifica_encuesta_activa($cm);
                            // header("Location: " . URL_APP . "/autogestion/home");
                        }
                    } else {
                        #ERROR DE ACTIVACION
                        $_SESSION['login'] = false;
                        header("Location: " . URL_APP . "/sitio/home/erActivacion");
                    }


                }

            }
        }
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
