-- Migración SQL: Insertar tipos de rutinas iniciales
-- Descripción: Inserta los tipos básicos de rutinas

INSERT INTO tipo_rutinas (nombre, descripcion, color, activo, created_at, updated_at) VALUES
('Fuerza', 'Rutinas enfocadas en el desarrollo de fuerza muscular', '#EF4444', TRUE, NOW(), NOW()),
('Cardio', 'Rutinas cardiovasculares para mejorar resistencia', '#10B981', TRUE, NOW(), NOW()),
('Body', 'Rutinas de entrenamiento con peso corporal', '#3B82F6', TRUE, NOW(), NOW()),
('Hipertrofia', 'Rutinas para aumentar masa muscular', '#F59E0B', TRUE, NOW(), NOW()),
('Flexibilidad', 'Rutinas de estiramiento y movilidad', '#8B5CF6', TRUE, NOW(), NOW()),
('CrossFit', 'Rutinas de alta intensidad funcional', '#EC4899', TRUE, NOW(), NOW()),
('Pilates', 'Rutinas de fortalecimiento y control corporal', '#06B6D4', TRUE, NOW(), NOW()),
('Yoga', 'Rutinas de equilibrio mente-cuerpo', '#14B8A6', TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE nombre = nombre;

