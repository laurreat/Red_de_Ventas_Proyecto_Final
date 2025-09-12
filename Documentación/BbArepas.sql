CREATE TABLE `Pedidos` (
	`idPedidos` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idUsuario_fk` INTEGER NOT NULL,
	`idUsuario_vendedor_fk` INTEGER NOT NULL,
	`direccion` VARCHAR(255),
	`idEstadoPedido_fk` INTEGER NOT NULL,
	PRIMARY KEY(`idPedidos`)
);


CREATE TABLE `Roles` (
	`idRol` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`Rol` VARCHAR(50),
	PRIMARY KEY(`idRol`)
);


CREATE TABLE `RolesDelUsuario` (
	`idRolUsuario` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idRol_fk` INTEGER NOT NULL,
	`idUsuario_fk` INTEGER NOT NULL,
	PRIMARY KEY(`idRolUsuario`)
);


CREATE INDEX `RolesDelUsuario_index_0`
ON `RolesDelUsuario` ();
CREATE TABLE `Usuarios` (
	`idUsuarios` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`NumDocumento` VARCHAR(30) NOT NULL,
	`Nombres` VARCHAR(255) NOT NULL,
	`Apellidos` VARCHAR(255) NOT NULL,
	`Correo` VARCHAR(255) NOT NULL,
	`Telefono` VARCHAR(30),
	`clave` VARCHAR(255) NOT NULL,
	`Activo` TINYINT NOT NULL DEFAULT 1,
	PRIMARY KEY(`idUsuarios`)
);


CREATE TABLE `RelacionesEntreUsuarios` (
	`idRelacion` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idUsuarioLider_fk` INTEGER NOT NULL,
	`idUsuarioVendedor_fk` INTEGER NOT NULL,
	PRIMARY KEY(`idRelacion`)
);


CREATE TABLE `Productos` (
	`idProducto` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`NombreProducto` VARCHAR(255) NOT NULL,
	`idCategoria_fk` INTEGER NOT NULL,
	`CostoProducion` DOUBLE NOT NULL,
	`Precio_final_cliente` DOUBLE NOT NULL,
	`img` VARCHAR(255),
	`fecha_caducidad` DATETIME NOT NULL,
	PRIMARY KEY(`idProducto`)
);


CREATE TABLE `Categorias` (
	`idCategoria` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`NombreCategoria` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`idCategoria`)
);


CREATE TABLE `Movimientos_inventario` (
	`idMovimiento_inventario` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`del_usuario_id_fk` INTEGER NOT NULL,
	`a_usuario_id_fk` INTEGER NOT NULL,
	`idProducto_fk` INTEGER NOT NULL,
	`Cantidad` INTEGER NOT NULL,
	PRIMARY KEY(`idMovimiento_inventario`)
);


CREATE TABLE `InventarioDeUsuario` (
	`idProductoUsuario` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idProducto` INTEGER,
	`idUsuario` INTEGER,
	`Cantidad` INTEGER,
	PRIMARY KEY(`idProductoUsuario`)
);


CREATE TABLE `productosPedidos` (
	`idproductosPedidos` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idProducto_fk` INTEGER,
	`idPedio_fk` INTEGER,
	`Cantidad` INTEGER,
	PRIMARY KEY(`idproductosPedidos`)
);


CREATE TABLE `Estados_pedidos` (
	`idEstadoPedido` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`estado` VARCHAR(80),
	PRIMARY KEY(`idEstadoPedido`)
);


CREATE TABLE `Facturas` (
	`idFactura` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`idPedido_fk` INTEGER NOT NULL,
	`total` DOUBLE NOT NULL,
	`fecha` DATETIME NOT NULL,
	PRIMARY KEY(`idFactura`)
);


ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `RolesDelUsuario`(`idUsuario_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Roles`
ADD FOREIGN KEY(`idRol`) REFERENCES `RolesDelUsuario`(`idRol_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `RelacionesEntreUsuarios`(`idUsuarioLider_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `RelacionesEntreUsuarios`
ADD FOREIGN KEY(`idUsuarioVendedor_fk`) REFERENCES `Usuarios`(`idUsuarios`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Categorias`
ADD FOREIGN KEY(`idCategoria`) REFERENCES `Productos`(`idCategoria_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Productos`
ADD FOREIGN KEY(`idProducto`) REFERENCES `Movimientos_inventario`(`idProducto_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `Movimientos_inventario`(`del_usuario_id_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `Movimientos_inventario`(`a_usuario_id_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Productos`
ADD FOREIGN KEY(`idProducto`) REFERENCES `InventarioDeUsuario`(`idProducto`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `InventarioDeUsuario`(`idUsuario`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Productos`
ADD FOREIGN KEY(`idProducto`) REFERENCES `productosPedidos`(`idProducto_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Estados_pedidos`
ADD FOREIGN KEY(`idEstadoPedido`) REFERENCES `Pedidos`(`idEstadoPedido_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Pedidos`
ADD FOREIGN KEY(`idPedidos`) REFERENCES `productosPedidos`(`idPedio_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `Pedidos`(`idUsuario_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Usuarios`
ADD FOREIGN KEY(`idUsuarios`) REFERENCES `Pedidos`(`idUsuario_vendedor_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `Pedidos`
ADD FOREIGN KEY(`idPedidos`) REFERENCES `Facturas`(`idPedido_fk`)
ON UPDATE NO ACTION ON DELETE NO ACTION;