<?php
require_once "modules/clienteusuariometodoregistro/model.php";
require_once "modules/clienteusuariometodoregistro/view.php";


class ClienteUsuarioMetodoRegistroController {

	function __construct() {
		$this->model = new ClienteUsuarioMetodoRegistro();
		$this->view = new ClienteUsuarioMetodoRegistroView();
	}
}
?>
