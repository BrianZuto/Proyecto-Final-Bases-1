-- Migración SQL: Crear tabla transacciones
-- Descripción: Tabla para almacenar transacciones de pago de planes de suscripción

CREATE TABLE IF NOT EXISTS transacciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deportista_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_pago TIMESTAMP NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'Pendiente',
    metodo_pago VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_transacciones_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    CONSTRAINT fk_transacciones_plan FOREIGN KEY (plan_id) REFERENCES planes(id) ON DELETE RESTRICT,
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_plan_id (plan_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_pago (fecha_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

