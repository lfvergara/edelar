## IMPORTAR CLIENTES
# TRAER CLIENTES ACTIVOS
SELECT DISTINCT
       cliact_ID AS cliente_id,
       cliact_apenom AS apellido,
       '' AS nombre,
       cliact_DNI AS documento,
       cliact_email1 AS correoelectronico1,
       cliact_email2 AS correoelectronico2,
       cliact_email3 AS correoelectronico3,
       cliact_telefono1 AS telefono1,
       cliact_telefono2 AS telefono2,
       cliact_telefono3 AS telefono3,
       cliact_telefono4 AS telefono4,
       cliact_telefono5 AS telefono5
FROM tbl_clientes_ACTIVOS

# CONTAR CLIENTES ACTIVOS CON EL MISMO ID
SELECT cliact_ID,
       COUNT(cliact_ID) AS CANT
FROM tbl_clientes_ACTIVOS
GROUP BY cliact_ID
ORDER BY CANT DESC

# IMPORTAR CLIENTES EN OBJETO CLIENTE DESDE tbl_clientes_ACTIVOS
INSERT INTO cliente(cliente_id, 
                    apellido,
                    nombre,
                    documento,
                    correoelectronico1,
                    correoelectronico2,
                    correoelectronico3,
                    telefono1,
                    telefono2,
                    telefono3, 
                    telefono4, 
                    telefono5) 
                    (SELECT DISTINCT
                        cliact_ID,
                        cliact_apenom,
                        '',
                        cliact_DNI,
                        cliact_email1,
                        cliact_email2,
                        cliact_email3,
                        cliact_telefono1,
                        cliact_telefono2,
                        cliact_telefono3,
                        cliact_telefono4,
                        cliact_telefono5
                    FROM tbl_clientes_ACTIVOS)

## IMPORTAR CUENTAS
# CONTAR CANTIDAD DE CUENTAS
SELECT cuecor_IDcuenta,
       COUNT(cuecor_IDcuenta) AS CANT
FROM tbl_cuentas_correos 
GROUP BY cuecor_IDcuenta
ORDER BY CANT DESC

# TRAER CUENTAS, CORREOS, FACTURA DIGITAL ACTIVA
SELECT 
  cc.cuecor_IDcuenta,
  cc.cuecor_mail1,
  cc.cuecor_mail2,
  CASE 
    WHEN cfd.clifadi_estado IS NULL THEN 0
    ELSE cfd.clifadi_estado
  END
FROM 
  tbl_cuentas_correos cc LEFT JOIN
  tbl_clientes_factura_digital cfd ON cc.cuecor_IDcuenta = cfd.clifadi_IDcuenta

# IMPORTAR CUENTAS EN OBJETO CUENTA DESDE tbl_cuentas_correos y tbl_clientes_factura_digital
INSERT INTO cuenta(cuenta_id, 
                   correoelectronico1,
                   correoelectronico2,
                   activo_factura_digital) 
                   (SELECT 
                      cc.cuecor_IDcuenta,
                      cc.cuecor_mail1,
                      cc.cuecor_mail2,
                      CASE 
                        WHEN cfd.clifadi_estado IS NULL THEN 0
                        ELSE cfd.clifadi_estado
                      END
                    FROM 
                      tbl_cuentas_correos cc LEFT JOIN
                      tbl_clientes_factura_digital cfd ON cc.cuecor_IDcuenta = cfd.clifadi_IDcuenta)

# IMPORTAR CUENTAS CON SUS CLIENTES CORRESPONDIENTES DESDE cuenta y tbl_clientes_nises_ACTIVOS
INSERT INTO cuentacliente(compuesto,
                          compositor)
                          (SELECT DISTINCT
                            clinis_IDcliente,
                            cuenta_id
                           FROM
                            cuenta c INNER JOIN
                            tbl_clientes_nises_ACTIVOS cna ON c.cuenta_id = cna.clinis_IDcuenta)

## IMPORTAR SUMINISTROS
# TRAER SUMINISTROS
SELECT clinis_NIS,
       clinis_direccion
FROM tbl_clientes_nises_ACTIVOS

# IMPORTAR SUMINISTROS desde tbl_clientes_nises_activos
INSERT INTO suministro(suministro,
                       ubicacion)
                       (SELECT
                          clinis_NIS,
                          clinis_direccion
                        FROM 
                          tbl_clientes_nises_ACTIVOS)


INSERT INTO suministrocuenta(compuesto,compositor)
                            (SELECT 
                              cna.clinis_IDcuenta,
                              s.suministro_id
                            FROM 
                              suministro s,
                              tbl_clientes_nises_ACTIVOS cna
                            WHERE 
                              s.suministro = cna.clinis_NIS)

## IMPORTAR DEUDA
# TRAER DEUDA
SELECT cd.clideu_nfactura,
       cd.clideu_periodo,
       cd.clideu_tarifa,
       cd.clideu_impagua,
       cd.clideu_impenergia,
       cd.clideu_importe,
       cd.clideu_efactura,
       cd.clideu_femision,
       cd.clideu_fvencimiento,
       cd.clideu_fultimpopago
FROM tbl_clientes_deudas cd,
     suministro s
WHERE cd.clideu_NIS = s.suministro

# IMPORTAR DEUDA desde tbl_clientes_deudas
INSERT INTO deuda(numero_factura, 
                  periodo, 
                  tarifa, 
                  importe_energia,
                  importe_agua, 
                  importe_total, 
                  estado, 
                  fecha_emision,
                  fecha_vencimiento, 
                  fecha_ultimo_pago,
                  suministro_id)
                  (SELECT cd.clideu_nfactura,
                          cd.clideu_periodo,
                          cd.clideu_tarifa,
                          cd.clideu_impenergia,
                          cd.clideu_impagua,
                          cd.clideu_importe,
                          cd.clideu_efactura,
                          cd.clideu_femision,
                          cd.clideu_fvencimiento,
                          cd.clideu_fultimpopago,
                          s.suministro_id
                  FROM tbl_clientes_deudas cd,
                       suministro s
                  WHERE cd.clideu_NIS = s.suministro)

# IMPORTAR MANTENIMIENTOS PREVENTIVOS DESDE tbl_cortes_programados
SELECT 
  cp.cor_ID AS ID,
  cp.cor_notaEUCOP AS NUMEUCOP,
  cp.cor_fini AS FINI,
  cp.cor_hini AS HINI,
  cp.cor_hfin AS HFIN,
  cp.cor_motivo AS MOTIVO,
  cp.cor_descripcion AS DESCRIPCION,
  cp.cor_responsable_edelar AS RESPEDELAR,
  cp.cor_responsable_contratista AS RESPCONTRATISTA,
  cp.cor_informe AS INFORME,
  cp.cor_aprobado AS APROBADO,
  CASE
    WHEN cp.cor_institucion = 'EDELAR' THEN 2
    WHEN cp.cor_institucion = 'TRANSNOA' THEN 3
    WHEN cp.cor_institucion = 'TRANSENER' THEN 4
    ELSE 1
  END AS MANTENIMIENTOINSTITUCION,
  CASE
    WHEN cp.cor_categoria = 'Urgencia' THEN 3
    WHEN cp.cor_categoria = 'Programado (> 48hs)' THEN 2
    ELSE 1
  END AS MANTENIMIENTOCATEGORIA,
  cp.cor_ID AS MANTENIMIENTOUBICACION,
  cp.cor_ID AS MANTENIMIENTOUBICACION_ID,
  cu.corubi_sector,
  cu.corubi_calles,
  cu.corubi_IDdistrito
FROM 
    tbl_cortesprogramados cp INNER JOIN
    tbl_cortesubicaciones cu ON cp.cor_ID = cu.corubi_IDcorte
ORDER BY
  cp.cor_ID ASC