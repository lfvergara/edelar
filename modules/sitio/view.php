<?php


class SitioView extends View {
	function home($banner_collection, $mantenimientopreventivo_collection) {
		$gui = file_get_contents("static/modules/sitio/home.html");
		$gui_lst_banner = file_get_contents("static/common/lst_banner.html");
		$gui_lst_banner = $this->render_regex_dict('LST_BANNER', $gui_lst_banner, $banner_collection);
		$gui_lst_mantenimientopreventivo = file_get_contents("static/common/lst_mantenimientopreventivo.html");
		$gui_lst_mantenimientopreventivo = $this->render_regex_dict('LST_MANTENIMIENTOPREVENTIVO', $gui_lst_mantenimientopreventivo, $mantenimientopreventivo_collection);

		$render = str_replace('{lst_mantenimientopreventivo}', $gui_lst_mantenimientopreventivo, $gui);
		$render = str_replace('{lst_banner}', $gui_lst_banner, $render);
		$template = $this->render_sitio("THEME_HOME", $render);
		print $template;
	}

	/* INSTITUCIONAL *******************************************************/
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

	function trabajaedelar($areainteres_collection, $provincia_collection, $msj_alert) {
		$gui = file_get_contents("static/modules/sitio/trabajaedelar.html");
		$gui_slt_areainteres = file_get_contents("static/common/slt_areainteres.html");
		$gui_slt_areainteres = $this->render_regex('SLT_AREAINTERES', $gui_slt_areainteres, $areainteres_collection);
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);

		switch ($msj_alert) {
			case 'erFormato':
				$msj = 'Solo se permiten documentos .PDF o .DOC(Word). Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				break;
			case 'erArchivo':
				$msj = 'Disculpe, ha ocurrido un error en la carga del archivo. Por favor intente nuevamente.<br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				break;
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				break;
			case 'okCorreo':
				$msj = 'Su mensaje ha sido enviado a nuestro staff. <br>Muchas gracias por comunicarse con nosotros!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				break;
			default:
				$alert_array = array('{display_commit}'=>'none','{msj_commit}'=>$msj,'{class_commit}'=>'', '{icon_commit}'=>'');
				break;
		}

		$render = $this->render($alert_array, $gui);
		$render = str_replace('{slt_areainteres}', $gui_slt_areainteres, $render);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}
	/* INSTITUCIONAL *******************************************************/

	/* CENTRO DE AYUDA *****************************************************/
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

	function contacto($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/contacto.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				break;
			case 'okCorreo':
				$msj = 'Su mensaje ha sido enviado a nuestro staff. <br>Muchas gracias por comunicarse con nosotros!';
				$alert_array = array('{display_commit}'=>'block', '{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				break;
			default:
				$alert_array = array('{display_commit}'=>'none','{msj_commit}'=>$msj,'{class_commit}'=>'', '{icon_commit}'=>'');
				break;
		}
	
		$render = $this->render($alert_array, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}
	/* CENTRO DE AYUDA *****************************************************/

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

	/* DEUDA ***************************************************************/
	function ver_deuda($array_deuda, $metodo) {
		$gui = file_get_contents("static/modules/sitio/ver_deuda.html");
		$gui_tbl_deuda = file_get_contents("static/common/tbl_deuda.html");
		$deuda_collection = json_decode($array_deuda);
		$deuda_collection = $deuda_collection[0];
		
		if(!empty($deuda_collection) AND is_array($deuda_collection)) {
			foreach ($deuda_collection as $clave=>$valor) $deuda_collection[$clave]->nis = $valor->suministro->id;
		} else {
			$deuda_collection = array();
		}

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
	/* DEUDA ***************************************************************/

	/* MANTENIMIENTOS PREVENTIVOS ******************************************/
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
	/* MANTENIMIENTOS PREVENTIVOS ******************************************/
	
	/* TRAMITES COMERCIALES ************************************************/
	function turnero($unicom_collection, $tramite_collection) {
		$gui = file_get_contents("static/modules/sitio/turnero_online.html");
		$gui_slt_unicom = file_get_contents("static/common/slt_unicom.html");
		$gui_slt_unicom = $this->render_regex('SLT_UNICOM', $gui_slt_unicom, $unicom_collection);
		$gui_slt_tramite = file_get_contents("static/common/slt_tramite.html");
		$gui_slt_tramite = $this->render_regex('SLT_TRAMITE', $gui_slt_tramite, $tramite_collection);

		$render = str_replace('{slt_unicom}', $gui_slt_unicom, $gui);
		$render = str_replace('{slt_tramite}', $gui_slt_tramite, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function tramites_hogares_comercios() {
		$gui = file_get_contents("static/modules/sitio/hogares_comercios_tramites.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function grandes_clientes() {
		$gui = file_get_contents("static/modules/sitio/grandes_clientes.html");
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function adhesion_debito($tarjetacredito_collection, $msj_alert) {
		$gui = file_get_contents("static/modules/sitio/adhesion_debito.html");
		$gui_slt_tarjetacredito = file_get_contents("static/common/slt_tarjetacredito.html");
		$gui_slt_tarjetacredito = $this->render_regex('SLT_TARJETACREDITO', $gui_slt_tarjetacredito, $tarjetacredito_collection);
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$render = str_replace('{slt_tarjetacredito}', $gui_slt_tarjetacredito, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function adhesion_facturadigital($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/adhesion_facturadigital.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function cambio_vencimiento_jubilados($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/cambio_vencimiento_jubilados.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function baja_voluntaria($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/baja_voluntaria.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function nnss_reconexion_propietario($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/nnss_reconexion_propietario.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function nnss_reconexion_inquilino($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/nnss_reconexion_inquilino.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}

	function ver_frm_nuevosuministroreconexion_inquilino($html) {
		$gui = file_get_contents("static/modules/sitio/{$html}.html");
		$render = str_replace('{url_app}', URL_APP, $gui);
		print $render;
	}

	function nnss_reconexion_precario($msj_alert) {
		$gui = file_get_contents("static/modules/sitio/nnss_reconexion_precario.html");
		$gui_msj_alert = file_get_contents("static/common/msj_alert.html");

		switch ($msj_alert) {
			case 'erCaptcha':
				$msj = 'Estimado cliente, ha ocurrido un error con el captcha. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'erTramite':
				$msj = 'Estimado cliente, ha ocurrido un error con la carga de su gestión. Por favor intente nuevamente. <br>Disculpe las molestias ocasionadas!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'danger', '{icon_commit}'=>'error');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			case 'okTramite':
				$msj = 'Estimado cliente, su gestión ha sido ingresada a nuestro sistema. <br>Muchas gracias por confiar en nosotros!';
				$alert_array = array('{msj_commit}'=>$msj, '{class_commit}'=>'success', '{icon_commit}'=>'valid');
				$gui_msj_alert = $this->render($alert_array, $gui_msj_alert);
				$gui = str_replace('{msj_alert}', $gui_msj_alert, $gui);
				break;
			default:
				$gui = str_replace('{msj_alert}', '', $gui);
				break;
		}
		
		$template = $this->render_sitio("THEME_SECCION", $gui);
		print $template;
	}
	/* TRAMITES COMERCIALES ************************************************/

	/* OFICINA VIRTUAL *****************************************************/
	function ofivirtual($cliente, $metodo) {
		$gui = file_get_contents("static/modules/sitio/ofivirtual.html");
		//$gui_tbl_deuda = file_get_contents("static/common/tbl_deuda.html");
		$rst_cliente = json_decode($cliente);
		print_r($rst_cliente);exit;
		$obj_cliente = $rst_cliente[0];
		$obj_cliente = $this->set_dict($obj_cliente);

		$suministro_collection = $rst_cliente[1];
		//print_r($deuda_collection);exit;
		


		/*
		if(!empty($deuda_collection) AND is_array($deuda_collection)) {
			foreach ($deuda_collection as $clave=>$valor) $deuda_collection[$clave]->nis = $valor->suministro->id;
		} else {
			$deuda_collection = array();
		}

		$gui_tbl_deuda = $this->render_regex('TBL_DEUDA', $gui_tbl_deuda, $deuda_collection);		
		$render = str_replace('{tbl_deuda}', $gui_tbl_deuda, $gui);
		$render = str_replace('{fecha_sys}', date('d/m/Y'), $render);
		$render = str_replace('{hora_sys}', date('h:i:s'), $render);
		*/
		$render = $this->render($obj_cliente, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function ofivirtual_suministro($obj_suministro, $rst_deuda, $metodo) {
		$gui = file_get_contents("static/modules/sitio/ofivirtual_suministro.html");
		$gui_tbl_deuda = file_get_contents("static/common/ofivirtual_tbl_deuda_suministro.html");
		$rst_deuda = json_decode($rst_deuda);

		$deuda_collection = (is_array($rst_deuda) AND !empty($rst_deuda)) ? $rst_deuda[0] : array();
		$display_tbl_deuda = (is_array($rst_deuda) AND !empty($rst_deuda)) ? 'block' : 'none';
		$display_alert_deuda = (is_array($rst_deuda) AND !empty($rst_deuda)) ? 'none' : 'block';
		$gui_tbl_deuda = $this->render_regex('TBL_DEUDA', $gui_tbl_deuda, $deuda_collection);		
		
		$render = str_replace('{tbl_deuda}', $gui_tbl_deuda, $gui);
		$render = str_replace('{display_tbl_deuda}', $display_tbl_deuda, $render);
		$render = $this->render($obj_suministro, $render);
		$template = $this->render_sitio("THEME_AUTOGESTION", $render);
		print $template;
	}
	/* OFICINA VIRTUAL *****************************************************/

	/* COMMON **************************************************************/
	function editar_turno($obj_turnopendiente, $unicom_collection, $tramite_collection) {
		$gui = file_get_contents("static/modules/sitio/editar_turno.html");
		$gui_slt_unicom = file_get_contents("static/common/slt_unicom.html");
		$gui_slt_unicom = $this->render_regex('SLT_UNICOM', $gui_slt_unicom, $unicom_collection);
		$gui_slt_tramite = file_get_contents("static/common/slt_tramite.html");
		$gui_slt_tramite = $this->render_regex('SLT_TRAMITE', $gui_slt_tramite, $tramite_collection);

		$obj_turnopendiente = $this->set_dict($obj_turnopendiente);
		$render = $this->render($obj_turnopendiente, $gui);
		$render = str_replace('{slt_unicom}', $gui_slt_unicom, $render);
		$render = str_replace('{slt_tramite}', $gui_slt_tramite, $render);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function turnos_documento($turnopendiente_collection) {
		$gui_slt_turnos = file_get_contents("static/modules/sitio/turnos_documento.html");
		$gui_slt_turnos_documento = file_get_contents("static/common/slt_turnos_documento.html");

 		$cantidad = ($turnopendiente_collection[0]['CANTIDAD'] >= 2) ? '' : '<button type="button" onclick="solicitarTurno();" id="btnEnviar" name="btnEnviar" value="Solicitar Turno" style=" background-color: #191967;"> Solicitar Turno </button>';
		$gui_slt_turnos_documento = $this->render_regex_dict('SLT_TURNOS', $gui_slt_turnos_documento, $turnopendiente_collection);

		$render = str_replace('{slt_turnos}',$gui_slt_turnos_documento, $gui_slt_turnos);
		$render = str_replace('{cantidad}',$cantidad, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}

	function gestion_requisitos($obj_tramite) {
		$gui_slt_requisitos = file_get_contents("static/common/lst_requisitos.html");
		$render = str_replace('{tramite-requisito}', $obj_tramite->requisito, $gui_slt_requisitos);
		print $render;
	}

	function horas_disponibles($resultado) {
		$gui_slt_horas_disponibles = file_get_contents("static/common/slt_horas_disponibles.html");
		$gui_slt_horas_disponibles = $this->render_regex_dict('SLT_HORAS_DISPONIBLES', $gui_slt_horas_disponibles, $resultado);
		print $gui_slt_horas_disponibles;
	}

	function horas_disponibles_edit($resultado, $dia) {
		$gui_slt_horas_disponibles = file_get_contents("static/common/slt_horas_disponibles_edit.html");
		$gui_slt_horas_disponibles = $this->render_regex_dict('SLT_HORAS_DISPONIBLES', $gui_slt_horas_disponibles, $resultado);
		$gui_slt_horas_disponibles = str_replace('{dia}',$dia, $gui_slt_horas_disponibles);
		print $gui_slt_horas_disponibles;
	}

	function dias_disponibles($resultado) {
		$gui_slt_dias_disponibles = file_get_contents("static/common/slt_dias_disponibles.html");

		$array_turnos = array();
		foreach ($resultado as $clave=>$valor) {
			$array_turnos[$clave]['TURNOS'] = $resultado[$clave]['TURNOS'];
			$array_turnos[$clave]['OFICINA'] = $resultado[$clave]['OFICINA'];
 			unset($resultado[$clave]['TURNOS']);
		}

		$gui_slt_dias_disponibles = $this->render_regex_dict('SLT_DIAS_DISPONIBLES', $gui_slt_dias_disponibles, $resultado);
		$render_horario = '';
		foreach ($array_turnos as $c=>$v) {
			$horario_disponibles = '';
			foreach ($v['TURNOS'] as $clave=>$valor) {
				$horario_disponibles .= '<label><input onmousedown="return false;" type="radio" id="hora_turno" name="hora_turno" value="'.$valor.'@'.$v['OFICINA'].'">'.$valor.'</label><br>';
			}

			$horarios_disponibles = '<td>'.$horario_disponibles.'</td>';
			$render_horario .= $horarios_disponibles;
		}

		$render_horario = str_replace('<!--HORARIOS_DISPONIBLES-->', $render_horario, $gui_slt_dias_disponibles);
		print $render_horario;
	}

	function dias_disponibles_edit($resultado, $horario, $oficina, $fecha, $turnopendiente_fecha) {
		$gui_slt_dias_disponibles = file_get_contents("static/common/slt_dias_disponibles_edit.html");

		$array_turnos = array();
		foreach ($resultado as $key => $value) {

			$array_turnos[$key]['TURNOS'] = $resultado[$key]['TURNOS'];
			$array_turnos[$key]['OFICINA'] = $resultado[$key]['OFICINA'];
			unset($resultado[$key]['TURNOS']);
		}

		$gui_slt_dias_disponibles = $this->render_regex_dict('SLT_DIAS_DISPONIBLES', $gui_slt_dias_disponibles, $resultado);

		$render_horario = '';
		foreach ($array_turnos as $key => $value) {
			if ($value['OFICINA'] == $oficina) {
 				$array_search = array_search($horario, $value['TURNOS']);
				if (empty($array_search)) {
					array_push($value['TURNOS'], $horario);
					sort($value['TURNOS']);
				}
			}

			$horario_disponibles = '';
			foreach ($value['TURNOS'] as $clave => $valor) {
				if ($value['OFICINA'] == $oficina and $valor == $horario and $fecha == $turnopendiente_fecha) {
					$horario_disponibles .= '<label><input onmousedown="return false;" type="radio" id="hora_turno" name="hora_turno" value="'.$valor.'@'.$value['OFICINA'].'" checked> '.$valor.'</label><br>';
				}else {
					$horario_disponibles .= '<label><input onmousedown="return false;" type="radio" id="hora_turno" name="hora_turno" value="'.$valor.'@'.$value['OFICINA'].'"> '.$valor.'</label><br>';
				}
 			}

			$horarios_disponibles = '<td>'.$horario_disponibles.'</td>';
			$render_horario .= $horarios_disponibles;
		}
		$render_horario = str_replace('<!--HORARIOS_DISPONIBLES-->', $render_horario, $gui_slt_dias_disponibles);

		print $render_horario;
	}

	function dias_no_disponibles() {
		$gui_slt_no_dias_disponibles = file_get_contents("static/common/slt_dias_no_disponibles.html");
		print $gui_slt_no_dias_disponibles;
	}

	function mensaje_turno($mensaje) {
		$gui = file_get_contents("static/modules/sitio/mensaje_alerta.html");
		$render = str_replace('{mensaje}', $mensaje, $gui);
		$template = $this->render_sitio("THEME_SECCION", $render);
		print $template;
	}

	function imprimir_turno($turnopendiente_collection,$obj_tramite) {
		$gui = file_get_contents("static/common/comprobante_turno.html");

		$fecha = $turnopendiente_collection[0]['FECHA'];
		$fechaEntera = strtotime($fecha);
		$anio = date("Y", $fechaEntera);
		$mes = date("m", $fechaEntera);
		$dia = date("d", $fechaEntera);
		$fecha_turno = $dia.'-'.$mes.'-'.$anio;
		$obj_tramite->requisito = '<ul>' . $obj_tramite->requisito . '</ul>';
		print_r($obj_tramite);exit;
		$render = str_replace('{documento}', $turnopendiente_collection[0]['DOCUMENTO'], $gui);
		$render = str_replace('{numero}', $turnopendiente_collection[0]['NUMERO'], $render);
		$render = str_replace('{fecha}', $fecha_turno, $render);
		$render = str_replace('{hora}', $turnopendiente_collection[0]['HORA'], $render);
		$render = str_replace('{oficina}', $turnopendiente_collection[0]['OFICINA'], $render);
		$render = str_replace('{direccion}', $turnopendiente_collection[0]['DIRECCION'], $render);
		$render = str_replace('{gestion}', $turnopendiente_collection[0]['GESTION'], $render);
		$render = str_replace('{requisitos}', $obj_tramite->requisito, $render);
 		return $render;
	}
	/* COMMON **************************************************************/
}
?>
