<?php
require_once "modules/sitio/view.php";


class SitioController {

	function __construct() {
		$this->view = new SitioView();
	}

    function home() {
        $this->view->home();
	}

	function nosotros() {
		$this->view->nosotros();
	}
	/* INSTITUCIONAL ******************************************/
	function oficinascomerciales() {
		$this->view->oficinascomerciales();
	}

	function rse() {
		$this->view->rse();
	}

	function trabajaedelar() {
		$this->view->trabajaedelar();
	}
	/* INSTITUCIONAL ******************************************/

	/* MENU = CENTRO DE AYUDA ******************************************/
	function leerfactura() {
		$this->view->leerfactura();
	}

	function preguntasfrecuentes() {
		$this->view->preguntasfrecuentes();
	}

	function seguridad() {
		$this->view->seguridad();
	}

	function usoenergia() {
		$this->view->usoenergia();
	}

	function contacto() {
		$this->view->contacto();
	}
	/* MENU = CENTRO DE AYUDA ******************************************/

	function tramites_hogares_comercios() {
		$this->view->tramites_hogares_comercios();
	}


	/* PARA PRUEBA DE FORMULARIOS ******************************************/
	function p1_signup_cliente() {
		$this->view->p1_signup_cliente();
	}

	function p2_signup_cliente() {
		if (isset($_SESSION["array_registro"]) AND !empty($_SESSION["array_registro"])) {
			$this->view->p2_signup_cliente();
		} else {
			$this->p1_signup_cliente();
		}
	}

	function p3_signup_cliente() {
		$this->view->p3_signup_cliente();
	}

	function p4_signup_cliente() {
		//FIXME - Desarrollar script de verificación de datos
		$this->view->p3_signup_cliente();
	}
	/* PARA PRUEBA DE FORMULARIOS ******************************************/
}
?>
