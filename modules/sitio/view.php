<?php


class SitioView extends View {
	function home($mantenimientopreventivo_collection) {
		$gui = file_get_contents("static/modules/sitio/home.html");
		$gui_lst_mantenimientopreventivo = file_get_contents("static/common/lst_mantenimientopreventivo.html");
		$gui_lst_mantenimientopreventivo = $this->render_regex_dict('LST_MANTENIMIENTOPREVENTIVO', $gui_lst_mantenimientopreventivo, $mantenimientopreventivo_collection);

		$render = str_replace('{lst_mantenimientopreventivo}', $gui_lst_mantenimientopreventivo, $gui);
		$template = $this->render_sitio("THEME_HOME", $render);
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
		$gui_lst_rse = file_get_contents("static/common/lst_rse.html");
		$gui_lst_rse = $this->render_regex_dict('LST_RSE', $gui_lst_rse, $rse_collection);

		$render = str_replace('{lst_rse}', $gui_lst_rse, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function ver_rse($obj_rse, $archivo_collection, $video_collection) {
		$gui = file_get_contents("static/modules/sitio/ver_rse.html");
		$gui_lst_archivorse = file_get_contents("static/common/lst_archivorse.html");
		$gui_lst_archivorse = $this->render_regex_dict('LST_ARCHIVORSE', $gui_lst_archivorse, $archivo_collection);
		$gui_lst_videorse = file_get_contents("static/common/lst_videorse.html");
		$gui_lst_videorse = $this->render_regex_dict('LST_VIDEORSE', $gui_lst_videorse, $video_collection);

		$obj_archivo = $archivo_collection[0];
		$obj_archivo = $this->set_dict_array('archivo', $archivo_collection[0]);
		
		$obj_rse = $this->set_dict_array('rse', $obj_rse[0]);
		$render = str_replace('{lst_archivorse}', $gui_lst_archivorse, $gui);
		$render = str_replace('{lst_videorse}', $gui_lst_videorse, $render);
		$render = $this->render($obj_rse, $render);
		$render = $this->render($obj_archivo, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function trabajaedelar($areainteres_collection, $provincia_collection, $msj_modal) {
		$gui = file_get_contents("static/modules/sitio/trabajaedelar.html");
		$gui_slt_areainteres = file_get_contents("static/common/slt_areainteres.html");
		$gui_slt_areainteres = $this->render_regex('SLT_AREAINTERES', $gui_slt_areainteres, $areainteres_collection);
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);

		switch ($msj_modal) {
			case 'erFormato':
				$modal_array = array('{modal-display}'=>'show', '{modal-msj_erformato}'=>'block', '{modal-msj_erarchivo}'=>'none',
									 '{modal-msj_ercaptcha}'=>'none','{modal-msj_okcorreo}'=>'none');
				break;
			case 'erArchivo':
				$modal_array = array('{modal-display}'=>'show','{modal-msj_erformato}'=>'none','{modal-msj_erarchivo}'=>'block',
									 '{modal-msj_ercaptcha}'=>'none','{modal-msj_okcorreo}'=>'none');
				break;
			case 'erCaptcha':
				$modal_array = array('{modal-display}'=>'show','{modal-msj_erformato}'=>'none','{modal-msj_erarchivo}'=>'none',
									 '{modal-msj_ercaptcha}'=>'block','{modal-msj_okcorreo}'=>'none');
				break;
			case 'okCorreo':
				$modal_array = array('{modal-display}'=>'show','{modal-msj_erformato}'=>'none','{modal-msj_erarchivo}'=>'none',
									 '{modal-msj_ercaptcha}'=>'none','{modal-msj_okcorreo}'=>'block');
				break;
			default:
				$modal_array = array('{modal-display}'=>'','{modal-msj_erformato}'=>'none','{modal-msj_erarchivo}'=>'none',
									 '{modal-msj_ercaptcha}'=>'none','{modal-msj_okcorreo}'=>'none');
				break;
		}

		$render = $this->render($modal_array, $gui);
		$render = str_replace('{slt_areainteres}', $gui_slt_areainteres, $gui);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
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

	/* WS DEUDA ************************************************************/
	function ver_deuda($array_deuda, $metodo) {
		$gui = file_get_contents("static/modules/sitio/ver_deuda.html");
		$gui_tbl_deuda = file_get_contents("static/common/tbl_deuda.html");
		$deuda_collection = json_decode($array_deuda);
		$deuda_collection = $deuda_collection[0];

		foreach ($deuda_collection as $clave=>$valor) $deuda_collection[$clave]->nis = $valor->suministro->id;
		$gui_tbl_deuda = $this->render_regex('TBL_DEUDA', $gui_tbl_deuda, $deuda_collection);		
		$render = str_replace('{tbl_deuda}', $gui_tbl_deuda, $gui);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = str_replace('{hora_sys}', date('h:i:s'), $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function consultar_factura_ajax($obj_deuda, $factura_id, $suministro) {
		$gui = file_get_contents("static/modules/sitio/consultar_factura_ajax.html");
		$obj_deuda = $this->set_dict($obj_deuda);
		$render = $this->render($obj_deuda, $gui);
		$render = str_replace('{wsfactura-id}', $factura_id, $render);
		$render = str_replace('{wssuministro-id}', $suministro, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}

	function imprimir_factura_ajax($obj_deuda, $factura_id, $suministro) {
		$gui = file_get_contents("static/modules/sitio/imprimir_factura_ajax.html");
		$obj_deuda = $this->set_dict($obj_deuda);
		$render = $this->render($obj_deuda, $gui);
		$render = str_replace('{wsfactura-id}', $factura_id, $render);
		$render = str_replace('{wssuministro-id}', $suministro, $render);
		return $render;
	}
	/* WS DEUDA ************************************************************/

	/* WS MANTENIMIENTOS PREVENTIVOS ***************************************/
	function ver_mantenimiento($mantenimientopreventivo_collection, $coordenadas, $obj_mantenimiento) {
		$gui = file_get_contents("static/modules/sitio/ver_mantenimiento.html");
		$gui_lst_coordenada = file_get_contents("static/common/lst_coordenada.js");
		$gui_lst_mantenimientopreventivo = file_get_contents("static/common/lst_mantenimientopreventivo.html");
		$gui_lst_mantenimientopreventivo = $this->render_regex_dict('LST_MANTENIMIENTOPREVENTIVO', $gui_lst_mantenimientopreventivo, $mantenimientopreventivo_collection);
		
		if(is_array($coordenadas)){
			$array_coordenadas = array();
			foreach ($coordenadas as $clave=>$valor) {
				$array_coordenadas[$clave] = array("mantenimientopreventivo_id"=>$valor[$clave]['mantenimientopreventivo_id'],
												   "etiqueta"=>$valor[$clave]['etiqueta'],
												   "filcolor"=>$valor[$clave]['filcolor'],
												   "strockcolor"=>$valor[$clave]['strockcolor'],
												   "coordenadas"=> array());

				$var_coordenadas = Array();
				foreach ($valor as $c=>$v) $var_coordenadas[] = array("latitud"=>$v['latitud'], "longitud"=>$v['longitud']);
				$array_coordenadas[$clave]['coordenadas'] = $var_coordenadas;
			}

			$render_coordenada = '';
			$cod_lst_coordenada= $this->get_regex('LST_COORDENADA', $gui_lst_coordenada);

			foreach ($array_coordenadas as $clave=>$valor) {
				$cod_opt_coordenada = file_get_contents("static/common/opt_coordenada.html");
				$etiqueta = $valor['etiqueta'];
				$coordenadas = $valor['coordenadas'];
				$i = $clave;
				unset($valor['coordenadas']);
				$gui_lst_coordenada = $this->render($valor, $cod_lst_coordenada);

				$cod_option_coordenada = $this->get_regex('OPT_COORDENADA', $cod_opt_coordenada);
				$render_opcion_coordenada = '';
				foreach($coordenadas as $coordenada) {
					$opt_coordenada = $this->render($coordenada, $cod_option_coordenada);
					$render_opcion_coordenada .= $opt_coordenada;
				}

				$render_opcion_coordenada = str_replace($this->get_regex('OPT_COORDENADA', $cod_opt_coordenada), $render_opcion_coordenada, $cod_opt_coordenada);
				$gui_lst_coordenada = str_replace('{lista_coordenadas}', $render_opcion_coordenada, $gui_lst_coordenada);
				$gui_lst_coordenada = str_replace('{i}', $i, $gui_lst_coordenada);
				$render_coordenada .= $gui_lst_coordenada;
			}

			$render_coordenada = str_replace('<!--LST_COORDENADA-->', '', $render_coordenada);
			$render_coordenada = str_replace('<!--OPT_COORDENADA-->', '', $render_coordenada);
			$gui = str_replace('{latitud}', $latitud, $gui);
			$gui = str_replace('{longitud}', $longitud, $gui);
			$gui = str_replace('{zoom}', $zoom, $gui);
			$gui = str_replace('{lst_coordenada}', $render_coordenada, $gui);
		}

		$obj_mantenimiento = $this->set_dict_array('mantenimientopreventivo', $obj_mantenimiento);
		$render = str_replace('{lst_mantenimientopreventivo}', $gui_lst_mantenimientopreventivo, $gui);
		$render = $this->render($obj_mantenimiento, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;	
	}
	/* WS MANTENIMIENTOS PREVENTIVOS ***************************************/
	
}
?>