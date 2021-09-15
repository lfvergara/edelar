<?php


abstract class View {

    function render_login() {
        $plantilla = file_get_contents("static/login_gestor.html");
        $dict = array("{app_nombre}"=>APP_TITTLE,
                      "{url_app}"=>URL_APP,
                      "{url_static}"=>URL_STATIC_GESTOR);
        return $this->render($dict, $plantilla);
    }

    function render_sitio($flag_theme, $contenido) {
        $flag_theme = str_replace('THEME_', "", $flag_theme);
        switch ($flag_theme) {
            case 'HOME':
                # HOME
                $plantilla = file_get_contents(THEME_HOME_SITIO);
                $sidebar_movil = file_get_contents(SIDEBAR_SITIO_DEUDA_TURNO_MOVIL);
                break;
            case 'SECCION':
                # SECCIÓN
                $sidebar = file_get_contents(SIDEBAR_SITIO_LOGIN_AUTOGESTION);
                $sidebar_movil = file_get_contents(SIDEBAR_SITIO_DEUDA_TURNO_MOVIL);
                $plantilla = file_get_contents(THEME_SECCION_SITIO);
                break;           
            default:
                # 404
                $plantilla = file_get_contents(THEME_404);
                break;
        }

        $fecha = $this->descomponer_fecha();
        $dia_semana = $fecha["{fecha_dia_semana}"];
        $dia_numero = $fecha["{fecha_dia}"];
        $mes = $fecha["{fecha_mes}"];
        $anio = $fecha["{fecha_anio}"];
        $dict = array("{fecha_string}"=>"{$dia_semana} {$dia_numero} de {$mes} del {$anio}",
                      "{app_nombre}"=>APP_TITTLE,
                      "{url_static}"=>URL_STATIC,
                      "{app_footer}"=>APP_TITTLE . " - " . date("Y"),
                      "{contenido}"=>$contenido);
        $plantilla = $this->render($dict, $plantilla);
        $plantilla = ($flag_theme == 'SECCION') ? str_replace("{sidebar_login_autogestion}", $sidebar, $plantilla) : $plantilla;
        $plantilla = str_replace("{sidebar_deuda_turno_movil}", $sidebar_movil, $plantilla);
        $plantilla = str_replace("{url_app}", URL_APP, $plantilla);
        $plantilla = str_replace("{url_static}", URL_STATIC, $plantilla);
        return $plantilla;
    }

    
    /*
    function render_autogestion($contenido) {
        $fecha = $this->descomponer_fecha();
        $dia_semana = $fecha["{fecha_dia_semana}"];
        $dia_numero = $fecha["{fecha_dia}"];
        $mes = $fecha["{fecha_mes}"];
        $anio = $fecha["{fecha_anio}"];
        $dict = array("{fecha_string}"=>"{$dia_semana} {$dia_numero} de {$mes} del {$anio}",
                      "{app_nombre}"=>APP_TITTLE,
                      "{url_static}"=>URL_STATIC,
                      "{app_footer}"=>APP_TITTLE . " 2015 - " . date("Y"),
                      "{contenido}"=>$contenido);
        $plantilla = file_get_contents(TEMPLATE_AUTOGESTION);
        $plantilla = $this->render($dict, $plantilla);
        $plantilla = str_replace("{url_app}", URL_APP, $plantilla);
        $plantilla = str_replace("{url_static}", URL_STATIC, $plantilla);
        return $plantilla;
    }

    function render_template($contenido) {
        $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
        $user_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

        $configuracionmenu = $_SESSION["data-login-" . APP_ABREV]["usuario-configuracionmenu"];
        $sidebar = $this->render_menu($configuracionmenu);
        $display_admin = ($user_level == 3 || $user_level == 9) ? "display: block" : "display: none";
        $display_analista = ($user_level == 3 || $user_level == 9) ? "display: none" : "display: block";
        $display_usuario_perfil = ($user_id == 15) ? "none" : "block";
        $contenido = str_replace("{display_admin}", $display_admin, $contenido);
        $contenido = str_replace("{display_analista}", $display_analista, $contenido);

        $dict = array("{app_nombre}"=>APP_TITTLE,
                      "{app_version}"=>APP_VERSION,
                      "{url_static}"=>URL_STATIC_GESTOR,
                      "{sidebar-menu}"=>$sidebar,
                      "{app_footer}"=>APP_TITTLE . " " . date("Y"),
                      "{usuariodetalle-nombre}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-nombre"],
                      "{usuariodetalle-apellido}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-apellido"],
                      "{usuario-denominacion}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"],
                      "{nivel-denominacion}"=>$_SESSION["data-login-" . APP_ABREV]["nivel-denominacion"],
                      "{usuario-perfil-display}"=>$display_usuario_perfil,
                      "{contenido}"=>$contenido);

        $post_dict = array("{url_app}"=>URL_APP, "{url_static}"=>URL_STATIC_GESTOR);
        $plantilla = file_get_contents(TEMPLATE_GESTOR);
        $plantilla = $this->render($dict, $plantilla);
        $plantilla = $this->render($post_dict, $plantilla);
        return $plantilla;
    }

    function render_menu($configuracionmenu_id) {
        $obj_configuracionmenu = HelperMenu::traer_configuracionmenu($configuracionmenu_id);
        $menu_collection = HelperMenu::traer_menu_collection();
        $submenu_collection = $obj_configuracionmenu->submenu_collection;
        $item_collection = $obj_configuracionmenu->item_collection;
        unset($obj_configuracionmenu->item_collection, $obj_configuracionmenu->submenu_collection);


        foreach ($submenu_collection as $clave=>$valor) {
            $submenu_id = $valor->submenu_id;
            $submenu_collection[$clave]->clavelin = "sm_{$submenu_id}";
            $array_temp = array();
            foreach ($item_collection as $c=>$v) {
                $submenu_temp = $v->submenu->submenu_id;
                $item_id = $v->item_id;
                $item_collection[$c]->clavelin = "i_{$item_id}";
                if ($submenu_id == $submenu_temp) $array_temp[] = $v;
            }

            $submenu_collection[$clave]->item_collection = $array_temp;
        }

        $array_temp_menu_id = array();
        foreach ($submenu_collection as $clave=>$valor) {
            if (!in_array($valor->menu->menu_id, $array_temp_menu_id)) $array_temp_menu_id[] = $valor->menu->menu_id;
        }

        $menu_collection_temp = array();
        foreach ($array_temp_menu_id as $menu_id) {
            foreach ($menu_collection as $clave=>$valor) {
                if ($valor->menu_id == $menu_id) {
                    $valor->submenu_collection = array();
                    $menu_id = $valor->menu_id;
                    $menu_collection[$clave]->clavelin = "m_{$menu_id}";
                    $menu_collection_temp[] = $valor;
                }
            }
        }

        foreach ($menu_collection_temp as $clave=>$valor) {
            $menu_id_temp = $valor->menu_id;
            foreach ($submenu_collection as $submenu) {
                if ($menu_id_temp == $submenu->menu->menu_id) $valor->submenu_collection[] = $submenu;
            }
        }

        foreach ($menu_collection_temp as $menu) {
            $submenu_collection = $menu->submenu_collection;
            foreach ($submenu_collection as $clave=>$valor) {
                $item_collection = $valor->item_collection;
                $submenu_collection[$clave]->css_class = (empty($item_collection)) ? "" : "sub-menu";
            }
        }

        $sidebar = file_get_contents("static/sidebar_gestor.html");
        $render_menu = '';
        $cod_btn_menu = $this->get_regex('BTN_MENU', $sidebar);
        foreach ($menu_collection_temp as $dict_menu) {
            $submenu_collection = $dict_menu->submenu_collection;
            unset($dict_menu->submenu_collection);
            $dict_menu = $this->set_dict($dict_menu);
            $btn_menu = $this->render($dict_menu, $cod_btn_menu);

            $cod_btn_submenu = $this->get_regex('BTN_SUBMENU', $cod_btn_menu);
            $render_submenu = '';
            foreach($submenu_collection as $dict) {
                $item_collection = $dict->item_collection;
                unset($dict->item_collection);

                $dict = $this->set_dict($dict);
                $btn_submenu = $this->render($dict, $cod_btn_submenu);
                $cod_btn_item = $this->get_regex('BTN_ITEM', $btn_submenu);

                $render_item = '';
                $item_collection = $this->set_collection_dict($item_collection);
                foreach ($item_collection as $clave=>$valor) {
                    $render_item .= $this->render($valor, $cod_btn_item);
                }

                $btn_submenu = str_replace($cod_btn_item, $render_item, $btn_submenu);
                $render_submenu .= $btn_submenu;
            }

            $btn_menu = str_replace($cod_btn_submenu, $render_submenu, $btn_menu);
            $render_menu .= $btn_menu;
        }

        $sidebar = str_replace($cod_btn_menu, $render_menu, $sidebar);
        return $sidebar;
    }

    function render_breadcrumb($render, $fecha=NULL) {
        $user_level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
        switch ($user_level) {
            case 1:
                $panel_general = "panel";
                $panel_objeto = "panel";
                break;
            case 2:
                $panel_general = "panel";
                $panel_objeto = "panel";
                break;
            case 3:
                $panel_general = "panel";
                $panel_objeto = "panel";
                break;
            case 9 || 10:
                $panel_general = "panel";
                $panel_objeto = "panel";
                break;
        }

        $fecha = (is_null($fecha)) ? date('Y-m-d') : $fecha;
        $class = strtolower(str_replace("View", "", get_class($this)));
        $dict_vista = array(
            "{objeto}"=>$class,
            "{panel_general}"=>$panel_general,
            "{panel_objeto}"=>$panel_objeto);
        $render = $this->render($dict_vista, $render);
        return $render;
    }
    */

    function render($dict, $html) {
        $render = str_replace(array_keys($dict), array_values($dict), $html);
        return $render;
    }

    function get_regex($tag, $html) {
        $pcre_limit = ini_set("pcre.recursion_limit", 10000);
        $regex = "/<!--$tag-->(.|\n){1,}<!--$tag-->/";
        preg_match($regex, $html, $coincidencias);
        ini_set("pcre.recursion_limit", $pcre_limit);
        return $coincidencias[0];
    }

    function render_regex($tag, $base, $coleccion) {
        $render = '';
        $codigo = $this->get_regex($tag, $base);
        $coleccion = $this->set_collection_dict($coleccion);
        foreach($coleccion as $dict) $render .= $this->render($dict, $codigo);
        $render_final = str_replace($codigo, $render, $base);
        return $render_final;
    }

    function render_regex_dict($tag, $base, $coleccion) {
        $render = '';
        $codigo = $this->get_regex($tag, $base);
        foreach($coleccion as $dict) {
            $render .= $this->render($dict, $codigo);
        }
        $render_final = str_replace($codigo, $render, $base);
        return $render_final;
    }

    function set_dict($obj) {
        $new_dict = array();
        foreach($obj as $clave=>$valor) {
            if (is_object($valor)) {
                $new_dict = array_merge($new_dict, $this->set_dict($valor));
            } else {
                $name_object = strtolower(get_class($obj));
                $new_dict["{{$name_object}-{$clave}}"] = $valor;
            }
        }
        return $new_dict;
    }

    function set_dict_array($name_object, $array) {
        $new_dict = array();
        foreach($array as $clave=>$valor) $new_dict["{{$name_object}-{$clave}}"] = $valor;
        return $new_dict;
    }

    function set_collection_dict($collection) {
        $new_array = array();
        foreach($collection as $obj) $new_array[] = $this->set_dict($obj);
        return $new_array;
    }

    function order_collection_dict($collection, $column, $criterion) {
        $array_temp = array();
        foreach ($collection as $array) {
            $array_temp[] = $array["{{$column}}"];
        }
        array_multisort($array_temp, $criterion, $collection);
        print_r($collection);
        return $collection;
    }

    function order_collection_array($collection, $column, $criterion) {
        $array_temp = array();
        foreach ($collection as $array) {
            $array_temp[] = $array["{$column}"];
        }
        array_multisort($array_temp, $criterion, $collection);
        return $collection;
    }

    function order_collection_objects($collection, $column, $criterion) {
        $array_temp = array();
        foreach ($collection as $array) {
            $array_temp[] = $array->$column;
        }
        array_multisort($array_temp, $criterion, $collection);
        return $collection;
    }

    function descomponer_fecha($fecha='') {
        $dia = date('d');
        $dias_semana = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia_semana = date('w');
        $dia_semana = $dias_semana[$dia_semana];
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $mes = date('m');
        $mes = $mes - 1;
        $mes = $meses[$mes];
        $anio = date('Y');

        $array_fecha = array(
            "{fecha_dia}" => $dia,
            "{fecha_dia_semana}" => $dia_semana,
            "{fecha_mes}" => $mes,
            "{fecha_anio}" => $anio);

        return $array_fecha;
    }
}
?>
