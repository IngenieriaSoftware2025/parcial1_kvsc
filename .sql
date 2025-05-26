CREATE TABLE actividad (
    act_id SERIAL PRIMARY KEY,
    act_nombre VARCHAR(100) NOT NULL,
    act_descripcion VARCHAR(255),
    act_fecha_esperada DATETIME YEAR TO MINUTE NOT NULL,
    act_hora_esperada DATETIME HOUR TO MINUTE NOT NULL
);

CREATE TABLE asistencia (
    asi_id SERIAL PRIMARY KEY,
    asi_actividad INTEGER NOT NULL,
    asi_timestamp_registro DATETIME YEAR TO MINUTE NOT NULL,
    asi_fue_puntual BOOLEAN NOT NULL,
    asi_minutos_diferencia INTEGER NOT NULL,
    FOREIGN KEY (asi_actividad) REFERENCES actividad(act_id)
);
