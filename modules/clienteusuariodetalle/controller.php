<?php
require_once "modules/clienteusuariodetalle/model.php";
require_once "modules/clienteusuariodetalle/view.php";


class ClienteUsuarioDetalleController {

	function __construct() {
		$this->model = new ClienteUsuarioDetalle();
		$this->view = new ClienteUsuarioDetalleView();
	}
}
?>
