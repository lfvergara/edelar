<?php
require_once "modules/clienteusuariodetalle/model.php";
require_once "modules/clienteusuarioregistro/model.php";


class ClienteUsuario {
    
    function __construct(ClienteUsuarioDetalle $clienteusuariodetalle=NULL, ClienteUsuarioRegistro $clienteusuarioregistro=NULL) {
        $this->clienteusuario_id = 0;
        $this->denominacion = '';
        $this->token = '';
        $this->clienteusuariodetalle = $clienteusuariodetalle;
        $this->clienteusuarioregistro = $clienteusuarioregistro;
    }
}
?>