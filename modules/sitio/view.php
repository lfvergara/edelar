<?php


class SitioView extends View {
	function home() {
		$gui_contenido = file_get_contents("static/modules/sitio/home.html");
		$template = $this->render_sitio("THEME_HOME", $gui_contenido);
		print $template;
	}

	/* INSTITUCIONAL ******************************************/
	function nosotros() {
		$gui_contenido = file_get_contents("static/modules/sitio/nosotros.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function oficinascomerciales() {
		$gui_contenido = file_get_contents("static/modules/sitio/oficinascomerciales.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function rse() {
		$gui_contenido = file_get_contents("static/modules/sitio/rse.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function trabajaedelar() {
		$gui_contenido = file_get_contents("static/modules/sitio/trabajaedelar.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}
	/* INSTITUCIONAL ******************************************/

	/* MENU = CENTRO DE AYUDA ******************************************/
	function leerfactura() {
		$gui_contenido = file_get_contents("static/modules/sitio/leerfactura.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function preguntasfrecuentes() {
		$gui_contenido = file_get_contents("static/modules/sitio/preguntasfrecuentes.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function seguridad() {
		$gui_contenido = file_get_contents("static/modules/sitio/seguridad.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function usoenergia() {
		$gui_contenido = file_get_contents("static/modules/sitio/usoenergia.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function contacto() {
		$gui_contenido = file_get_contents("static/modules/sitio/contacto.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}
	/* MENU = CENTRO DE AYUDA ******************************************/

	function tramites_hogares_comercios() {
		$gui_contenido = file_get_contents("static/modules/sitio/hogares_comercios_tramites.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	/* PARA PRUEBA DE FORMULARIOS ******************************************/
	function p1_signup_cliente() {
		$gui_contenido = file_get_contents("static/modules/sitio/p1_signup_cliente.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;
	}

	function p2_signup_cliente() {
		$gui_contenido = file_get_contents("static/modules/sitio/p2_signup_cliente.html");
		$render = str_replace('{clienteusuario-correoelectronico}', $_SESSION["array_registro"]['correoelectronico'], $gui_contenido);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function p3_signup_cliente() {
		$gui_contenido = file_get_contents("static/modules/sitio/p3_signup_cliente.html");
		$template = $this->render_sitio("THEME_SECCION", $gui_contenido);
		print $template;	
	}
	/* PARA PRUEBA DE FORMULARIOS ******************************************/
}
?>
