-- ============================================================================
-- DATOS DE PRUEBA PARA EL SISTEMA
-- Descripción: Inserta datos de prueba completos para todas las tablas
-- ============================================================================

USE bases1;

-- ============================================================================
-- 1. PLANES (Debe ejecutarse después de crear la tabla planes)
-- ============================================================================
INSERT IGNORE INTO planes (nombre, descripcion, precio, duracion_dias, activo, created_at, updated_at) VALUES
('Plan Básico', 'Plan ideal para principiantes. Acceso a ejercicios básicos y rutinas simples.', 50000.00, 30, TRUE, NOW(), NOW()),
('Plan Intermedio', 'Plan para usuarios con experiencia. Acceso a rutinas intermedias y avanzadas.', 80000.00, 30, TRUE, NOW(), NOW()),
('Plan Premium', 'Plan completo con acceso a todas las rutinas, ejercicios y contenido exclusivo.', 120000.00, 30, TRUE, NOW(), NOW()),
('Plan Anual Básico', 'Plan básico con descuento por pago anual.', 500000.00, 365, TRUE, NOW(), NOW()),
('Plan Anual Premium', 'Plan premium con descuento por pago anual.', 1200000.00, 365, TRUE, NOW(), NOW());

-- ============================================================================
-- 2. USUARIOS DE PRUEBA (Debe ejecutarse después de crear roles)
-- ============================================================================
-- Contraseña para todos: password123 (hash bcrypt)
INSERT IGNORE INTO users (name, email, password, rol_id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, nombre_usuario, telefonos, direccion, created_at, updated_at) VALUES
-- Administradores
('Juan Pérez Admin', 'admin@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Juan', 'Carlos', 'Pérez', 'García', 'admin', '3001234567', 'Calle 123 #45-67, Bogotá', NOW(), NOW()),
('María López Admin', 'maria.admin@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'María', 'Isabel', 'López', 'Rodríguez', 'maria_admin', '3002345678', 'Avenida 456 #78-90, Medellín', NOW(), NOW()),

-- Entrenadores
('Carlos Entrenador', 'carlos.trainer@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Carlos', 'Alberto', 'Martínez', 'Sánchez', 'carlos_trainer', '3003456789', 'Carrera 789 #12-34, Cali', NOW(), NOW()),
('Ana Coach', 'ana.coach@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Ana', 'Lucía', 'González', 'Hernández', 'ana_coach', '3004567890', 'Calle 321 #56-78, Barranquilla', NOW(), NOW()),
('Pedro Fitness', 'pedro.fitness@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Pedro', 'Antonio', 'Ramírez', 'Torres', 'pedro_fitness', '3005678901', 'Avenida 654 #90-12, Bucaramanga', NOW(), NOW()),

-- Deportistas
('Luis Deportista', 'luis.deportista@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Luis', 'Fernando', 'Osorio', 'Vargas', 'luis_deportista', '3006789012', 'Calle 987 #23-45, Bogotá', NOW(), NOW()),
('Laura Atleta', 'laura.atleta@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Laura', 'Patricia', 'Torres', 'Jiménez', 'laura_atleta', '3007890123', 'Carrera 147 #34-56, Medellín', NOW(), NOW()),
('Brian Fitness', 'brian.fitness@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Brian', 'Alejandro', 'Zuleta', 'Moreno', 'brian_fitness', '3008901234', 'Avenida 258 #45-67, Cali', NOW(), NOW()),
('Sofía Deportista', 'sofia.deportista@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Sofía', 'Alejandra', 'Morales', 'Castro', 'sofia_deportista', '3009012345', 'Calle 369 #56-78, Barranquilla', NOW(), NOW()),
('Diego Runner', 'diego.runner@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Diego', 'Armando', 'Rojas', 'Pérez', 'diego_runner', '3010123456', 'Carrera 741 #67-89, Bogotá', NOW(), NOW()),
('Valentina Fit', 'valentina.fit@fittracker.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'Valentina', 'María', 'Castro', 'López', 'valentina_fit', '3011234567', 'Avenida 852 #78-90, Medellín', NOW(), NOW());

-- ============================================================================
-- 3. TABLAS DE HERENCIA (deportistas, entrenadores, administradores)
-- ============================================================================
-- Administradores
INSERT IGNORE INTO administradores (user_id, created_at, updated_at) 
SELECT id, created_at, updated_at FROM users WHERE rol_id = 1;

-- Entrenadores
INSERT IGNORE INTO entrenadores (user_id, created_at, updated_at) 
SELECT id, created_at, updated_at FROM users WHERE rol_id = 2;

-- Deportistas
INSERT IGNORE INTO deportistas (user_id, created_at, updated_at) 
SELECT id, created_at, updated_at FROM users WHERE rol_id = 3;

-- ============================================================================
-- 4. PLANES ASIGNADOS A USUARIOS
-- ============================================================================
INSERT IGNORE INTO plan_usuario (user_id, plan_id, fecha_inicio, fecha_fin, activo, created_at, updated_at) VALUES
-- Luis tiene Plan Premium
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), 3, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(NOW(), INTERVAL 20 DAY), TRUE, NOW(), NOW()),
-- Laura tiene Plan Intermedio
((SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com'), 2, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), TRUE, NOW(), NOW()),
-- Brian tiene Plan Básico
((SELECT id FROM users WHERE email = 'brian.fitness@fittracker.com'), 1, DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), TRUE, NOW(), NOW()),
-- Sofía tiene Plan Premium
((SELECT id FROM users WHERE email = 'sofia.deportista@fittracker.com'), 3, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 27 DAY), TRUE, NOW(), NOW()),
-- Diego tiene Plan Intermedio
((SELECT id FROM users WHERE email = 'diego.runner@fittracker.com'), 2, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), TRUE, NOW(), NOW()),
-- Valentina tiene Plan Básico
((SELECT id FROM users WHERE email = 'valentina.fit@fittracker.com'), 1, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), TRUE, NOW(), NOW());

-- ============================================================================
-- 5. EJERCICIOS DE PRUEBA
-- ============================================================================
INSERT IGNORE INTO ejercicios (nombre, descripcion, categoria_id, grupo_muscular, dificultad, duracion_minutos, calorias_estimadas, calificacion, equipo, instrucciones, imagen_url, video_url, activo, created_at, updated_at) VALUES
-- Ejercicios de Pecho
('Press de Banca', 'Ejercicio fundamental para desarrollar el pecho. Acostado en un banco, levanta la barra con peso.', (SELECT id FROM categorias WHERE nombre = 'Pecho' LIMIT 1), 'Pectorales', 'Intermedio', 5, 50, 4.5, 'Barra, Mancuernas, Banco', '1. Acuéstate en el banco\n2. Agarra la barra con las manos separadas al ancho de los hombros\n3. Baja la barra al pecho\n4. Empuja hacia arriba', NULL, NULL, TRUE, NOW(), NOW()),
('Flexiones', 'Ejercicio de peso corporal para fortalecer pecho, brazos y core.', (SELECT id FROM categorias WHERE nombre = 'Pecho' LIMIT 1), 'Pectorales', 'Principiante', 3, 30, 4.0, 'Ninguno', '1. Posición de plancha\n2. Baja el cuerpo flexionando los codos\n3. Empuja hacia arriba', NULL, NULL, TRUE, NOW(), NOW()),
('Aperturas con Mancuernas', 'Ejercicio de aislamiento para los pectorales.', (SELECT id FROM categorias WHERE nombre = 'Pecho' LIMIT 1), 'Pectorales', 'Intermedio', 4, 40, 4.2, 'Mancuernas, Banco', '1. Acuéstate en banco inclinado\n2. Abre los brazos con mancuernas\n3. Cierra los brazos sobre el pecho', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Espalda
('Dominadas', 'Ejercicio completo para espalda y brazos usando el peso corporal.', (SELECT id FROM categorias WHERE nombre = 'Espalda' LIMIT 1), 'Dorsales', 'Avanzado', 5, 60, 4.8, 'Barra de Dominadas', '1. Cuelga de la barra\n2. Tira del cuerpo hacia arriba\n3. Baja controladamente', NULL, NULL, TRUE, NOW(), NOW()),
('Remo con Barra', 'Ejercicio para desarrollar el grosor de la espalda.', (SELECT id FROM categorias WHERE nombre = 'Espalda' LIMIT 1), 'Dorsales', 'Intermedio', 5, 55, 4.5, 'Barra, Discos', '1. Flexiona las rodillas ligeramente\n2. Tira de la barra hacia el abdomen\n3. Mantén la espalda recta', NULL, NULL, TRUE, NOW(), NOW()),
('Jalones al Pecho', 'Ejercicio en máquina para trabajar la espalda.', (SELECT id FROM categorias WHERE nombre = 'Espalda' LIMIT 1), 'Dorsales', 'Principiante', 4, 45, 4.3, 'Máquina de Jalones', '1. Siéntate en la máquina\n2. Tira de la barra hacia el pecho\n3. Controla el movimiento', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Piernas
('Sentadillas', 'Ejercicio fundamental para piernas y glúteos.', (SELECT id FROM categorias WHERE nombre = 'Piernas' LIMIT 1), 'Cuádriceps', 'Principiante', 4, 50, 4.7, 'Ninguno o Barra', '1. Pies al ancho de los hombros\n2. Baja como si te sentaras\n3. Vuelve a la posición inicial', NULL, NULL, TRUE, NOW(), NOW()),
('Peso Muerto', 'Ejercicio completo para espalda baja, glúteos y piernas.', (SELECT id FROM categorias WHERE nombre = 'Piernas' LIMIT 1), 'Isquiotibiales', 'Avanzado', 6, 70, 4.9, 'Barra, Discos', '1. Pies al ancho de los hombros\n2. Flexiona cadera manteniendo espalda recta\n3. Levanta la barra', NULL, NULL, TRUE, NOW(), NOW()),
('Zancadas', 'Ejercicio unilateral para piernas y glúteos.', (SELECT id FROM categorias WHERE nombre = 'Piernas' LIMIT 1), 'Cuádriceps', 'Intermedio', 4, 45, 4.4, 'Ninguno o Mancuernas', '1. Da un paso largo hacia adelante\n2. Baja la rodilla trasera\n3. Vuelve a la posición inicial', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Brazos
('Curl de Bíceps', 'Ejercicio de aislamiento para bíceps.', (SELECT id FROM categorias WHERE nombre = 'Brazo' LIMIT 1), 'Bíceps', 'Principiante', 3, 25, 4.2, 'Mancuernas o Barra', '1. De pie, agarra las mancuernas\n2. Flexiona los codos levantando el peso\n3. Baja controladamente', NULL, NULL, TRUE, NOW(), NOW()),
('Tríceps en Polea', 'Ejercicio para desarrollar los tríceps.', (SELECT id FROM categorias WHERE nombre = 'Brazo' LIMIT 1), 'Tríceps', 'Intermedio', 3, 30, 4.3, 'Máquina de Polea', '1. Agarra la barra en la polea\n2. Extiende los brazos hacia abajo\n3. Vuelve a la posición inicial', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Hombros
('Press Militar', 'Ejercicio para desarrollar los deltoides.', (SELECT id FROM categorias WHERE nombre = 'Hombros' LIMIT 1), 'Deltoides', 'Intermedio', 4, 40, 4.5, 'Barra o Mancuernas', '1. De pie, levanta la barra sobre la cabeza\n2. Baja controladamente\n3. Repite', NULL, NULL, TRUE, NOW(), NOW()),
('Elevaciones Laterales', 'Ejercicio de aislamiento para deltoides laterales.', (SELECT id FROM categorias WHERE nombre = 'Hombros' LIMIT 1), 'Deltoides', 'Principiante', 3, 20, 4.1, 'Mancuernas', '1. De pie, levanta los brazos lateralmente\n2. Hasta la altura de los hombros\n3. Baja controladamente', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Abdomen
('Abdominales', 'Ejercicio básico para fortalecer el core.', (SELECT id FROM categorias WHERE nombre = 'Abdomen' LIMIT 1), 'Recto Abdominal', 'Principiante', 3, 20, 4.0, 'Ninguno', '1. Acostado, flexiona las rodillas\n2. Levanta el torso hacia las rodillas\n3. Baja controladamente', NULL, NULL, TRUE, NOW(), NOW()),
('Plancha', 'Ejercicio isométrico para core y estabilidad.', (SELECT id FROM categorias WHERE nombre = 'Abdomen' LIMIT 1), 'Core', 'Intermedio', 1, 15, 4.6, 'Ninguno', '1. Posición de plancha\n2. Mantén el cuerpo recto\n3. Aguanta el tiempo indicado', NULL, NULL, TRUE, NOW(), NOW()),

-- Ejercicios de Cardio
('Correr', 'Ejercicio cardiovascular de alta intensidad.', (SELECT id FROM categorias WHERE nombre = 'Cardio' LIMIT 1), 'Sistema Cardiovascular', 'Principiante', 30, 300, 4.8, 'Ninguno', '1. Calienta 5 minutos\n2. Corre a ritmo constante\n3. Enfría 5 minutos', NULL, NULL, TRUE, NOW(), NOW()),
('Burpees', 'Ejercicio funcional de alta intensidad.', (SELECT id FROM categorias WHERE nombre = 'Cardio' LIMIT 1), 'Full Body', 'Avanzado', 5, 80, 4.7, 'Ninguno', '1. Flexión\n2. Salto hacia adelante\n3. Salto vertical\n4. Repite', NULL, NULL, TRUE, NOW(), NOW());

-- ============================================================================
-- 6. ASIGNACIÓN DE EJERCICIOS A PLANES
-- ============================================================================
-- Plan Básico: Ejercicios principiantes
INSERT IGNORE INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
SELECT 1, id, NOW(), NOW() FROM ejercicios WHERE dificultad = 'Principiante' LIMIT 10;

-- Plan Intermedio: Ejercicios intermedios y algunos principiantes
INSERT IGNORE INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
SELECT 2, id, NOW(), NOW() FROM ejercicios WHERE dificultad IN ('Principiante', 'Intermedio') LIMIT 15;

-- Plan Premium: Todos los ejercicios
INSERT IGNORE INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
SELECT 3, id, NOW(), NOW() FROM ejercicios;

-- ============================================================================
-- 7. RUTINAS DE PRUEBA
-- ============================================================================
INSERT IGNORE INTO rutinas (nombre, descripcion, tipo_rutina_id, nivel, imagen_url, tiempo_estimado_minutos, calorias_estimadas, activo, created_at, updated_at) VALUES
('Rutina de Fuerza Principiante', 'Rutina completa para desarrollar fuerza en principiantes. Incluye ejercicios básicos de todos los grupos musculares.', (SELECT id FROM tipo_rutinas WHERE nombre = 'Fuerza' LIMIT 1), 'Principiante', NULL, 45, 250, TRUE, NOW(), NOW()),
('Rutina de Cardio Intenso', 'Rutina cardiovascular de alta intensidad para quemar calorías y mejorar resistencia.', (SELECT id FROM tipo_rutinas WHERE nombre = 'Cardio' LIMIT 1), 'Intermedio', NULL, 30, 400, TRUE, NOW(), NOW()),
('Rutina Full Body', 'Rutina completa que trabaja todo el cuerpo en una sola sesión.', (SELECT id FROM tipo_rutinas WHERE nombre = 'Body' LIMIT 1), 'Intermedio', NULL, 60, 350, TRUE, NOW(), NOW()),
('Rutina de Hipertrofia Avanzada', 'Rutina avanzada para aumentar masa muscular. Requiere experiencia previa.', (SELECT id FROM tipo_rutinas WHERE nombre = 'Hipertrofia' LIMIT 1), 'Avanzado', NULL, 75, 450, TRUE, NOW(), NOW()),
('Rutina de Flexibilidad', 'Rutina de estiramientos y movilidad para mejorar flexibilidad.', (SELECT id FROM tipo_rutinas WHERE nombre = 'Flexibilidad' LIMIT 1), 'Principiante', NULL, 20, 100, TRUE, NOW(), NOW());

-- ============================================================================
-- 8. DETALLE DE RUTINAS (Ejercicios en cada rutina)
-- ============================================================================
-- Rutina de Fuerza Principiante
INSERT IGNORE INTO detalle_rutinas (rutina_id, ejercicio_id, orden, series, repeticiones, peso, descanso_segundos, notas, created_at, updated_at) VALUES
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Sentadillas' LIMIT 1), 1, 3, '10-12', NULL, 60, 'Calentar antes de comenzar', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Press de Banca' LIMIT 1), 2, 3, '8-10', '20kg', 90, 'Usar peso moderado', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Remo con Barra' LIMIT 1), 3, 3, '10-12', '15kg', 60, 'Mantener espalda recta', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Curl de Bíceps' LIMIT 1), 4, 3, '12-15', '5kg', 45, 'Controlar el movimiento', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Abdominales' LIMIT 1), 5, 3, '15-20', NULL, 30, 'No forzar el cuello', NOW(), NOW());

-- Rutina de Cardio Intenso
INSERT IGNORE INTO detalle_rutinas (rutina_id, ejercicio_id, orden, series, repeticiones, peso, descanso_segundos, notas, created_at, updated_at) VALUES
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Correr' LIMIT 1), 1, 1, '20 minutos', NULL, 0, 'Ritmo constante', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Burpees' LIMIT 1), 2, 4, '10-15', NULL, 30, 'Alta intensidad', NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Flexiones' LIMIT 1), 3, 3, '15-20', NULL, 45, 'Mantener forma correcta', NOW(), NOW());

-- Rutina Full Body
INSERT IGNORE INTO detalle_rutinas (rutina_id, ejercicio_id, orden, series, repeticiones, peso, descanso_segundos, notas, created_at, updated_at) VALUES
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Sentadillas' LIMIT 1), 1, 4, '12-15', NULL, 60, NULL, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Press de Banca' LIMIT 1), 2, 4, '10-12', '25kg', 90, NULL, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Remo con Barra' LIMIT 1), 3, 4, '10-12', '20kg', 60, NULL, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Press Militar' LIMIT 1), 4, 3, '10-12', '15kg', 60, NULL, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Zancadas' LIMIT 1), 5, 3, '12 por pierna', NULL, 45, NULL, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), (SELECT id FROM ejercicios WHERE nombre = 'Plancha' LIMIT 1), 6, 3, '30-45 segundos', NULL, 30, NULL, NOW(), NOW());

-- ============================================================================
-- 9. ASIGNACIÓN DE RUTINAS A PLANES
-- ============================================================================
INSERT IGNORE INTO rutina_plan (rutina_id, plan_id, created_at, updated_at) VALUES
-- Plan Básico: Rutinas principiantes
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), 1, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Flexibilidad' LIMIT 1), 1, NOW(), NOW()),

-- Plan Intermedio: Rutinas intermedias
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), 2, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), 2, NOW(), NOW()),

-- Plan Premium: Todas las rutinas
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), 3, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), 3, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), 3, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Hipertrofia Avanzada' LIMIT 1), 3, NOW(), NOW()),
((SELECT id FROM rutinas WHERE nombre = 'Rutina de Flexibilidad' LIMIT 1), 3, NOW(), NOW());

-- ============================================================================
-- 10. PROGRESO DE RUTINAS (Algunos usuarios con progreso)
-- ============================================================================
INSERT IGNORE INTO rutina_usuario_progreso (user_id, rutina_id, porcentaje_completado, estado, fecha_inicio, fecha_ultima_sesion, sesiones_completadas, created_at, updated_at) VALUES
-- Luis tiene progreso en varias rutinas
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), 75.00, 'en_progreso', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 3, NOW(), NOW()),
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), 100.00, 'completada', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 5, NOW(), NOW()),

-- Laura tiene progreso
((SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), 50.00, 'en_progreso', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 2, NOW(), NOW()),

-- Brian tiene progreso
((SELECT id FROM users WHERE email = 'brian.fitness@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), 25.00, 'en_progreso', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 1, NOW(), NOW());

-- ============================================================================
-- 11. PROGRESOS DE EJERCICIOS (Ejercicios completados)
-- ============================================================================
-- Obtener IDs de deportistas
SET @luis_deportista = (SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'));
SET @laura_deportista = (SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com'));
SET @brian_deportista = (SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'brian.fitness@fittracker.com'));

-- Progresos de Luis
INSERT IGNORE INTO progresos (deportista_id, detalle_rutina_id, fecha_registro, porcentaje_completado, created_at, updated_at)
SELECT @luis_deportista, dr.id, DATE_SUB(NOW(), INTERVAL 1 DAY), 100.00, NOW(), NOW()
FROM detalle_rutinas dr
INNER JOIN rutinas r ON dr.rutina_id = r.id
WHERE r.nombre = 'Rutina Full Body'
LIMIT 4;

-- Progresos de Laura
INSERT IGNORE INTO progresos (deportista_id, detalle_rutina_id, fecha_registro, porcentaje_completado, created_at, updated_at)
SELECT @laura_deportista, dr.id, DATE_SUB(NOW(), INTERVAL 2 DAY), 100.00, NOW(), NOW()
FROM detalle_rutinas dr
INNER JOIN rutinas r ON dr.rutina_id = r.id
WHERE r.nombre = 'Rutina de Fuerza Principiante'
LIMIT 3;

-- ============================================================================
-- 12. SESIONES DE ENTRENAMIENTO
-- ============================================================================
INSERT IGNORE INTO sesiones (user_id, rutina_id, fecha_sesion, hora_inicio, hora_fin, duracion_minutos, calorias_quemadas, ejercicios_completados, estado, notas, rendimiento, created_at, updated_at) VALUES
-- Sesiones completadas de Luis
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), '08:00:00', '09:00:00', 60, 350, 6, 'completada', 'Excelente sesión, me sentí fuerte', 'Muy bueno', NOW(), NOW()),
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina de Cardio Intenso' LIMIT 1), DATE_SUB(NOW(), INTERVAL 3 DAY), '07:00:00', '07:30:00', 30, 400, 3, 'completada', 'Cardio intenso, sudé mucho', 'Bueno', NOW(), NOW()),
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), DATE_SUB(NOW(), INTERVAL 5 DAY), '18:00:00', '19:00:00', 60, 340, 6, 'completada', 'Sesión regular', 'Regular', NOW(), NOW()),

-- Sesiones de Laura
((SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina de Fuerza Principiante' LIMIT 1), DATE_SUB(NOW(), INTERVAL 2 DAY), '09:00:00', '09:45:00', 45, 250, 5, 'completada', 'Primera vez haciendo esta rutina', 'Bueno', NOW(), NOW()),

-- Sesión en progreso de Brian
((SELECT id FROM users WHERE email = 'brian.fitness@fittracker.com'), (SELECT id FROM rutinas WHERE nombre = 'Rutina Full Body' LIMIT 1), NOW(), '10:00:00', NULL, NULL, NULL, 2, 'en_progreso', 'Sesión iniciada', NULL, NOW(), NOW());

-- ============================================================================
-- 13. EJERCICIOS COMPLETADOS EN SESIONES
-- ============================================================================
-- Obtener IDs de sesiones
SET @sesion_luis_1 = (SELECT id FROM sesiones WHERE user_id = (SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com') AND fecha_sesion = DATE_SUB(NOW(), INTERVAL 1 DAY) LIMIT 1);
SET @sesion_laura_1 = (SELECT id FROM sesiones WHERE user_id = (SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com') AND fecha_sesion = DATE_SUB(NOW(), INTERVAL 2 DAY) LIMIT 1);

-- Ejercicios de la sesión de Luis
INSERT IGNORE INTO sesion_ejercicios (sesion_id, ejercicio_id, detalle_rutina_id, series_completadas, repeticiones_completadas, peso_usado, tiempo_segundos, notas, created_at, updated_at)
SELECT @sesion_luis_1, dr.ejercicio_id, dr.id, dr.series, dr.repeticiones, dr.peso, NULL, 'Completado correctamente', NOW(), NOW()
FROM detalle_rutinas dr
INNER JOIN rutinas r ON dr.rutina_id = r.id
WHERE r.nombre = 'Rutina Full Body'
LIMIT 6;

-- Ejercicios de la sesión de Laura
INSERT IGNORE INTO sesion_ejercicios (sesion_id, ejercicio_id, detalle_rutina_id, series_completadas, repeticiones_completadas, peso_usado, tiempo_segundos, notas, created_at, updated_at)
SELECT @sesion_laura_1, dr.ejercicio_id, dr.id, dr.series, dr.repeticiones, dr.peso, NULL, 'Bien ejecutado', NOW(), NOW()
FROM detalle_rutinas dr
INNER JOIN rutinas r ON dr.rutina_id = r.id
WHERE r.nombre = 'Rutina de Fuerza Principiante'
LIMIT 5;

-- ============================================================================
-- 14. CONTENIDO EDUCATIVO
-- ============================================================================
INSERT IGNORE INTO contenido (autor_id, titulo, descripcion, contenido, tipo, categoria, imagen_url, video_url, archivo_url, tags, publicado, fecha_publicacion, vistas, likes, created_at, updated_at) VALUES
-- Artículos
((SELECT id FROM users WHERE email = 'carlos.trainer@fittracker.com'), 'Guía Completa de Nutrición para Deportistas', 'Aprende los fundamentos de la nutrición deportiva para maximizar tu rendimiento.', 'La nutrición es fundamental para el rendimiento deportivo. En este artículo aprenderás...', 'articulo', 'Nutrición', NULL, NULL, NULL, 'nutrición, deporte, salud', TRUE, DATE_SUB(NOW(), INTERVAL 10 DAY), 150, 25, NOW(), NOW()),
((SELECT id FROM users WHERE email = 'ana.coach@fittracker.com'), 'Cómo Mejorar tu Técnica en Sentadillas', 'Consejos profesionales para realizar sentadillas de forma segura y efectiva.', 'La sentadilla es uno de los ejercicios más importantes. Aquí te enseñamos...', 'articulo', 'Técnica', NULL, NULL, NULL, 'sentadillas, técnica, fuerza', TRUE, DATE_SUB(NOW(), INTERVAL 5 DAY), 89, 12, NOW(), NOW()),

-- Videos
((SELECT id FROM users WHERE email = 'carlos.trainer@fittracker.com'), 'Rutina de Calentamiento Completa', 'Video tutorial de 10 minutos para calentar antes de entrenar.', 'En este video te mostramos una rutina completa de calentamiento...', 'video', 'Calentamiento', NULL, 'https://youtube.com/watch?v=ejemplo1', NULL, 'calentamiento, prevención, lesiones', TRUE, DATE_SUB(NOW(), INTERVAL 7 DAY), 234, 45, NOW(), NOW()),

-- Consejos
((SELECT id FROM users WHERE email = 'ana.coach@fittracker.com'), 'Importancia del Descanso en el Entrenamiento', 'El descanso es tan importante como el entrenamiento mismo.', 'Muchos deportistas subestiman la importancia del descanso...', 'consejo', 'Recuperación', NULL, NULL, NULL, 'descanso, recuperación, salud', TRUE, DATE_SUB(NOW(), INTERVAL 3 DAY), 67, 18, NOW(), NOW()),

-- Infografías
((SELECT id FROM users WHERE email = 'pedro.fitness@fittracker.com'), 'Grupos Musculares del Cuerpo', 'Infografía completa con todos los grupos musculares principales.', 'Esta infografía muestra todos los grupos musculares...', 'infografia', 'Anatomía', NULL, NULL, NULL, 'anatomía, músculos, educación', TRUE, DATE_SUB(NOW(), INTERVAL 15 DAY), 312, 67, NOW(), NOW());

-- ============================================================================
-- 15. GAMIFICACIONES (Logros)
-- ============================================================================
-- Logros de Luis
INSERT IGNORE INTO gamificaciones (deportista_id, fecha_asignacion, logro, puntos, created_at, updated_at) VALUES
((SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com')), DATE_SUB(NOW(), INTERVAL 10 DAY), 'Primera Sesión Completada', 10, NOW(), NOW()),
((SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com')), DATE_SUB(NOW(), INTERVAL 5 DAY), '5 Sesiones Completadas', 25, NOW(), NOW()),
((SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com')), DATE_SUB(NOW(), INTERVAL 2 DAY), 'Primera Rutina Completada', 30, NOW(), NOW());

-- Logros de Laura
INSERT IGNORE INTO gamificaciones (deportista_id, fecha_asignacion, logro, puntos, created_at, updated_at) VALUES
((SELECT id FROM deportistas WHERE user_id = (SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com')), DATE_SUB(NOW(), INTERVAL 3 DAY), 'Primera Sesión Completada', 10, NOW(), NOW());

-- ============================================================================
-- 16. PROGRESO DE EJERCICIOS INDIVIDUALES
-- ============================================================================
INSERT IGNORE INTO ejercicio_usuario_progreso (user_id, ejercicio_id, fecha_ejecucion, veces_completado, rendimiento, calorias_quemadas, comentarios, created_at, updated_at) VALUES
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM ejercicios WHERE nombre = 'Sentadillas' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), 4, 'Excelente', 200, 'Me sentí muy fuerte hoy', NOW(), NOW()),
((SELECT id FROM users WHERE email = 'luis.deportista@fittracker.com'), (SELECT id FROM ejercicios WHERE nombre = 'Press de Banca' LIMIT 1), DATE_SUB(NOW(), INTERVAL 1 DAY), 3, 'Bueno', 150, 'Pude aumentar el peso', NOW(), NOW()),
((SELECT id FROM users WHERE email = 'laura.atleta@fittracker.com'), (SELECT id FROM ejercicios WHERE nombre = 'Sentadillas' LIMIT 1), DATE_SUB(NOW(), INTERVAL 2 DAY), 3, 'Bueno', 180, 'Primera vez, me costó un poco', NOW(), NOW());

-- ============================================================================
-- FIN DE DATOS DE PRUEBA
-- ============================================================================
-- Nota: Estos datos son de prueba y deben usarse solo en entornos de desarrollo.
-- Para producción, se recomienda usar datos reales y seguros.

