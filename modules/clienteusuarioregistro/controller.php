<?php
require_once "modules/clienteusuarioregistro/model.php";
require_once "modules/clienteusuarioregistro/view.php";


class ClienteUsuarioRegistroController {

	function __construct() {
		$this->model = new ClienteUsuarioRegistro();
		$this->view = new ClienteUsuarioRegistroView();
	}
}
?>
