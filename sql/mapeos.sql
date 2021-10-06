
CREATE TABLE IF NOT EXISTS unicom (
    unicom_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nomenclatura INT(4)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS gerencia (
    gerencia_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS centrocosto (
    centrocosto_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , codigo INT(4)
    , denominacion VARCHAR(250)
    , gerencia INT(11)
    , INDEX(gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS menu (
    menu_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenu (
    submenu_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , icon VARCHAR(50)
    , url VARCHAR(50)
    , menu INT(11)
    , INDEX(menu)
    , FOREIGN KEY (menu)
        REFERENCES menu (menu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS item (
    item_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , url VARCHAR(50)
    , detalle VARCHAR(100)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS configuracionmenu (
    configuracionmenu_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nivel INT(11)
    , gerencia INT(11)
    , INDEX (gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS submenuconfiguracionmenu (
    submenuconfiguracionmenu_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES submenu (submenu_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS itemconfiguracionmenu (
    itemconfiguracionmenu_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES configuracionmenu (configuracionmenu_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES item (item_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS log (
    log_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , fecha DATE
    , hora TIME
    , ipv4 VARCHAR(16)
    , usuario VARCHAR(100)
    , accion VARCHAR(200)
) ENGINE=InnoDb;

/* ******************************************************************************************** */
CREATE TABLE IF NOT EXISTS usuariodetalle (
    usuariodetalle_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(50)
    , nombre VARCHAR(50)
    , correoelectronico VARCHAR(250)
    , token TEXT
    , gerencia INT(11)
    , INDEX (gerencia)
    , FOREIGN KEY (gerencia)
        REFERENCES gerencia (gerencia_id)
        ON DELETE CASCADE
    , unicom INT(11)
    , INDEX (unicom)
    , FOREIGN KEY (unicom)
        REFERENCES unicom (unicom_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS usuario (
    usuario_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(50)
    , nivel INT(1)
    , usuariodetalle INT(11)
    , INDEX (usuariodetalle)
    , FOREIGN KEY (usuariodetalle)
        REFERENCES usuariodetalle (usuariodetalle_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

INSERT INTO usuariodetalle (usuariodetalle_id, apellido, nombre, correoelectronico, token, centrocosto, unicom)
VALUES (1, 'Admin', 'admin', 'admin@admin.com', 'ff050c2a6dd7bc3e4602e9702de81d21', 1, 1);

INSERT INTO usuario (usuario_id, denominacion, nivel, usuariodetalle)
VALUES (1, 'admin', 3, 1);

/* ******************************************************************************************** */

/* NEW DESA************************************************************************************ */
CREATE TABLE IF NOT EXISTS clienteusuarioregistro (
    clienteusuarioregistro_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , fecha_registro DATE
    , fecha_activacion DATE
    , token_activacion TEXT
    , proveedor TEXT
    , uid TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS clienteusuariodetalle (
    clienteusuariodetalle_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , apellido TEXT
    , nombre TEXT
    , documento INT(8)
    , telefono BIGINT(15)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS clienteusuario (
    clienteusuario_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(150)
    , token VARCHAR(150)
    , clienteusuariodetalle INT(11)
    , INDEX (clienteusuariodetalle)
    , FOREIGN KEY (clienteusuariodetalle)
        REFERENCES clienteusuariodetalle (clienteusuariodetalle_id)
        ON DELETE CASCADE
    , clienteusuarioregistro INT(11)
    , INDEX (clienteusuarioregistro)
    , FOREIGN KEY (clienteusuarioregistro)
        REFERENCES clienteusuarioregistro (clienteusuarioregistro_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;


/* NEW DESA************************************************************************************ */

CREATE TABLE IF NOT EXISTS unicom (
    unicom_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , nomenclatura INT(4)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cliente (
    cliente_id BIGINT(15) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , apellido VARCHAR(100)
    , nombre VARCHAR(100)
    , documento BIGINT(20)
    , correoelectronico1 VARCHAR(250)
    , correoelectronico2 VARCHAR(250)
    , correoelectronico3 VARCHAR(250)
    , telefono1 BIGINT(13)
    , telefono2 BIGINT(13)
    , telefono3 BIGINT(13)
    , telefono4 BIGINT(13)
    , telefono5 BIGINT(13)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuenta (
    cuenta_id BIGINT(15) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , correoelectronico1 VARCHAR(250)
    , correoelectronico2 VARCHAR(250)
    , activo_factura_digital INT(1)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS cuentacliente (
    cuentacliente_id BIGINT(15) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto BIGINT(15)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES cliente (cliente_id)
        ON DELETE CASCADE
    , compositor BIGINT(15)
    , FOREIGN KEY (compositor)
        REFERENCES cuenta (cuenta_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS suministro (
    suministro_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , suministro INT(7)
    , ubicacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS suministrocuenta (
    suministrocuenta_id BIGINT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto BIGINT(15)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES cuenta (cuenta_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES suministro (suministro_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS deuda (
    deuda_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , numero_factura INT(10)
    , periodo INT(6)
    , tarifa VARCHAR(10)
    , importe_energia FLOAT
    , importe_agua FLOAT
    , importe_total FLOAT
    , estado VARCHAR(250)
    , fecha_emision DATE
    , fecha_vencimiento DATE
    , fecha_ultimo_pago DATE
    , sumnistro_id INT(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS deudasuministro (
    deudasuministro_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES suministro (suministro_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES deuda (deuda_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientoinstitucion (
    mantenimientoinstitucion_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientocategoria (
    mantenimientocategoria_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS departamento (
    departamento_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , unicom INT(11)
    , INDEX (unicom)
    , FOREIGN KEY (unicom)
        REFERENCES unicom (unicom_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientoubicacion (
    mantenimientoubicacion_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , sector TEXT
    , calles TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS departamentomantenimientoubicacion (
    departamentomantenimientoubicacion_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES mantenimientoubicacion (mantenimientoubicacion_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES departamento (departamento_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientopreventivo (
    mantenimientopreventivo_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , numero_eucop VARCHAR(10)
    , fecha_inicio DATE
    , hora_inicio TIME
    , hora_fin TIME
    , motivo TEXT
    , descripcion TEXT
    , responsable_edelar VARCHAR(250)
    , responsable_contratista VARCHAR(250)
    , informe INT(1)
    , aprobado INT(1)
    , mantenimientoinstitucion INT(11)
    , INDEX (mantenimientoinstitucion)
    , FOREIGN KEY (mantenimientoinstitucion)
        REFERENCES mantenimientoinstitucion (mantenimientoinstitucion_id)
        ON DELETE CASCADE
    , mantenimientocategoria INT(11)
    , INDEX (mantenimientocategoria)
    , FOREIGN KEY (mantenimientocategoria)
        REFERENCES mantenimientocategoria (mantenimientocategoria_id)
        ON DELETE CASCADE
    , mantenimientoubicacion INT(11)
    , INDEX (mantenimientoubicacion)
    , FOREIGN KEY (mantenimientoubicacion)
        REFERENCES mantenimientoubicacion (mantenimientoubicacion_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientoceta (
    mantenimientoceta_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , ceta VARCHAR(10)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientodistribuidor (
    mantenimientodistribuidor_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , distribuidor VARCHAR(10)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientocetamantenimientopreventivo (
    mantenimientocetamantenimientopreventivo_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES mantenimientopreventivo (mantenimientopreventivo_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES mantenimientoceta (mantenimientoceta_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS mantenimientodistribuidormantenimientopreventivo (
    mantenimientodistribuidormantenimientopreventivo_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES mantenimientopreventivo (mantenimientopreventivo_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES mantenimientodistribuidor (mantenimientodistribuidor_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS distcetnis (
    distcetnis_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , nis VARCHAR(8)
    , distribuidor VARCHAR(8)
    , cet VARCHAR(8)
    , distrito INT(1)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS exportarfacturadigital (
    exportarfacturadigital_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , suministro INT(8)
    , cuenta BIGINT(15)
    , fecha DATE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tiponovedad (
    tiponovedad_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS novedad (
    novedad_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , titulo TEXT
    , epigrafe TEXT
    , cuerpo TEXT
    , fecha DATE
    , tiponovedad INT(11)
    , INDEX (tiponovedad)
    , FOREIGN KEY (tiponovedad)
        REFERENCES tiponovedad (tiponovedad_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS archivo (
    archivo_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion VARCHAR(250)
    , url VARCHAR(100)
    , fecha_carga DATE
    , formato VARCHAR(50)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS archivonovedad (
    archivonovedad_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES novedad (novedad_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES archivo (archivo_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS areainteres (
    areainteres_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS provincia (
    provincia_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS curriculum (
    curriculum_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , genero VARCHAR(3)
    , localidad TEXT
    , direccion TEXT
    , correo VARCHAR(250)
    , telefono BIGINT(15)
    , mensaje TEXT
    , fecha DATE
    , areainteres INT(11)
    , INDEX (areainteres)
    , FOREIGN KEY (areainteres)
        REFERENCES areainteres (areainteres_id)
        ON DELETE CASCADE
    , provincia INT(11)
    , INDEX (provincia)
    , FOREIGN KEY (provincia)
        REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS agenda (
    agenda_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS destinatario (
    destinatario_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , correoelectronico TEXT
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS destinatarioagenda (
    destinatarioagenda_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES agenda (agenda_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES destinatario (destinatario_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS barrio (
    barrio_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , denominacion TEXT
    , departamento INT(11)
    , INDEX (departamento)
    , FOREIGN KEY (departamento)
        REFERENCES departamento (departamento_id)
        ON DELETE SET NULL
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS barriocoordenada (
    barriocoordenada_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , latitud TEXT
    , longitud TEXT
    , altitud INT(1)
    , etiqueta TEXT
    , fillcolor TEXT
    , strokecolor TEXT
    , indice int(11)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS barriocoordenadabarrio (
    barriocoordenadabarrio_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , compuesto INT(11)
    , INDEX(compuesto)
    , FOREIGN KEY (compuesto)
        REFERENCES barrio (barrio_id)
        ON DELETE CASCADE
    , compositor INT(11)
    , FOREIGN KEY (compositor)
        REFERENCES barriocoordenada (barriocoordenada_id)
        ON DELETE CASCADE
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS coordenadamantenimientopreventivo (
    coordenadamantenimientopreventivo_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , latitud TEXT
    , longitud TEXT
    , altitud INT(1)
    , etiqueta TEXT
    , fillcolor TEXT
    , strokecolor TEXT
    , indice int(11)
    , mantenimientopreventivo INT(11)
    , FOREIGN KEY (mantenimientopreventivo)
      REFERENCES mantenimientopreventivo (mantenimientopreventivo_id)
      ON DELETE CASCADE
) ENGINE=InnoDb;

ALTER TABLE curriculum
ADD edad INT(2) NULL AFTER telefono,
ADD estudio VARCHAR(15) NULL AFTER edad,
ADD titulo VARCHAR(250) NULL AFTER estudio,
ADD estadocivil VARCHAR(100) NULL AFTER titulo;

CREATE TABLE IF NOT EXISTS turnopendiente (
    turnopendiente_id INT(11) NOT NULL
        AUTO_INCREMENT PRIMARY KEY
    , numero TEXT
    , documento INT(8)
    , fecha_hasta DATE
    , hora_solicitud TIME
    , telefono BIGINT(15)
    , correoelectronico VARCHAR(150)
    , estado VARCHAR(150)
    , token_fecha DATE
    , token TEXT
    , turnero_id INT(11)
    , oficina INT(11)
    , INDEX (oficina)
    , FOREIGN KEY (oficina)
      REFERENCES oficina (oficina_id)
      ON DELETE CASCADE
    , tipogestioncomercial INT(11)
    , INDEX (tipogestioncomercial)
    , FOREIGN KEY (tipogestioncomercial)
      REFERENCES tipogestioncomercial (tipogestioncomercial_id)
      ON DELETE CASCADE
) ENGINE=InnoDb;

ALTER TABLE bdedelar.turnopendiente DROP FOREIGN KEY turnopendiente_ibfk_2;
ALTER TABLE turnopendiente ADD CONSTRAINT turnopendiente_ibfk_2 FOREIGN KEY (tramite) REFERENCES tramite(tramite_id) ON DELETE CASCADE ON UPDATE RESTRICT;