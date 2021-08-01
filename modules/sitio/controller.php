<?php
require_once "modules/sitio/view.php";


//LIBS ENCRIPT
require_once "common/libs/sha256encript/SHA256Encript.php";
require_once "common/libs/sha256encript/AESEncrypter.php";

class SitioController {

	function __construct() {
		$this->view = new SitioView();
	}

    function home() {
        $this->view->home();
	}

	/* MENU = INSTITUCIONAL ************************************************/
	function nosotros() {
		$this->view->nosotros();
	}
	
	function oficinascomerciales() {
		$this->view->oficinascomerciales();
	}

	function rse() {
		$this->view->rse();
	}

	function trabajaedelar() {
		$this->view->trabajaedelar();
	}
	/* MENU = INSTITUCIONAL ************************************************/

	/* MENU = CENTRO DE AYUDA **********************************************/
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
	/* MENU = CENTRO DE AYUDA **********************************************/

	function tramites_hogares_comercios() {
		$this->view->tramites_hogares_comercios();
	}

	/* MENU = DEUDA ********************************************************/
	function ver_deuda() {
		require_once "common/libs/WebService/wsAExterno_get_deuda_desa.php";
		$ws_get_deuda = new wsAgenteExternoGetDeudaHelper();

		$tipo = filter_input(INPUT_POST, 'tipo');
		switch ($tipo) {
			case 1:
				$variable = filter_input(INPUT_POST, 'nis');
				$rst = $ws_get_deuda->suministro($variable);
				break;
			case 2:
				$variable = filter_input(INPUT_POST, 'dni');
				$rst = $ws_get_deuda->documento($variable);
				break;
		}

		$this->view->ver_deuda($variable, $rst, $tipo);
	}
	/* MENU = DEUDA ********************************************************/

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
