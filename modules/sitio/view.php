<?php


class SitioView extends View {
	function home() {
		$gui = file_get_contents("static/modules/sitio/home.html");
		$template = $this->render_sitio("THEME_HOME", $gui);
		print $template;
	}

	/* INSTITUCIONAL ******************************************/
	function nosotros() {
		$gui = file_get_contents("static/modules/sitio/nosotros.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function oficinascomerciales() {
		$gui = file_get_contents("static/modules/sitio/oficinascomerciales.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function rse($rse_collection) {
		$gui = file_get_contents("static/modules/sitio/rse.html");
		$gui_lst_rse = file_get_contents("static/modules/sitio/lst_rse.html");
		$gui_lst_rse = $this->render_regex_dict('LST_RSE', $gui_lst_rse, $rse_collection);

		$render = str_replace('{lst_rse}', $gui_lst_rse, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function ver_rse($obj_rse, $archivo_collection, $video_collection) {
		$gui = file_get_contents("static/modules/sitio/ver_rse.html");
		$gui_lst_archivorse = file_get_contents("static/modules/sitio/lst_archivorse.html");
		$gui_lst_archivorse = $this->render_regex_dict('LST_ARCHIVORSE', $gui_lst_archivorse, $archivo_collection);
		$gui_lst_videorse = file_get_contents("static/modules/sitio/lst_videorse.html");
		$gui_lst_videorse = $this->render_regex_dict('LST_VIDEORSE', $gui_lst_videorse, $video_collection);

		$obj_rse = $this->set_dict_array('rse', $obj_rse[0]);
		print_r($obj_rse);exit;

	}

	function trabajaedelar() {
		$gui = file_get_contents("static/modules/sitio/trabajaedelar.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}
	/* INSTITUCIONAL ******************************************/

	/* MENU = CENTRO DE AYUDA ******************************************/
	function leerfactura() {
		$gui = file_get_contents("static/modules/sitio/leerfactura.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function preguntasfrecuentes() {
		$gui = file_get_contents("static/modules/sitio/preguntasfrecuentes.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function seguridad() {
		$gui = file_get_contents("static/modules/sitio/seguridad.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function usoenergia() {
		$gui = file_get_contents("static/modules/sitio/usoenergia.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function contacto() {
		$gui = file_get_contents("static/modules/sitio/contacto.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}
	/* MENU = CENTRO DE AYUDA ******************************************/

	function tramites_hogares_comercios() {
		$gui = file_get_contents("static/modules/sitio/hogares_comercios_tramites.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	/* PARA PRUEBA DE FORMULARIOS ******************************************/
	function p1_signup_cliente() {
		$gui = file_get_contents("static/modules/sitio/p1_signup_cliente.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function p2_signup_cliente() {
		$gui = file_get_contents("static/modules/sitio/p2_signup_cliente.html");
		$render = str_replace('{clienteusuario-correoelectronico}', $_SESSION["array_registro"]['correoelectronico'], $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function p3_signup_cliente() {
		$gui = file_get_contents("static/modules/sitio/p3_signup_cliente.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;	
	}
	/* PARA PRUEBA DE FORMULARIOS ******************************************/
}
?>
