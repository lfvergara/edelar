<?php
require_once 'modules/oficina/model.php';
require_once 'modules/tramite/model.php';


class TurnoPendiente extends StandardObject {
	
	function __construct(Oficina $oficina=NULL, Tramite $tramite=NULL) {
		$this->turnopendiente_id = 0;
		$this->numero = '';
		$this->documento = 0;
		$this->fecha_hasta = '';
		$this->hora_solicitud = '';
		$this->telefono = 0;
		$this->correoelectronico = '';
		$this->estado = '';
		$this->token_fecha = '';
		$this->token = '';
		$this->turnero_id = 0;
		$this->oficina = $oficina;
		$this->tramite = $tramite;
	}
}
?>