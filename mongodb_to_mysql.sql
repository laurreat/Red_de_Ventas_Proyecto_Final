-- ==============================================
-- Red de Ventas MLM - Arepa Llanerita
-- Migración de MongoDB a MySQL
-- Fecha: 2026-04-15
-- ==============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ==============================================
-- Tabla: users
-- ==============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(24) PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `cedula` varchar(20) UNIQUE,
  `email` varchar(255) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `rol` enum('administrador','lider','vendedor','cliente') DEFAULT 'cliente',
  `activo` tinyint(1) DEFAULT 1,
  `referido_por` varchar(24) DEFAULT NULL,
  `codigo_referido` varchar(20) UNIQUE,
  `total_referidos` int DEFAULT 0,
  `comisiones_ganadas` decimal(15,2) DEFAULT 0.00,
  `comisiones_disponibles` decimal(15,2) DEFAULT 0.00,
  `meta_mensual` decimal(15,2) DEFAULT 0.00,
  `ventas_mes_actual` decimal(15,2) DEFAULT 0.00,
  `nivel_vendedor` int DEFAULT 0,
  `zonas_asignadas` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `configuracion_personal` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`referido_por`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `apellidos`, `cedula`, `email`, `password`, `telefono`, `direccion`, `ciudad`, `departamento`, `fecha_nacimiento`, `rol`, `activo`, `referido_por`, `codigo_referido`, `total_referidos`, `comisiones_ganadas`, `comisiones_disponibles`, `meta_mensual`, `ventas_mes_actual`, `nivel_vendedor`, `email_verified_at`, `remember_token`, `configuracion_personal`, `created_at`, `updated_at`) VALUES
('69a9d474d2c328e862035066', 'Administrador', 'Principal', '12345678', 'admin@arepallanerita.com', '$2y$12$J4bByQW17Ow5FYytdDJq5u.gznzg9h2uLJYKRu7086/21bdUlMz82', '3001234567', 'Oficina Principal - Arepa la Llanerita', 'Villavicencio', 'Meta', '1980-01-01', 'administrador', 1, NULL, 'ADMIN001', 0, 0.00, 0.00, 1000000.00, 450000.00, 0, '2026-03-05 19:07:32', 'D8guqXuKZVdCw8XfJuscuSJNJ4VqYxihg6dHVU6vfwAUHDR8M5x5czPIIHI1', '{\"notif_email_pedidos\":false,\"notif_generales\":true}', '2026-03-05 19:07:32', '2026-03-09 19:38:27'),
('69a9d475d2c328e862035067', 'Carlos', 'Rodriguez', '87654321', 'carlos.rodriguez@arepallanerita.com', '$2y$12$ChZTbx9mrqVDBuRat4apnO6nNvZeRTeRSWturKktrwKkrq0OaSPpu', '3009876543', 'Calle 15 #10-25', 'Villavicencio', 'Meta', '1985-05-15', 'lider', 1, '69a9d474d2c328e862035066', 'LIDER001', 3, 250000.00, 50000.00, 500000.00, 320000.00, 2, '2026-03-05 19:07:33', NULL, NULL, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035068', 'Ana', 'Lopez', '11223344', 'ana.lopez@arepallanerita.com', '$2y$12$1yPApB2QOtRx0XUl7tFNxOG47OJYpXb7T4nKu1oMbQImmd6C9TOH6', '3011234567', 'Carrera 20 #8-15', 'Villavicencio', 'Meta', '1990-08-20', 'vendedor', 1, '69a9d475d2c328e862035067', 'VEND001', 5, 180000.00, 30000.00, 200000.00, 150000.00, 1, '2026-03-05 19:07:33', NULL, NULL, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035069', 'Miguel', 'Torres', '55667788', 'miguel.torres@arepallanerita.com', '$2y$12$Dq4KbS1nO/tm2qC/Gin/9epeMV9SDsYXXpjYHprej/nu52a5kK1Zq', '3022345678', 'Avenida 40 #25-10', 'Villavicencio', 'Meta', '1988-12-10', 'vendedor', 1, '69a9d475d2c328e862035067', 'VEND002', 2, 120000.00, 20000.00, 180000.00, 95000.00, 1, '2026-03-05 19:07:33', NULL, NULL, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e86203506a', 'Maria', 'Gonzalez', '99887766', 'maria.gonzalez@email.com', '$2y$12$5/sZiIny481YH1dEf298KOEjk3/jdu.agHe.raLjb5obpcnBx.Xg2', '3033456789', 'Barrio La Esperanza, Calle 8 #12-34', 'Villavicencio', 'Meta', '1992-03-25', 'cliente', 1, '69a9d475d2c328e862035068', 'CLI001', 1, 0.00, 0.00, 0.00, 0.00, 0, '2026-03-05 19:07:33', NULL, NULL, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e86203506b', 'Pedro', 'Ramirez', '44332211', 'pedro.ramirez@email.com', '$2y$12$nGdDx6pVLYVNcOJFO5ySmOS3ogPPk8hqSqKkwjIY/y0tUxESDIes6', '3044567890', 'Barrio Los Pinos, Carrera 15 #20-45', 'Villavicencio', 'Meta', '1987-11-30', 'cliente', 1, '69a9d475d2c328e862035068', 'CLI002', 0, 0.00, 0.00, 0.00, 0.00, 0, '2026-03-05 19:07:33', NULL, NULL, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69aadd8bad3650118f0fdb82', 'Luis Urrea', NULL, NULL, 'luis2005.320@gmail.com', '$2y$12$knjCN5qYBX.5WSiIWcFykO.NWTUtKkgRT/aZGEboFverX.G3Yn1O2', '3154311266', 'calle 1e 7bis 07', NULL, NULL, '2005-06-04', 'cliente', 1, NULL, 'REF9622', 0, 0.00, 0.00, 0.00, 0.00, 0, NULL, 'ihjhqjOlsvGhL2JiNbb4E6xhXBvXWN2UOiFRwe3RwORW6jAVR8zySs9WYlOT', NULL, '2026-03-06 13:58:35', '2026-03-09 19:46:35'),
('69aaea780bd7afaf7308b4f2', 'DANIEL', 'TIMANA', '1125180685', 'usuariocampesena2025@gmail.com', '$2y$12$.nxRuXr1BM57miumSQQZ7uMChBiyMSEBOBZM2F.9RXyWvPFUDt.Jy', '3219840627', NULL, 'Florencia', 'Otro', '2004-12-27', 'cliente', 1, NULL, 'REF6187', 0, 0.00, 0.00, 0.00, 0.00, 0, NULL, NULL, NULL, '2026-03-06 14:53:44', '2026-03-06 14:53:44');

-- ==============================================
-- Tabla: roles
-- ==============================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id` varchar(24) PRIMARY KEY,
  `name` varchar(50) NOT NULL UNIQUE,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `system_role` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `permissions`, `active`, `system_role`, `created_at`, `updated_at`) VALUES
('69a9d474d2c328e862035062', 'administrador', 'Administrador', 'Acceso completo al sistema', '[\"admin.dashboard\",\"admin.users.view\",\"admin.users.create\",\"admin.users.edit\",\"admin.users.delete\",\"admin.products.view\",\"admin.products.create\",\"admin.products.edit\",\"admin.products.delete\",\"admin.orders.view\",\"admin.orders.create\",\"admin.orders.edit\",\"admin.orders.delete\",\"admin.roles.view\",\"admin.roles.create\",\"admin.roles.edit\",\"admin.roles.delete\",\"admin.reports.view\",\"admin.settings.view\",\"admin.settings.edit\"]', 1, 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035063', 'lider', 'Líder', 'Gestión de equipos y ventas', '[\"dashboard.view\",\"orders.view\",\"orders.create\",\"orders.edit\",\"products.view\",\"team.view\",\"team.reports\",\"commissions.view\"]', 1, 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035064', 'vendedor', 'Vendedor', 'Gestión de ventas y clientes', '[\"dashboard.view\",\"orders.view\",\"orders.create\",\"products.view\",\"clients.view\",\"clients.create\",\"commissions.view\"]', 1, 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035065', 'cliente', 'Cliente', 'Acceso a catálogo y pedidos', '[\"catalog.view\",\"orders.view\",\"profile.edit\"]', 1, 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32');

-- ==============================================
-- Tabla: permissions
-- ==============================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` varchar(24) PRIMARY KEY,
  `name` varchar(100) NOT NULL UNIQUE,
  `display_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `category`, `active`, `created_at`, `updated_at`) VALUES
('69a9d474d2c328e862035042', 'admin.dashboard', 'Dashboard Administrativo', 'Acceso al dashboard administrativo', 'admin', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035043', 'admin.users.view', 'Ver Usuarios', 'Ver listado de usuarios', 'admin', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035044', 'admin.users.create', 'Crear Usuarios', 'Crear nuevos usuarios', 'admin', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035045', 'admin.users.edit', 'Editar Usuarios', 'Editar usuarios existentes', 'admin', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035046', 'admin.users.delete', 'Eliminar Usuarios', 'Eliminar usuarios', 'admin', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035047', 'admin.products.view', 'Ver Productos (Admin)', 'Ver gestión de productos en admin', 'products', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035048', 'admin.products.create', 'Crear Productos', 'Crear nuevos productos', 'products', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035049', 'admin.products.edit', 'Editar Productos', 'Editar productos existentes', 'products', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504a', 'admin.products.delete', 'Eliminar Productos', 'Eliminar productos', 'products', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504b', 'admin.orders.view', 'Ver Pedidos (Admin)', 'Ver gestión de pedidos en admin', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504c', 'admin.orders.create', 'Crear Pedidos (Admin)', 'Crear pedidos desde admin', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504d', 'admin.orders.edit', 'Editar Pedidos', 'Editar pedidos existentes', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504e', 'admin.orders.delete', 'Eliminar Pedidos', 'Eliminar pedidos', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203504f', 'admin.roles.view', 'Ver Roles', 'Ver gestión de roles', 'roles', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035050', 'admin.roles.create', 'Crear Roles', 'Crear nuevos roles', 'roles', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035051', 'admin.roles.edit', 'Editar Roles', 'Editar roles existentes', 'roles', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035052', 'admin.roles.delete', 'Eliminar Roles', 'Eliminar roles personalizados', 'roles', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035053', 'admin.reports.view', 'Ver Reportes', 'Acceso a reportes administrativos', 'reports', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035054', 'admin.settings.view', 'Ver Configuración', 'Ver configuración del sistema', 'settings', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035055', 'admin.settings.edit', 'Editar Configuración', 'Editar configuración del sistema', 'settings', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035056', 'dashboard.view', 'Dashboard General', 'Acceso al dashboard general', 'general', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035057', 'orders.view', 'Ver Pedidos', 'Ver pedidos propios', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035058', 'orders.create', 'Crear Pedidos', 'Crear nuevos pedidos', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035059', 'orders.edit', 'Editar Pedidos Propios', 'Editar pedidos propios', 'orders', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505a', 'products.view', 'Ver Catálogo', 'Ver catálogo de productos', 'products', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505b', 'clients.view', 'Ver Clientes', 'Ver listado de clientes', 'clients', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505c', 'clients.create', 'Crear Clientes', 'Registrar nuevos clientes', 'clients', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505d', 'team.view', 'Ver Equipo', 'Ver miembros del equipo', 'team', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505e', 'team.reports', 'Reportes de Equipo', 'Ver reportes del equipo', 'team', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e86203505f', 'commissions.view', 'Ver Comisiones', 'Ver comisiones propias', 'commissions', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035060', 'catalog.view', 'Ver Catálogo', 'Ver catálogo de productos', 'catalog', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32'),
('69a9d474d2c328e862035061', 'profile.edit', 'Editar Perfil', 'Editar perfil personal', 'profile', 1, '2026-03-05 19:07:32', '2026-03-05 19:07:32');

-- ==============================================
-- Tabla: categorias
-- ==============================================
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` varchar(24) PRIMARY KEY,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
('69a9d475d2c328e86203506c', 'Arepas Tradicionales', 'Arepas tradicionales llaneras', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e86203506d', 'Arepas con Carne', 'Arepas con diferentes tipos de carne', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e86203506e', 'Arepas con Pollo', 'Arepas con pollo en diferentes preparaciones', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e86203506f', 'Arepas Especiales', 'Arepas con preparaciones especiales', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035070', 'Arepas Combinadas', 'Arepas con múltiples ingredientes', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035071', 'Bebidas Tradicionales', 'Bebidas típicas de los llanos', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035072', 'Bebidas Frías', 'Jugos y bebidas refrescantes', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33'),
('69a9d475d2c328e862035073', 'Postres', 'Postres tradicionales llaneros', 1, '2026-03-05 19:07:33', '2026-03-05 19:07:33');

-- ==============================================
-- Tabla: productos
-- ==============================================
CREATE TABLE IF NOT EXISTS `productos` (
  `id` varchar(24) PRIMARY KEY,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria_id` varchar(24) DEFAULT NULL,
  `precio` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock` int DEFAULT 0,
  `stock_minimo` int DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `imagen` varchar(255) DEFAULT NULL,
  `veces_vendido` int DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `categoria_id`, `precio`, `stock`, `stock_minimo`, `activo`, `imagen`, `veces_vendido`, `created_at`, `updated_at`) VALUES
('69a9d475d2c328e862035074', 'Arepa de Queso Llanero', 'Arepa tradicional rellena con queso fresco llanero, cremosa y deliciosa', '69a9d475d2c328e86203506c', 18000.00, 50, 10, 1, 'productos/1773074277_12__Arepa_de_Queso_Llanero.avif', 0, '2026-03-05 19:07:33', '2026-03-09 16:37:57'),
('69a9d475d2c328e862035075', 'Arepa de Cuajada', 'Arepa con cuajada fresca, suave y tradicional', '69a9d475d2c328e86203506c', 16000.00, 40, 8, 1, 'productos/1773074211_8__Arepa_de_Cuajada.avif', 0, '2026-03-05 19:07:33', '2026-03-09 16:36:51'),
('69a9d475d2c328e862035076', 'Arepa Sola', 'Arepa tradicional sin relleno, perfecta para acompañar', '69a9d475d2c328e86203506c', 8000.00, 98, 20, 1, 'productos/1773074156_3__Arepa_Sola.avif', 2, '2026-03-05 19:07:33', '2026-03-09 20:41:12'),
('69a9d475d2c328e862035077', 'Arepa de Carne Mechada', 'Arepa rellena con carne mechada jugosa y bien condimentada', '69a9d475d2c328e86203506d', 25000.00, 29, 5, 1, 'productos/1773074190_6__Arepa_de_Carne_Mechada.avif', 1, '2026-03-05 19:07:33', '2026-03-09 20:35:17'),
('69a9d476d2c328e862035078', 'Arepa de Carne Asada', 'Arepa con carne asada a la parrilla, sabor único', '69a9d475d2c328e86203506d', 28000.00, 24, 5, 1, 'productos/1773074181_5__Arepa_de_Carne_Asada.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:36:21'),
('69a9d476d2c328e862035079', 'Arepa de Chicharrón', 'Arepa rellena con chicharrón crujiente y sabroso', '69a9d475d2c328e86203506d', 22000.00, 35, 7, 1, 'productos/1773074200_7__Arepa_de_Chicharr__n.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:36:40'),
('69a9d476d2c328e86203507a', 'Arepa de Pollo Desmechado', 'Arepa con pollo desmechado en salsa especial', '69a9d475d2c328e86203506e', 23000.00, 40, 8, 1, 'productos/1773074347_10__Arepa_de_Pollo_Desmechado.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:39:07'),
('69a9d476d2c328e86203507b', 'Arepa de Pollo Guisado', 'Arepa con pollo guisado con vegetales frescos', '69a9d475d2c328e86203506e', 24000.00, 30, 6, 1, 'productos/1773074287_11__Arepa_de_Pollo_Guisado.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:38:07'),
('69a9d476d2c328e86203507c', 'Arepa Mixta (Queso + Carne)', 'La combinación perfecta: queso llanero y carne mechada', '69a9d475d2c328e862035070', 32000.00, 18, 4, 1, 'productos/1773074145_2__Arepa_Mixta__Queso___Carne_.avif', 2, '2026-03-05 19:07:34', '2026-03-09 20:41:12'),
('69a9d476d2c328e86203507d', 'Arepa Llanera Especial', 'Arepa con queso, carne mechada, aguacate y suero costeño', '69a9d475d2c328e862035070', 35000.00, 13, 3, 1, 'productos/1773074134_1__Arepa_Llanera_Especial.avif', 2, '2026-03-05 19:07:34', '2026-03-09 20:29:57'),
('69a9d476d2c328e86203507e', 'Arepa de Huevo Perico', 'Arepa rellena con huevo perico tradicional', '69a9d475d2c328e86203506f', 20000.00, 25, 5, 1, 'productos/1773074360_9__Arepa_de_Huevo_Perico.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:39:20'),
('69a9d476d2c328e86203507f', 'Arepa Vegetariana', 'Arepa con aguacate, tomate, cebolla y queso', '69a9d475d2c328e86203506f', 19000.00, 19, 4, 1, 'productos/1773074168_4__Arepa_Vegetariana.avif', 1, '2026-03-05 19:07:34', '2026-03-09 20:35:17'),
('69a9d476d2c328e862035080', 'Chicha Llanera', 'Bebida tradicional de arroz dulce con canela', '69a9d475d2c328e862035071', 12000.00, 60, 15, 1, 'productos/1773074267_13__Chicha_Llanera.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:37:47'),
('69a9d476d2c328e862035081', 'Guarapo de Caña', 'Jugo fresco de caña de azúcar natural', '69a9d475d2c328e862035071', 10000.00, 40, 10, 1, 'productos/1773074249_15__Guarapo_de_Ca__a.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:37:29'),
('69a9d476d2c328e862035082', 'Jugo de Maracuyá', 'Refrescante jugo natural de maracuyá', '69a9d475d2c328e862035072', 8000.00, 50, 12, 1, 'productos/1773074225_16__Jugo_de_Maracuy__.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:37:05'),
('69a9d476d2c328e862035083', 'Limonada Natural', 'Limonada fresca con limón natural y panela', '69a9d475d2c328e862035072', 7000.00, 60, 15, 1, 'productos/1773074239_17__Limonada_Natural.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:37:19'),
('69a9d476d2c328e862035084', 'Quesillo Llanero', 'Postre tradicional de leche y caramelo', '69a9d475d2c328e862035073', 15000.00, 20, 5, 1, 'productos/1773074118_18__Quesillo_Llanero.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:35:18'),
('69a9d476d2c328e862035085', 'Dulce de Lechosa', 'Dulce tradicional de papaya en almíbar', '69a9d475d2c328e862035073', 12000.00, 13, 3, 1, 'productos/1773074259_14__Dulce_de_Lechosa.avif', 0, '2026-03-05 19:07:34', '2026-03-09 16:37:39');

-- ==============================================
-- Tabla: pedidos
-- ==============================================
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` varchar(24) PRIMARY KEY,
  `numero_pedido` varchar(20) UNIQUE NOT NULL,
  `user_id` varchar(24) NOT NULL,
  `vendedor_id` varchar(24) DEFAULT NULL,
  `estado` enum('pendiente','confirmado','en_preparacion','listo','en_camino','entregado','cancelado') DEFAULT 'pendiente',
  `total` decimal(15,2) DEFAULT 0.00,
  `descuento` decimal(15,2) DEFAULT 0.00,
  `total_final` decimal(15,2) DEFAULT 0.00,
  `direccion_entrega` text DEFAULT NULL,
  `telefono_entrega` varchar(20) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `stock_devuelto` tinyint(1) DEFAULT 0,
  `cliente_data` text DEFAULT NULL,
  `vendedor_data` text DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `productos` text DEFAULT NULL,
  `historial_estados` text DEFAULT NULL,
  `fecha_entrega_estimada` timestamp NULL DEFAULT NULL,
  `ip_creacion` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vendedor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pedidos` (`id`, `numero_pedido`, `user_id`, `vendedor_id`, `estado`, `total`, `descuento`, `total_final`, `direccion_entrega`, `telefono_entrega`, `metodo_pago`, `notas`, `stock_devuelto`, `cliente_data`, `vendedor_data`, `created_at`, `updated_at`) VALUES
('69aadf67ad3650118f0fdb84', 'ARE-2026-0001', '69aadd8bad3650118f0fdb82', '69a9d474d2c328e862035066', 'entregado', 52000.00, 0.00, 52000.00, NULL, NULL, NULL, 'prueba factus', 0, '{\"_id\":\"69aadd8bad3650118f0fdb82\",\"name\":\"Luis\",\"apellidos\":\"Urrea\",\"email\":\"luis2005.320@gmail.com\",\"telefono\":\"3154311266\",\"cedula\":\"1137624282\"}', '{\"_id\":\"69a9d474d2c328e862035066\",\"name\":\"Administrador\",\"apellidos\":\"Principal\",\"email\":\"admin@arepallanerita.com\",\"telefono\":\"3001234567\"}', '2026-03-06 14:06:31', '2026-03-06 14:06:41'),
('69aaeb0d0bd7afaf7308b4f4', 'ARE-2026-00002', '69aaea780bd7afaf7308b4f2', NULL, 'entregado', 35000.00, 0.00, 35000.00, 'manzana 6 lote 48 barrio la Ciudadela', '3219840627', 'tarjeta', 'gey lucho', 0, '{\"_id\":\"69aaea780bd7afaf7308b4f2\",\"name\":\"DANIEL\",\"apellidos\":\"TIMANA\",\"email\":\"usuariocampesena2025@gmail.com\",\"telefono\":\"3219840627\",\"cedula\":\"1125180685\"}', NULL, '2026-03-06 14:56:13', '2026-03-06 14:57:25'),
('69af2dc565947841ad02b502', 'ARE-2026-00003', '69aadd8bad3650118f0fdb82', NULL, 'cancelado', 35000.00, 0.00, 35000.00, 'calle 1e 7bis 07', '3154311266', 'efectivo', NULL, 0, '{\"_id\":\"69aadd8bad3650118f0fdb82\",\"name\":\"Luis Urrea\",\"apellidos\":\"\",\"email\":\"luis2005.320@gmail.com\",\"telefono\":\"3154311266\",\"cedula\":\"\"}', NULL, '2026-03-09 20:29:57', '2026-03-09 20:41:26'),
('69af2f0565947841ad02b506', 'ARE-2026-00004', '69aadd8bad3650118f0fdb82', NULL, 'entregado', 84000.00, 0.00, 84000.00, 'calle 1e 7bis 07', '3154311266', 'efectivo', NULL, 0, '{\"_id\":\"69aadd8bad3650118f0fdb82\",\"name\":\"Luis Urrea\",\"apellidos\":\"\",\"email\":\"luis2005.320@gmail.com\",\"telefono\":\"3154311266\",\"cedula\":\"\"}', NULL, '2026-03-09 20:35:17', '2026-03-09 20:38:19'),
('69af306865947841ad02b50c', 'ARE-2026-00005', '69aadd8bad3650118f0fdb82', NULL, 'entregado', 40000.00, 0.00, 40000.00, 'calle 1e 7bis 07', '3154311266', 'efectivo', NULL, 0, '{\"_id\":\"69aadd8bad3650118f0fdb82\",\"name\":\"Luis Urrea\",\"apellidos\":\"\",\"email\":\"luis2005.320@gmail.com\",\"telefono\":\"3154311266\",\"cedula\":\"\"}', NULL, '2026-03-09 20:41:12', '2026-03-09 20:41:54');

-- ==============================================
-- Tabla: detalle_pedidos (detalles de productos por pedido)
-- ==============================================
CREATE TABLE IF NOT EXISTS `detalle_pedidos` (
  `id` varchar(24) PRIMARY KEY,
  `pedido_id` varchar(24) NOT NULL,
  `producto_id` varchar(24) NOT NULL,
  `producto_nombre` varchar(255) DEFAULT NULL,
  `cantidad` int NOT NULL DEFAULT 1,
  `precio_unitario` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `detalle_pedidos` (`id`, `pedido_id`, `producto_id`, `producto_nombre`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
('dp001', '69aadf67ad3650118f0fdb84', '69a9d476d2c328e862035085', 'Dulce de Lechosa', 2, 12000.00, 24000.00),
('dp002', '69aadf67ad3650118f0fdb84', '69a9d476d2c328e862035078', 'Arepa de Carne Asada', 1, 28000.00, 28000.00),
('dp003', '69aaeb0d0bd7afaf7308b4f4', '69a9d476d2c328e86203507d', 'Arepa Llanera Especial', 1, 35000.00, 35000.00),
('dp004', '69af2dc565947841ad02b502', '69a9d476d2c328e86203507d', 'Arepa Llanera Especial', 1, 35000.00, 35000.00),
('dp005', '69af2f0565947841ad02b506', '69a9d475d2c328e862035076', 'Arepa Sola', 1, 8000.00, 8000.00),
('dp006', '69af2f0565947841ad02b506', '69a9d476d2c328e86203507c', 'Arepa Mixta (Queso + Carne)', 1, 32000.00, 32000.00),
('dp007', '69af2f0565947841ad02b506', '69a9d476d2c328e86203507f', 'Arepa Vegetariana', 1, 19000.00, 19000.00),
('dp008', '69af2f0565947841ad02b506', '69a9d475d2c328e862035077', 'Arepa de Carne Mechada', 1, 25000.00, 25000.00),
('dp009', '69af306865947841ad02b50c', '69a9d476d2c328e86203507c', 'Arepa Mixta (Queso + Carne)', 1, 32000.00, 32000.00),
('dp010', '69af306865947841ad02b50c', '69a9d475d2c328e862035076', 'Arepa Sola', 1, 8000.00, 8000.00);

-- ==============================================
-- Tabla: comisiones
-- ==============================================
CREATE TABLE IF NOT EXISTS `comisiones` (
  `id` varchar(24) PRIMARY KEY,
  `user_id` varchar(24) NOT NULL,
  `pedido_id` varchar(24) NOT NULL,
  `tipo` enum('venta_directa','referido_nivel1','referido_nivel2','referido_nivel3') DEFAULT 'venta_directa',
  `porcentaje` decimal(5,2) DEFAULT 0.00,
  `monto` decimal(15,2) NOT NULL,
  `estado` enum('pendiente','pagada','cancelada') DEFAULT 'pendiente',
  `user_data` text DEFAULT NULL,
  `pedido_data` text DEFAULT NULL,
  `detalles_calculo` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pedido_id`) REFERENCES `pedidos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comisiones` (`id`, `user_id`, `pedido_id`, `tipo`, `porcentaje`, `monto`, `estado`, `created_at`, `updated_at`) VALUES
('69aadf67ad3650118f0fdb85', '69a9d474d2c328e862035066', '69aadf67ad3650118f0fdb84', 'venta_directa', 10.00, 5200.00, 'pendiente', '2026-03-06 14:06:31', '2026-03-06 14:06:31');

-- ==============================================
-- Tabla: notificacions
-- ==============================================
CREATE TABLE IF NOT EXISTS `notificacions` (
  `id` varchar(24) PRIMARY KEY,
  `user_id` varchar(24) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `datos_adicionales` text DEFAULT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notificacions` (`id`, `user_id`, `tipo`, `titulo`, `mensaje`, `leida`, `created_at`, `updated_at`) VALUES
('69aadd8bad3650118f0fdb83', '69a9d474d2c328e862035066', 'nuevo_registro', 'Nuevo Usuario Registrado', 'Nuevo usuario registrado: Luis Urrea (luis2005.320@gmail.com)', 1, '2026-03-06 13:58:35', '2026-03-06 14:00:45'),
('69aadf67ad3650118f0fdb86', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-0001', 'Se ha creado un nuevo pedido por $52,000', 1, '2026-03-06 14:06:31', '2026-03-09 20:37:57'),
('69aadf67ad3650118f0fdb87', '69a9d474d2c328e862035066', 'venta', 'Nueva Venta Realizada', 'Has realizado una venta por $52,000.00', 1, '2026-03-06 14:06:31', '2026-03-09 20:37:57'),
('69aadf67ad3650118f0fdb88', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-0001', 'Se ha creado un nuevo pedido de Luis Urrea por $52,000.00', 1, '2026-03-06 14:06:31', '2026-03-09 20:37:57'),
('69aadf67ad3650118f0fdb89', '69a9d475d2c328e862035067', 'pedido', 'Nuevo Pedido #ARE-2026-0001', 'Se ha creado un nuevo pedido de Luis Urrea por $52,000.00', 0, '2026-03-06 14:06:31', '2026-03-06 14:06:31'),
('69aadf67ad3650118f0fdb8a', '69a9d474d2c328e862035066', 'venta', 'Nueva Venta Realizada', 'Has realizado una venta por $52,000.00', 1, '2026-03-06 14:06:31', '2026-03-09 20:37:57'),
('69aadf67ad3650118f0fdb8b', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-0001 Creado', 'Tu pedido ha sido creado exitosamente por un monto de $52,000.00', 1, '2026-03-06 14:06:31', '2026-03-08 23:59:25'),
('69aadf71ad3650118f0fdb8c', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-0001 - Entregado', '¡Tu pedido ha sido entregado exitosamente!', 1, '2026-03-06 14:06:41', '2026-03-06 14:45:16'),
('69aadf71ad3650118f0fdb8d', '69a9d474d2c328e862035066', 'venta', 'Pedido Entregado #ARE-2026-0001', 'El pedido ha sido entregado exitosamente. ¡Venta completada!', 1, '2026-03-06 14:06:41', '2026-03-09 20:37:57'),
('69aaea780bd7afaf7308b4f3', '69a9d474d2c328e862035066', 'nuevo_registro', 'Nuevo Usuario Registrado', 'Nuevo usuario registrado: DANIEL TIMANA (usuariocampesena2025@gmail.com)', 1, '2026-03-06 14:53:44', '2026-03-09 20:37:57'),
('69aaeb0d0bd7afaf7308b4f5', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-00002', 'Se ha creado un nuevo pedido por $35,000', 1, '2026-03-06 14:56:13', '2026-03-09 20:37:57'),
('69aaeb0d0bd7afaf7308b4f6', '69aaea780bd7afaf7308b4f2', 'pedido', 'Pedido #ARE-2026-00002 Creado', 'Tu pedido ha sido creado exitosamente por un monto de $35.000', 1, '2026-03-06 14:56:13', '2026-03-06 14:56:37'),
('69aaeb0d0bd7afaf7308b4f7', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido Sin Vendedor #ARE-2026-00002', 'El cliente DANIEL TIMANA ha creado un pedido por $35.000 (sin vendedor asignado)', 1, '2026-03-06 14:56:13', '2026-03-09 19:49:44'),
('69aaeb550bd7afaf7308b4f8', '69aaea780bd7afaf7308b4f2', 'pedido', 'Pedido #ARE-2026-00002 - Entregado', '¡Tu pedido ha sido entregado exitosamente!', 0, '2026-03-06 14:57:25', '2026-03-06 14:57:25'),
('69af2dc565947841ad02b503', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-00003', 'Se ha creado un nuevo pedido por $35,000', 1, '2026-03-09 20:29:57', '2026-03-09 20:37:57'),
('69af2dc565947841ad02b504', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-00003 Creado', 'Tu pedido ha sido creado exitosamente por un monto de $35.000', 1, '2026-03-09 20:29:57', '2026-03-09 20:31:48'),
('69af2dc565947841ad02b505', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido Sin Vendedor #ARE-2026-00003', 'El cliente Luis Urrea  ha creado un pedido por $35.000 (sin vendedor asignado)', 1, '2026-03-09 20:29:57', '2026-03-09 20:37:57'),
('69af2f0565947841ad02b507', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-00004', 'Se ha creado un nuevo pedido por $84,000', 1, '2026-03-09 20:35:17', '2026-03-09 20:37:57'),
('69af2f0565947841ad02b508', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-00004 Creado', 'Tu pedido ha sido creado exitosamente por un monto de $84.000', 1, '2026-03-09 20:35:17', '2026-03-09 20:36:47'),
('69af2f0565947841ad02b509', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido Sin Vendedor #ARE-2026-00004', 'El cliente Luis Urrea  ha creado un pedido por $84.000 (sin vendedor asignado)', 1, '2026-03-09 20:35:17', '2026-03-09 20:37:57'),
('69af2fbb65947841ad02b50a', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-00004 - Entregado', '¡Tu pedido ha sido entregado exitosamente!', 1, '2026-03-09 20:38:19', '2026-03-09 20:40:58'),
('69af306865947841ad02b50d', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido #ARE-2026-00005', 'Se ha creado un nuevo pedido por $40,000', 1, '2026-03-09 20:41:12', '2026-03-09 20:42:17'),
('69af306965947841ad02b50e', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-00005 Creado', 'Tu pedido ha sido creado exitosamente por un monto de $40.000', 1, '2026-03-09 20:41:13', '2026-03-09 20:41:19'),
('69af306965947841ad02b50f', '69a9d474d2c328e862035066', 'pedido', 'Nuevo Pedido Sin Vendedor #ARE-2026-00005', 'El cliente Luis Urrea  ha creado un pedido por $40.000 (sin vendedor asignado)', 1, '2026-03-09 20:41:13', '2026-03-09 20:42:17'),
('69af309265947841ad02b510', '69aadd8bad3650118f0fdb82', 'pedido', 'Pedido #ARE-2026-00005 - Entregado', '¡Tu pedido ha sido entregado exitosamente!', 1, '2026-03-09 20:41:54', '2026-03-09 20:42:37');

-- ==============================================
-- Tabla: configuraciones
-- ==============================================
CREATE TABLE IF NOT EXISTS `configuraciones` (
  `id` varchar(24) PRIMARY KEY,
  `clave` varchar(100) NOT NULL UNIQUE,
  `valor` text DEFAULT NULL,
  `tipo` varchar(20) DEFAULT 'string',
  `categoria` varchar(50) DEFAULT 'general',
  `descripcion` text DEFAULT NULL,
  `es_publica` tinyint(1) DEFAULT 0,
  `es_editable` tinyint(1) DEFAULT 1,
  `grupo` varchar(50) DEFAULT NULL,
  `orden` int DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuraciones` (`id`, `clave`, `valor`, `tipo`, `categoria`, `descripcion`, `es_publica`, `es_editable`, `grupo`, `orden`, `created_at`, `updated_at`) VALUES
('69a9d476d2c328e862035086', 'nombre_empresa', '{\"valor\":\"Arepa la Llanerita\"}', 'string', 'general', 'Nombre de la empresa', 1, 1, 'general', 1, '2026-03-05 19:07:34', '2026-03-05 19:07:34');

-- ==============================================
-- Tabla: password_resets
-- ==============================================
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` varchar(24) PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================
-- Tabla: migrations
-- ==============================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, 'create_users_table', 1),
(2, 'create_roles_table', 1),
(3, 'create_permissions_table', 1),
(4, 'create_migrations_table', 1);

-- ==============================================
-- COMMIT
-- ==============================================
COMMIT;
