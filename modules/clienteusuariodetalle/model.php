<?php


class ClienteUsuarioDetalle extends StandardObject {
    
    function __construct() {
        $this->clienteusuariodetalle_id = 0;
        $this->apellido = '';
        $this->nombre = '';
        $this->documento = 0;
        $this->telefono = 0;
    }
}
?>