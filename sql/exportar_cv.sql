SELECT
    curriculum.denominacion AS DENOMINACION,
    curriculum.genero AS GENERO,
    curriculum.edad AS EDAD,
    curriculum.estudio AS ESTUDIO,
    curriculum.titulo AS TITULO,
    curriculum.estadocivil AS ESTADOCIVIL,
    curriculum.localidad AS LOCALIDAD,
    curriculum.direccion AS DIRECCION,
    curriculum.correo AS CORREO,
    curriculum.telefono AS TELEFONO,
    curriculum.mensaje AS MENSAJE,
    areainteres.denominacion AS AREA_INTERES,
    provincia.denominacion AS PROVINCIA,
    curriculum.fecha AS FECHA,
    date_format('{fecha_desde}', '%y') as ANO,
    (SELECT
      count(curriculum.curriculum_id)
     FROM
      `curriculum`
     WHERE
      curriculum.fecha BETWEEN '2019-01-01' AND '{fecha_hasta}'
    ) AS CONTADOR
FROM
    `curriculum`
INNER JOIN
     areainteres
ON
     curriculum.areainteres = areainteres.areainteres_id
INNER JOIN
     provincia
ON
     curriculum.provincia = provincia.provincia_id
WHERE
     curriculum.fecha BETWEEN '{fecha_desde}' AND '{fecha_hasta}'
ORDER BY fecha DESC
