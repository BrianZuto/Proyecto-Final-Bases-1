-- Migración SQL: Insertar categorías de grupos musculares
-- Descripción: Inserta las categorías básicas de ejercicios por grupos musculares

INSERT INTO categorias (nombre, descripcion, color, activo, created_at, updated_at) VALUES
('Espalda', 'Ejercicios para fortalecer la espalda, músculos dorsales y lumbares', '#3B82F6', TRUE, NOW(), NOW()),
('Brazo', 'Ejercicios para bíceps, tríceps y antebrazos', '#EF4444', TRUE, NOW(), NOW()),
('Glúteos', 'Ejercicios para tonificar y fortalecer los glúteos', '#EC4899', TRUE, NOW(), NOW()),
('Pecho', 'Ejercicios para pectorales y músculos del pecho', '#F59E0B', TRUE, NOW(), NOW()),
('Piernas', 'Ejercicios para cuádriceps, isquiotibiales y pantorrillas', '#10B981', TRUE, NOW(), NOW()),
('Hombros', 'Ejercicios para deltoides y músculos del hombro', '#8B5CF6', TRUE, NOW(), NOW()),
('Abdomen', 'Ejercicios para core, abdominales y músculos del tronco', '#06B6D4', TRUE, NOW(), NOW()),
('Cardio', 'Ejercicios cardiovasculares y de resistencia', '#14B8A6', TRUE, NOW(), NOW()),
('Flexibilidad', 'Ejercicios de estiramiento y movilidad', '#6366F1', TRUE, NOW(), NOW()),
('Full Body', 'Ejercicios que trabajan múltiples grupos musculares', '#F97316', TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE nombre = nombre;

