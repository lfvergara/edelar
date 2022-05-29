<?php


class ClienteUsuarioRegistro extends StandardObject {
    
    function __construct() {
        $this->clienteusuarioregistro_id = 0;
        $this->fecha_registro = '';
        $this->fecha_activacion = '';
        $this->token_activacion = '';
        $this->proveedor = '';
        $this->uid = '';
    }
}
?>