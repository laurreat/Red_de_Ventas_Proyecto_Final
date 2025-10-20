# CASOS DE USO - RED DE VENTAS MLM
## AREPA LA LLANERITA

---

## ÍNDICE

1. [Introducción](#introducción)
2. [Actores del Sistema](#actores-del-sistema)
3. [Diagrama General de Casos de Uso](#diagrama-general)
4. [Casos de Uso - Módulo de Autenticación](#autenticación)
5. [Casos de Uso - Módulo de Administración](#administración)
6. [Casos de Uso - Módulo de Líder](#líder)
7. [Casos de Uso - Módulo de Vendedor](#vendedor)
8. [Casos de Uso - Módulo de Cliente](#cliente)


---

## 1. INTRODUCCIÓN

Este documento describe todos los casos de uso del Sistema de Red de Ventas MLM - Arepa La Llanerita. Cada caso de uso detalla las interacciones entre los usuarios (actores) y el sistema para lograr objetivos específicos.

### Propósito
Documentar de manera clara y precisa todas las funcionalidades del sistema desde la perspectiva del usuario.

### Alcance
El sistema gestiona:
- Autenticación y autorización de usuarios
- Gestión de productos y pedidos
- Sistema MLM de referidos y comisiones
- Dashboards personalizados por rol
- Reportes y analytics
- Notificaciones en tiempo real

---

## 2. ACTORES DEL SISTEMA

### 2.1 Actor: Administrador
**Descripción**: Usuario con máximos privilegios en el sistema. Tiene acceso completo a todas las funcionalidades.

**Responsabilidades**:
- Gestionar usuarios del sistema
- Configurar productos y categorías
- Supervisar todos los pedidos
- Aprobar y gestionar comisiones
- Generar reportes generales
- Configurar parámetros del sistema

---

### 2.2 Actor: Líder
**Descripción**: Usuario responsable de gestionar un equipo de vendedores.

**Responsabilidades**:
- Gestionar su equipo de vendedores
- Asignar metas y objetivos
- Capacitar al equipo
- Supervisar rendimiento del equipo
- Gestionar sus propias comisiones
- Administrar su red de referidos

---

### 2.3 Actor: Vendedor
**Descripción**: Usuario que realiza ventas directas a clientes.

**Responsabilidades**:
- Crear y gestionar pedidos
- Administrar cartera de clientes
- Ver catálogo de productos
- Consultar sus comisiones
- Referir nuevos vendedores
- Gestionar su red de referidos

---

### 2.4 Actor: Cliente
**Descripción**: Usuario que realiza compras a través del sistema.

**Responsabilidades**:
- Realizar pedidos
- Ver historial de compras
- Consultar estado de pedidos
- Actualizar información personal
- Marcar productos como favoritos

---

### 2.5 Actor: Usuario Público
**Descripción**: Visitante sin autenticación que accede al catálogo público.

**Responsabilidades**:
- Ver catálogo de productos
- Consultar información de productos
- Registrarse en el sistema
- Solicitar información de contacto

---

## 3. DIAGRAMA GENERAL DE CASOS DE USO

```
┌─────────────────────────────────────────────────────────────────────┐
│                    SISTEMA RED DE VENTAS MLM                         │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │                  AUTENTICACIÓN                              │   │
│  │  • Iniciar Sesión                                          │   │
│  │  • Registrarse                                             │   │
│  │  • Recuperar Contraseña                                    │   │
│  │  • Cerrar Sesión                                           │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │              ADMINISTRACIÓN (Admin)                         │   │
│  │  • Gestionar Usuarios                                      │   │
│  │  • Gestionar Productos                                     │   │
│  │  • Gestionar Pedidos                                       │   │
│  │  • Gestionar Comisiones                                    │   │
│  │  • Ver Red de Referidos Global                            │   │
│  │  • Generar Reportes                                        │   │
│  │  • Configurar Sistema                                      │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │              GESTIÓN DE EQUIPO (Líder)                     │   │
│  │  • Ver Dashboard de Equipo                                 │   │
│  │  • Gestionar Equipo de Vendedores                         │   │
│  │  • Asignar Metas                                           │   │
│  │  • Capacitar Equipo                                        │   │
│  │  • Ver Rendimiento del Equipo                             │   │
│  │  • Gestionar Comisiones Propias                           │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │              GESTIÓN DE VENTAS (Vendedor)                  │   │
│  │  • Ver Dashboard Personal                                  │   │
│  │  • Crear Pedido                                            │   │
│  │  • Gestionar Clientes                                      │   │
│  │  • Ver Catálogo de Productos                              │   │
│  │  • Consultar Comisiones                                    │   │
│  │  • Referir Nuevos Vendedores                              │   │
│  │  • Gestionar Red de Referidos                             │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │              GESTIÓN DE COMPRAS (Cliente)                  │   │
│  │  • Ver Dashboard de Cliente                                │   │
│  │  • Realizar Pedido                                         │   │
│  │  • Ver Historial de Pedidos                               │   │
│  │  • Consultar Estado de Pedido                             │   │
│  │  • Gestionar Favoritos                                     │   │
│  │  • Actualizar Perfil                                       │   │
│  └────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────┐   │
│  │              CATÁLOGO PÚBLICO                               │   │
│  │  • Ver Catálogo de Productos                              │   │
│  │  • Ver Detalle de Producto                                 │   │
│  │  • Buscar Productos                                        │   │
│  │  • Filtrar por Categoría                                   │   │
│  └────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘

    👤              👤            👤           👤          👤
  Admin          Líder       Vendedor     Cliente     Público
```

---

## 4. CASOS DE USO - MÓDULO DE AUTENTICACIÓN

### CU-001: Iniciar Sesión

**Actor Principal**: Todos los usuarios registrados

**Descripción**: Permite a un usuario autenticarse en el sistema utilizando sus credenciales (email y contraseña).

**Precondiciones**:
- El usuario debe estar registrado en el sistema
- El usuario debe tener su cuenta activa
- El usuario debe tener acceso a internet

**Flujo Principal**:
1. El usuario accede a la página principal del sistema
2. El usuario hace clic en el botón "Iniciar Sesión"
3. El sistema muestra el formulario de login
4. El usuario ingresa su email y contraseña
5. El usuario hace clic en el botón "Ingresar"
6. El sistema valida las credenciales
7. El sistema verifica que la cuenta esté activa
8. El sistema crea una sesión para el usuario
9. El sistema registra el último acceso
10. El sistema redirige al usuario a su dashboard según su rol

**Flujos Alternativos**:

**FA1 - Credenciales Incorrectas** (Paso 6):
- 6a. El sistema detecta que las credenciales son incorrectas
- 6b. El sistema muestra un mensaje de error
- 6c. El sistema retorna al paso 3

**FA2 - Cuenta Inactiva** (Paso 7):
- 7a. El sistema detecta que la cuenta está inactiva
- 7b. El sistema muestra un mensaje indicando que la cuenta está desactivada
- 7c. El sistema sugiere contactar al administrador
- 7d. El caso de uso termina

**FA3 - Recordar Sesión**:
- El usuario marca la opción "Recordarme"
- El sistema extiende la duración de la sesión a 30 días

**Postcondiciones**:
- El usuario queda autenticado en el sistema
- Se crea una sesión activa
- Se registra la fecha y hora del último acceso
- El usuario visualiza su dashboard correspondiente según su rol

**Reglas de Negocio**:
- RN-001: Las contraseñas deben estar encriptadas con Bcrypt
- RN-002: Después de 5 intentos fallidos, la cuenta se bloquea temporalmente por 15 minutos
- RN-003: La sesión expira después de 2 horas de inactividad (sin "Recordarme")

---

### CU-002: Registrarse en el Sistema

**Actor Principal**: Usuario Público

**Descripción**: Permite a un usuario nuevo crear una cuenta en el sistema.

**Precondiciones**:
- El usuario debe tener acceso a internet
- El usuario debe tener un email válido
- El usuario debe contar con código de referido (opcional)

**Flujo Principal**:
1. El usuario accede a la página principal
2. El usuario hace clic en "Registrarse"
3. El sistema muestra el formulario de registro
4. El usuario ingresa sus datos personales:
   - Nombre
   - Apellidos
   - Cédula
   - Email
   - Contraseña
   - Confirmar contraseña
   - Teléfono
   - Dirección
   - Código de referido (opcional)
5. El usuario acepta los términos y condiciones
6. El usuario hace clic en "Crear Cuenta"
7. El sistema valida los datos ingresados
8. El sistema verifica que el email no esté registrado
9. El sistema verifica que la cédula no esté registrada
10. El sistema verifica el código de referido (si fue ingresado)
11. El sistema genera un código de referido único para el nuevo usuario
12. El sistema encripta la contraseña
13. El sistema crea el usuario con rol "cliente" por defecto
14. El sistema registra la relación de referido (si aplica)
15. El sistema envía un email de bienvenida
16. El sistema muestra un mensaje de éxito
17. El sistema inicia sesión automáticamente
18. El sistema redirige al dashboard del cliente

**Flujos Alternativos**:

**FA1 - Email ya Registrado** (Paso 8):
- 8a. El sistema detecta que el email ya existe
- 8b. El sistema muestra mensaje de error
- 8c. El sistema sugiere usar "Recuperar Contraseña"
- 8d. El sistema retorna al paso 3

**FA2 - Cédula ya Registrada** (Paso 9):
- 9a. El sistema detecta que la cédula ya existe
- 9b. El sistema muestra mensaje de error
- 9c. El sistema retorna al paso 3

**FA3 - Código de Referido Inválido** (Paso 10):
- 10a. El sistema detecta que el código no existe
- 10b. El sistema muestra mensaje de advertencia
- 10c. El sistema permite continuar sin código de referido
- 10d. El sistema continúa en paso 11

**FA4 - Validación de Datos Falla** (Paso 7):
- 7a. El sistema detecta datos inválidos o faltantes
- 7b. El sistema resalta los campos con error
- 7c. El sistema muestra mensajes específicos por campo
- 7d. El sistema retorna al paso 4

**Postcondiciones**:
- Se crea un nuevo usuario en la base de datos
- El usuario tiene rol "cliente" asignado
- Se genera un código de referido único
- Se registra la relación de referido (si aplica)
- El usuario recibe un email de bienvenida
- El usuario queda autenticado automáticamente
- Se actualiza el contador de referidos del referidor (si aplica)

**Reglas de Negocio**:
- RN-004: El email debe ser único en el sistema
- RN-005: La cédula debe ser única en el sistema
- RN-006: La contraseña debe tener mínimo 8 caracteres
- RN-007: El código de referido es opcional
- RN-008: El rol por defecto es "cliente"
- RN-009: Cada usuario recibe un código de referido único al registrarse

---

### CU-003: Recuperar Contraseña

**Actor Principal**: Usuario Registrado

**Descripción**: Permite a un usuario recuperar el acceso a su cuenta cuando ha olvidado su contraseña.

**Precondiciones**:
- El usuario debe estar registrado en el sistema
- El usuario debe tener acceso a su email registrado
- El usuario debe recordar su email de registro

**Flujo Principal**:
1. El usuario accede a la página de login
2. El usuario hace clic en "¿Olvidaste tu contraseña?"
3. El sistema muestra el formulario de recuperación
4. El usuario ingresa su email
5. El usuario hace clic en "Enviar enlace de recuperación"
6. El sistema valida que el email exista en la base de datos
7. El sistema genera un token único de recuperación
8. El sistema guarda el token en la tabla password_resets con timestamp
9. El sistema envía un email con el enlace de recuperación
10. El usuario recibe el email
11. El usuario hace clic en el enlace del email
12. El sistema valida que el token sea válido y no haya expirado
13. El sistema muestra el formulario para nueva contraseña
14. El usuario ingresa la nueva contraseña
15. El usuario confirma la nueva contraseña
16. El usuario hace clic en "Restablecer Contraseña"
17. El sistema valida que las contraseñas coincidan
18. El sistema encripta la nueva contraseña
19. El sistema actualiza la contraseña del usuario
20. El sistema elimina el token usado
21. El sistema muestra mensaje de éxito
22. El sistema redirige al login

**Flujos Alternativos**:

**FA1 - Email no Registrado** (Paso 6):
- 6a. El sistema no encuentra el email
- 6b. El sistema muestra mensaje genérico de seguridad
- 6c. El caso de uso termina

**FA2 - Token Expirado** (Paso 12):
- 12a. El sistema detecta que el token ha expirado (>60 minutos)
- 12b. El sistema muestra mensaje de token expirado
- 12c. El sistema sugiere solicitar un nuevo enlace
- 12d. El sistema redirige a paso 3

**FA3 - Token Inválido** (Paso 12):
- 12a. El sistema detecta que el token no existe
- 12b. El sistema muestra mensaje de error
- 12c. El caso de uso termina

**FA4 - Contraseñas no Coinciden** (Paso 17):
- 17a. El sistema detecta que las contraseñas no son iguales
- 17b. El sistema muestra mensaje de error
- 17c. El sistema retorna al paso 14

**Postcondiciones**:
- La contraseña del usuario es actualizada
- El token de recuperación es eliminado
- El usuario puede iniciar sesión con la nueva contraseña
- Se registra en auditoría el cambio de contraseña

**Reglas de Negocio**:
- RN-010: Los tokens de recuperación expiran en 60 minutos
- RN-011: Un token solo puede ser usado una vez
- RN-012: La nueva contraseña debe cumplir requisitos mínimos de seguridad
- RN-013: Por seguridad, el sistema no indica si el email existe o no

---

### CU-004: Cerrar Sesión

**Actor Principal**: Todos los usuarios autenticados

**Descripción**: Permite a un usuario cerrar su sesión actual en el sistema.

**Precondiciones**:
- El usuario debe estar autenticado
- Debe existir una sesión activa

**Flujo Principal**:
1. El usuario hace clic en su nombre de usuario en el menú
2. El sistema muestra un menú desplegable
3. El usuario selecciona "Cerrar Sesión"
4. El sistema invalida la sesión actual
5. El sistema elimina el token de sesión
6. El sistema registra el cierre de sesión
7. El sistema muestra mensaje de confirmación
8. El sistema redirige a la página de login

**Flujos Alternativos**: Ninguno

**Postcondiciones**:
- La sesión del usuario es eliminada
- El token de autenticación es invalidado
- El usuario ya no tiene acceso a las páginas protegidas
- Se registra la fecha y hora del cierre de sesión

**Reglas de Negocio**:
- RN-014: Al cerrar sesión, se eliminan todos los tokens de acceso

---

## 5. CASOS DE USO - MÓDULO DE ADMINISTRACIÓN

### CU-101: Gestionar Usuarios

**Actor Principal**: Administrador

**Descripción**: Permite al administrador crear, editar, visualizar y desactivar usuarios del sistema.

**Precondiciones**:
- El usuario debe estar autenticado como Administrador
- El sistema debe estar disponible

**Flujo Principal - Ver Lista de Usuarios**:
1. El administrador accede al menú "Administración"
2. El administrador selecciona "Usuarios"
3. El sistema muestra la lista de todos los usuarios
4. El sistema muestra para cada usuario:
   - Foto de perfil (si tiene)
   - Nombre completo
   - Email
   - Cédula
   - Rol (badge con color)
   - Estado (activo/inactivo)
   - Fecha de registro
   - Acciones disponibles
5. El sistema muestra filtros:
   - Por rol
   - Por estado
   - Búsqueda por nombre/email/cédula
6. El sistema muestra paginación (20 usuarios por página)

**Subflujo - Crear Usuario**:
1. El administrador hace clic en "Crear Usuario"
2. El sistema muestra el formulario de creación
3. El administrador ingresa los datos:
   - Nombre y apellidos
   - Cédula
   - Email
   - Contraseña
   - Teléfono
   - Dirección, ciudad, departamento
   - Rol (administrador, líder, vendedor, cliente)
   - Código de referido (opcional)
   - Zonas asignadas (si es vendedor)
   - Estado (activo/inactivo)
4. El administrador hace clic en "Guardar"
5. El sistema valida los datos
6. El sistema verifica que email y cédula sean únicos
7. El sistema genera código de referido único
8. El sistema encripta la contraseña
9. El sistema crea el usuario
10. El sistema envía email de bienvenida
11. El sistema muestra mensaje de éxito
12. El sistema redirige a la lista de usuarios

**Subflujo - Editar Usuario**:
1. El administrador hace clic en "Editar" de un usuario
2. El sistema muestra el formulario pre-llenado con datos actuales
3. El administrador modifica los campos deseados
4. El administrador hace clic en "Actualizar"
5. El sistema valida los cambios
6. El sistema guarda los cambios
7. El sistema registra la auditoría del cambio
8. El sistema muestra mensaje de éxito
9. El sistema redirige a la lista de usuarios

**Subflujo - Ver Detalle de Usuario**:
1. El administrador hace clic en "Ver" de un usuario
2. El sistema muestra el perfil completo del usuario:
   - Información personal completa
   - Estadísticas (ventas, comisiones, referidos)
   - Historial de pedidos
   - Red de referidos
   - Actividad reciente
3. El administrador puede realizar acciones:
   - Editar usuario
   - Cambiar contraseña
   - Activar/Desactivar
   - Ver comisiones
   - Ver pedidos

**Subflujo - Activar/Desactivar Usuario**:
1. El administrador hace clic en el toggle de estado
2. El sistema muestra confirmación
3. El administrador confirma la acción
4. El sistema cambia el estado del usuario
5. El sistema registra el cambio en auditoría
6. Si se desactiva:
   - El sistema cierra todas las sesiones activas del usuario
   - El sistema notifica al usuario por email
7. El sistema muestra mensaje de éxito
8. El sistema actualiza la lista

**Subflujo - Eliminar Usuario**:
1. El administrador hace clic en "Eliminar"
2. El sistema muestra advertencia y solicita confirmación
3. El administrador confirma ingresando su contraseña
4. El sistema verifica que el usuario no tenga:
   - Pedidos pendientes
   - Comisiones pendientes de pago
   - Equipo asignado (si es líder)
5. El sistema desactiva el usuario (soft delete)
6. El sistema registra en auditoría
7. El sistema muestra mensaje de éxito

**Flujos Alternativos**:

**FA1 - Email o Cédula Duplicados**:
- El sistema detecta duplicado
- El sistema muestra mensaje de error específico
- El sistema retorna al formulario

**FA2 - Usuario tiene Dependencias (Eliminar)**:
- El sistema detecta dependencias
- El sistema muestra mensaje explicativo
- El sistema sugiere desactivar en lugar de eliminar
- El caso de uso termina sin eliminar

**Postcondiciones**:
- Los cambios en usuarios se reflejan en la base de datos
- Se registra auditoría de todas las acciones
- Se envían notificaciones apropiadas
- Se actualizan contadores y estadísticas

**Reglas de Negocio**:
- RN-015: Solo administradores pueden gestionar usuarios
- RN-016: No se puede eliminar un usuario con pedidos pendientes
- RN-017: Al desactivar un usuario, se cierran sus sesiones activas
- RN-018: Email y cédula deben ser únicos en el sistema

---


### CU-102: Gestionar Productos

**Actor Principal**: Administrador

**Descripción**: Permite al administrador administrar el catálogo completo de productos.

**Precondiciones**:
- El usuario debe estar autenticado como Administrador
- Deben existir categorías creadas en el sistema

**Flujo Principal - Ver Catálogo**:
1. El administrador accede al menú "Productos"
2. El sistema muestra el catálogo de productos en formato grid
3. Para cada producto se muestra:
   - Imagen principal
   - Nombre
   - Categoría (badge)
   - Precio
   - Stock disponible
   - Indicador de disponibilidad (color)
   - Estado (activo/inactivo)
4. El sistema muestra filtros:
   - Por categoría
   - Por estado (activo/inactivo/todos)
   - Por disponibilidad de stock
   - Rango de precios
   - Búsqueda por nombre
5. El sistema muestra opciones de ordenamiento:
   - Por nombre (A-Z, Z-A)
   - Por precio (menor/mayor)
   - Por stock
   - Por fecha de creación

**Subflujo - Crear Producto**:
1. El administrador hace clic en "Crear Producto"
2. El sistema muestra formulario multi-sección
3. **Sección 1: Información Básica**
   - Administrador ingresa: nombre, descripción, categoría, precio
4. **Sección 2: Inventario**
   - Administrador ingresa: stock inicial, stock mínimo
5. **Sección 3: Imágenes**
   - Administrador sube imagen principal (drag & drop)
   - Administrador sube imágenes adicionales (opcional)
   - Sistema muestra preview en tiempo real
6. **Sección 4: Detalles**
   - Administrador ingresa: ingredientes, especificaciones, tiempo preparación
7. El administrador hace clic en "Publicar"
8. El sistema valida todos los datos
9. El sistema procesa y optimiza las imágenes
10. El sistema crea el producto
11. El sistema embebe los datos de la categoría
12. El sistema registra en auditoría
13. El sistema muestra mensaje de éxito
14. El sistema redirige al detalle del producto

**Subflujo - Editar Producto**:
1. El administrador selecciona un producto
2. El administrador hace clic en "Editar"
3. El sistema carga el formulario con datos actuales
4. El administrador modifica los campos deseados
5. Si cambia el precio:
   - Sistema registra cambio en historial_precios
   - Sistema solicita confirmar cambio
6. El administrador guarda los cambios
7. El sistema valida los datos
8. El sistema actualiza el producto
9. El sistema actualiza datos embebidos en pedidos relacionados (si aplica)
10. El sistema registra cambios en auditoría
11. El sistema notifica a vendedores sobre cambios importantes
12. El sistema muestra mensaje de éxito

**Subflujo - Ver Detalle de Producto**:
1. El administrador hace clic en un producto
2. El sistema muestra vista detallada:
   - **Panel Izquierdo**: Galería de imágenes con zoom
   - **Panel Derecho**: Información completa del producto
   - **Tabs Inferiores**:
     - Estadísticas de ventas
     - Historial de precios
     - Movimientos de inventario
     - Reviews (futuro)
3. El sistema muestra acciones disponibles:
   - Editar
   - Clonar producto
   - Activar/Desactivar
   - Ajustar stock
   - Ver pedidos con este producto

**Subflujo - Ajustar Stock**:
1. El administrador hace clic en "Ajustar Stock"
2. El sistema muestra modal de ajuste
3. El administrador selecciona tipo de movimiento:
   - Entrada (aumentar)
   - Salida (disminuir)
   - Ajuste (corrección)
4. El administrador ingresa:
   - Cantidad
   - Motivo del ajuste
   - Notas adicionales
5. El administrador confirma
6. El sistema valida la cantidad
7. El sistema actualiza el stock del producto
8. El sistema crea registro en MovimientoInventario
9. El sistema registra en auditoría
10. El sistema muestra mensaje de éxito

**Subflujo - Activar/Desactivar Producto**:
1. El administrador hace clic en toggle de estado
2. El sistema solicita confirmación
3. El administrador confirma
4. El sistema cambia el estado del producto
5. Si se desactiva:
   - Sistema verifica que no esté en pedidos pendientes
   - Sistema oculta del catálogo público
6. El sistema registra cambio en auditoría
7. El sistema muestra mensaje de éxito

**Flujos Alternativos**:

**FA1 - Validación de Datos Falla**:
- Sistema detecta datos inválidos
- Sistema resalta campos con error
- Sistema muestra mensajes específicos
- Retorna al formulario

**FA2 - Producto con Pedidos Pendientes (Desactivar)**:
- Sistema detecta pedidos pendientes
- Sistema muestra advertencia
- Sistema pregunta si desea continuar
- Si continúa: desactiva pero mantiene en pedidos existentes

**FA3 - Stock Insuficiente para Ajuste**:
- Sistema detecta que stock resultante sería negativo
- Sistema muestra error
- Retorna al modal de ajuste

**Postcondiciones**:
- Productos son actualizados en la base de datos
- Cambios se reflejan en el catálogo público
- Se actualizan estadísticas de inventario
- Se registran auditorías de cambios
- Se notifica a usuarios relevantes

**Reglas de Negocio**:
- RN-019: El precio debe ser mayor a 0
- RN-020: El stock no puede ser negativo
- RN-021: Un producto inactivo no aparece en catálogo público
- RN-022: Los cambios de precio se registran en historial
- RN-023: Las imágenes se optimizan automáticamente

---

### CU-103: Gestionar Pedidos

**Actor Principal**: Administrador

**Descripción**: Permite al administrador supervisar y gestionar todos los pedidos del sistema.

**Precondiciones**:
- El usuario debe estar autenticado como Administrador
- El sistema debe estar disponible

**Flujo Principal - Ver Lista de Pedidos**:
1. El administrador accede al menú "Pedidos"
2. El sistema muestra tabla con todos los pedidos
3. Para cada pedido se muestra:
   - Número de pedido
   - Cliente (nombre y email)
   - Vendedor (nombre con badge de rol)
   - Total (formato moneda)
   - Estado (badge con color)
   - Fecha de creación
   - Fecha entrega estimada
   - Acciones disponibles
4. El sistema muestra filtros avanzados:
   - Por estado (multi-select)
   - Por vendedor (autocomplete)
   - Por cliente (autocomplete)
   - Por rango de fechas
   - Por rango de montos
   - Por zona de entrega
5. El sistema muestra estadísticas en header:
   - Total pedidos del día
   - Total ventas del día
   - Pedidos pendientes
   - Pedidos en proceso

**Subflujo - Crear Pedido**:
1. El administrador hace clic en "Crear Pedido"
2. **Paso 1: Selección de Cliente**
   - Administrador busca cliente existente (autocomplete)
   - O crea nuevo cliente (modal)
   - Sistema muestra datos del cliente seleccionado
3. **Paso 2: Asignación de Vendedor**
   - Administrador busca vendedor (autocomplete)
   - O asigna automáticamente según zona
   - Sistema muestra datos del vendedor
4. **Paso 3: Selección de Productos**
   - Sistema muestra catálogo de productos disponibles
   - Administrador busca productos (filtros, búsqueda)
   - Administrador agrega productos al carrito
   - Para cada producto selecciona cantidad
   - Sistema muestra carrito lateral con:
     - Productos agregados
     - Cantidades (editables)
     - Precios unitarios
     - Subtotales
   - Sistema calcula automáticamente:
     - Subtotal general
     - IVA (si aplica)
     - Total
5. **Paso 4: Aplicar Descuentos** (opcional)
   - Administrador busca cupón válido
   - O aplica descuento manual
   - Sistema valida y aplica descuento
   - Sistema recalcula totales
6. **Paso 5: Datos de Entrega**
   - Administrador ingresa:
     - Dirección de entrega
     - Teléfono de contacto
     - Zona de entrega (select)
     - Fecha y hora estimada
     - Notas especiales
     - Método de pago
   - Sistema calcula precio de domicilio según zona
7. **Paso 6: Resumen y Confirmación**
   - Sistema muestra preview completo
   - Administrador revisa todos los datos
   - Administrador marca checkbox de confirmación
   - Administrador hace clic en "Crear Pedido"
8. El sistema genera número único de pedido
9. El sistema crea el pedido con estado "pendiente"
10. El sistema embebe datos de cliente, vendedor y productos
11. El sistema actualiza stock de productos (crea MovimientoInventario)
12. El sistema calcula comisiones (ComisionService)
13. El sistema crea registros de comisiones
14. El sistema envía notificaciones:
    - Al cliente (confirmación)
    - Al vendedor (nuevo pedido)
15. El sistema registra en auditoría
16. El sistema muestra mensaje de éxito
17. El sistema redirige al detalle del pedido creado

**Subflujo - Ver Detalle de Pedido**:
1. El administrador hace clic en un pedido
2. El sistema muestra vista detallada completa:
   - **Header**: Número, estado, fecha, acciones
   - **Timeline de Estados**: Línea de tiempo visual
   - **Datos del Cliente**: Info completa con enlace a perfil
   - **Datos del Vendedor**: Info completa con enlace a perfil
   - **Productos**: Tabla con imagen, nombre, cantidad, precios
   - **Totales**: Subtotal, descuentos, envío, total final
   - **Datos de Entrega**: Dirección, teléfono, zona, notas
   - **Comisiones**: Calculadas y estado
   - **Historial**: Todos los cambios del pedido
3. Sistema muestra acciones disponibles según estado

**Subflujo - Cambiar Estado de Pedido**:
1. El administrador hace clic en "Cambiar Estado"
2. El sistema muestra modal con estados disponibles:
   - Pendiente → Confirmado
   - Confirmado → En Preparación
   - En Preparación → Listo
   - Listo → En Camino
   - En Camino → Entregado
   - Cualquier estado → Cancelado
3. El administrador selecciona nuevo estado
4. El administrador ingresa notas (opcional)
5. El administrador confirma
6. El sistema valida la transición de estado
7. El sistema actualiza el estado del pedido
8. El sistema registra en historial_estados:
   - Estado anterior
   - Nuevo estado
   - Fecha y hora
   - Usuario que realizó el cambio
   - Notas
9. Si estado es "Entregado":
   - Sistema marca comisiones como "aprobadas"
   - Sistema actualiza estadísticas de vendedor
10. Si estado es "Cancelado":
    - Sistema devuelve stock (MovimientoInventario)
    - Sistema cancela comisiones pendientes
    - Sistema solicita motivo de cancelación
11. El sistema envía notificaciones:
    - Al cliente (cambio de estado)
    - Al vendedor (cambio de estado)
12. El sistema registra en auditoría
13. El sistema muestra mensaje de éxito
14. El sistema actualiza la vista del pedido

**Subflujo - Cancelar Pedido**:
1. El administrador hace clic en "Cancelar Pedido"
2. El sistema muestra advertencia
3. El sistema solicita motivo de cancelación (obligatorio)
4. El administrador ingresa el motivo
5. El administrador confirma cancelación
6. El sistema verifica que sea cancelable
7. El sistema cambia estado a "cancelado"
8. El sistema devuelve stock de productos
9. El sistema cancela comisiones asociadas
10. El sistema registra el motivo en historial
11. El sistema envía notificaciones
12. El sistema registra en auditoría
13. El sistema muestra mensaje de confirmación

**Flujos Alternativos**:

**FA1 - Stock Insuficiente al Crear**:
- Sistema detecta producto sin stock suficiente
- Sistema muestra alerta específica
- Sistema sugiere ajustar cantidad o remover producto
- Retorna al paso de selección de productos

**FA2 - Cupón Inválido**:
- Sistema detecta cupón inválido o vencido
- Sistema muestra mensaje de error
- Sistema permite continuar sin cupón
- Retorna al paso de aplicar descuentos

**FA3 - Pedido No Cancelable**:
- Sistema detecta que pedido ya fue entregado
- Sistema muestra mensaje de error
- Sistema sugiere crear nota de crédito
- Caso de uso termina

**FA4 - Transición de Estado Inválida**:
- Sistema detecta transición no permitida
- Sistema muestra estados válidos
- Retorna al modal de cambio de estado

**Postcondiciones**:
- Pedido es creado/actualizado en base de datos
- Stock de productos es actualizado
- Comisiones son calculadas y creadas
- Notificaciones son enviadas a los involucrados
- Auditoría registra todas las acciones
- Estadísticas son actualizadas

**Reglas de Negocio**:
- RN-024: Un pedido debe tener al menos un producto
- RN-025: El stock se descuenta al crear el pedido
- RN-026: Las comisiones se calculan al crear el pedido
- RN-027: Las comisiones se aprueban cuando el pedido es "Entregado"
- RN-028: Al cancelar un pedido, el stock se devuelve
- RN-029: Un pedido entregado no puede ser cancelado
- RN-030: Cada cambio de estado debe registrarse en historial
- RN-031: El número de pedido debe ser único (formato: PED-YYYYMMDD-###)

---

### CU-104: Gestionar Comisiones

**Actor Principal**: Administrador

**Descripción**: Permite al administrador supervisar, aprobar y gestionar el sistema de comisiones MLM.

**Precondiciones**:
- El usuario debe estar autenticado como Administrador
- Deben existir pedidos que generen comisiones

**Flujo Principal - Ver Dashboard de Comisiones**:
1. El administrador accede al menú "Comisiones"
2. El sistema muestra dashboard con:
   - **KPIs en Header**:
     - Total comisiones generadas (mes actual)
     - Total pendientes de pago
     - Total pagadas (mes actual)
     - Comisiones por aprobar
   - **Gráficos**:
     - Evolución de comisiones (línea temporal)
     - Comisiones por tipo (pie chart)
     - Top 10 vendedores (bar chart)
     - Distribución por niveles MLM
3. El sistema muestra tabla de comisiones:
   - Usuario beneficiario
   - Tipo de comisión
   - Monto
   - Estado
   - Pedido relacionado
   - Fecha de creación
   - Acciones
4. El sistema muestra filtros:
   - Por usuario (autocomplete)
   - Por tipo (venta_directa, referido_nivel_1, referido_nivel_2, bono)
   - Por estado (pendiente, aprobada, pagada)
   - Por rango de fechas
   - Por rango de montos

**Subflujo - Ver Detalle de Comisión**:
1. El administrador hace clic en una comisión
2. El sistema muestra vista detallada:
   - **Header**: Monto grande, estado, tipo
   - **Beneficiario**: Datos completos, total acumulado, saldo
   - **Detalles del Cálculo**:
     - Pedido relacionado (enlace)
     - Total del pedido
     - Porcentaje aplicado
     - Fórmula de cálculo
     - Configuración usada
   - **Información del Pedido**:
     - Número, cliente, vendedor, total, estado
   - **Historial y Trazabilidad**:
     - Fecha de creación
     - Fecha de aprobación (si aplica)
     - Usuario que aprobó
     - Fecha de pago (si aplica)
     - Usuario que registró pago
     - Método de pago
     - Número de transacción
3. Sistema muestra acciones disponibles según estado

**Subflujo - Aprobar Comisión**:
1. El administrador selecciona comisión(es) pendiente(s)
2. El administrador hace clic en "Aprobar"
3. El sistema muestra confirmación con resumen
4. El administrador confirma
5. El sistema verifica que las comisiones sean "pendientes"
6. El sistema cambia estado a "aprobada"
7. El sistema registra fecha y usuario que aprobó
8. El sistema actualiza saldo disponible del usuario
9. El sistema envía notificación al beneficiario
10. El sistema registra en auditoría
11. El sistema muestra mensaje de éxito

**Subflujo - Marcar como Pagada**:
1. El administrador selecciona comisión(es) aprobada(s)
2. El administrador hace clic en "Marcar como Pagada"
3. El sistema muestra modal de registro de pago
4. El administrador ingresa:
   - Método de pago (transferencia, efectivo, otro)
   - Número de referencia/transacción
   - Fecha de pago
   - Comprobante (archivo opcional)
   - Notas adicionales
5. El administrador confirma
6. El sistema valida los datos
7. El sistema cambia estado a "pagada"
8. El sistema registra todos los detalles del pago
9. El sistema actualiza saldos del usuario:
   - Disminuye saldo disponible
   - Aumenta total pagado histórico
10. El sistema envía notificación y comprobante al beneficiario
11. El sistema registra en auditoría
12. El sistema muestra mensaje de éxito

**Subflujo - Rechazar Comisión**:
1. El administrador selecciona comisión pendiente
2. El administrador hace clic en "Rechazar"
3. El sistema solicita motivo del rechazo (obligatorio)
4. El administrador ingresa motivo detallado
5. El administrador confirma
6. El sistema cambia estado a "rechazada"
7. El sistema registra motivo y usuario que rechazó
8. El sistema envía notificación al beneficiario con motivo
9. El sistema registra en auditoría
10. El sistema muestra mensaje de confirmación

**Subflujo - Calcular Comisiones Pendientes**:
1. El administrador hace clic en "Calcular Comisiones"
2. El sistema muestra modal de configuración
3. El administrador selecciona:
   - Calcular para pedidos específicos
   - Calcular para todos los pendientes
   - Recalcular con nuevas tasas
   - Simular cálculo (sin guardar)
4. El administrador configura porcentajes (si aplica)
5. El administrador hace clic en "Ejecutar"
6. El sistema valida configuración
7. El sistema busca pedidos sin comisiones calculadas
8. Para cada pedido:
   - Sistema calcula comisión de venta directa
   - Sistema busca referidores del vendedor
   - Sistema calcula comisiones de referidos
   - Sistema crea registros de comisiones
9. El sistema muestra barra de progreso
10. El sistema muestra resumen de comisiones creadas:
    - Total de comisiones generadas
    - Desglose por usuario
    - Desglose por tipo
11. El sistema registra en auditoría
12. El sistema muestra mensaje de éxito

**Subflujo - Exportar Reporte**:
1. El administrador aplica filtros deseados
2. El administrador hace clic en "Exportar"
3. El sistema muestra opciones:
   - Formato (Excel, PDF, CSV)
   - Incluir gráficos (solo PDF)
   - Rango de datos
4. El administrador selecciona y confirma
5. El sistema genera el archivo
6. El sistema descarga el archivo
7. El sistema registra la exportación en auditoría

**Flujos Alternativos**:

**FA1 - Comisión Ya Procesada**:
- Sistema detecta que comisión ya fue aprobada/pagada
- Sistema muestra mensaje de advertencia
- Caso de uso termina

**FA2 - Datos de Pago Incompletos**:
- Sistema detecta campos obligatorios vacíos
- Sistema resalta campos faltantes
- Retorna al modal de registro de pago

**FA3 - No Hay Comisiones Pendientes de Calcular**:
- Sistema no encuentra pedidos sin comisiones
- Sistema muestra mensaje informativo
- Caso de uso termina

**Postcondiciones**:
- Estados de comisiones son actualizados
- Saldos de usuarios son actualizados
- Notificaciones son enviadas
- Auditoría registra todas las acciones
- Estadísticas son actualizadas

**Reglas de Negocio**:
- RN-032: Solo comisiones "pendientes" pueden ser aprobadas
- RN-033: Solo comisiones "aprobadas" pueden ser pagadas
- RN-034: Una comisión pagada no puede modificarse
- RN-035: El rechazo de comisión requiere motivo obligatorio
- RN-036: Comisión de venta directa: 15% por defecto
- RN-037: Comisión referido nivel 1: 5% por defecto
- RN-038: Comisión referido nivel 2: 2% por defecto
- RN-039: Las comisiones se calculan sobre el total_final del pedido

---


## 6. CASOS DE USO - MÓDULO DE LÍDER

### CU-201: Ver Dashboard de Equipo

**Actor Principal**: Líder

**Descripción**: Permite al líder visualizar métricas y rendimiento de su equipo de vendedores.

**Precondiciones**:
- El usuario debe estar autenticado como Líder
- El líder debe tener vendedores asignados en su equipo

**Flujo Principal**:
1. El líder inicia sesión en el sistema
2. El sistema redirige automáticamente al dashboard del líder
3. El sistema calcula y muestra KPIs del equipo:
   - Total de vendedores en el equipo
   - Ventas totales del equipo (mes actual)
   - Comisiones generadas por el equipo
   - Meta del mes y porcentaje de cumplimiento
   - Comparación con mes anterior (%)
4. El sistema muestra gráficos de rendimiento:
   - Evolución de ventas del equipo (línea temporal - últimos 6 meses)
   - Ventas por vendedor (gráfico de barras horizontal)
   - Cumplimiento de metas (gauge charts por vendedor)
   - Tendencia de crecimiento
5. El sistema muestra sección de "Top Performers":
   - 5 mejores vendedores del mes
   - Para cada uno: foto, nombre, ventas, comisiones, pedidos
   - Badge de reconocimiento (oro, plata, bronce)
6. El sistema muestra panel de alertas:
   - Vendedores sin ventas en el mes
   - Metas en riesgo de no cumplirse
   - Nuevos referidos incorporados al equipo
   - Comisiones pendientes de aprobación
7. El sistema muestra actividad reciente del equipo:
   - Últimas 10 ventas realizadas por el equipo
   - Nuevos miembros incorporados
   - Cambios de estado importantes
8. El sistema muestra accesos rápidos:
   - Botón "Ver Equipo Completo"
   - Botón "Asignar Metas"
   - Botón "Gestionar Capacitaciones"
   - Botón "Ver Comisiones"
   - Botón "Generar Reportes"
9. El sistema actualiza automáticamente cada 5 minutos

**Flujos Alternativos**:

**FA1 - Equipo sin Vendedores**:
- Sistema detecta que no hay vendedores asignados
- Sistema muestra mensaje informativo
- Sistema sugiere contactar administrador
- Sistema muestra tutorial de referidos

**FA2 - Sin Ventas en el Mes**:
- Sistema detecta que no hay ventas este mes
- Sistema muestra mensaje motivacional
- Sistema sugiere acciones para impulsar ventas

**Postcondiciones**:
- El líder visualiza el estado actual de su equipo
- El líder identifica áreas de oportunidad
- El líder puede tomar decisiones basadas en datos

**Reglas de Negocio**:
- RN-040: Solo se muestran vendedores activos
- RN-041: Las métricas se calculan en base al mes actual
- RN-042: El dashboard se actualiza automáticamente cada 5 minutos

---

### CU-202: Gestionar Equipo de Vendedores

**Actor Principal**: Líder

**Descripción**: Permite al líder ver y gestionar a los vendedores de su equipo.

**Precondiciones**:
- El usuario debe estar autenticado como Líder
- El líder debe tener vendedores en su equipo

**Flujo Principal - Ver Lista del Equipo**:
1. El líder accede al menú "Mi Equipo"
2. El sistema muestra lista de todos los vendedores del equipo
3. Para cada vendedor se muestra:
   - Avatar y nombre completo
   - Email y teléfono
   - Fecha de ingreso al equipo
   - Ventas del mes actual
   - Comisiones ganadas este mes
   - Meta asignada y % de progreso
   - Estado (activo/inactivo)
   - Nivel en la red MLM
   - Acciones disponibles
4. El sistema muestra filtros:
   - Por estado (activo/inactivo)
   - Por cumplimiento de meta (alto/medio/bajo)
   - Por ventas (alto/medio/bajo)
   - Por zona asignada
5. El sistema muestra opciones de visualización:
   - Vista de tarjetas (grid)
   - Vista de tabla (detallada)
   - Vista de organigrama (jerárquica)

**Subflujo - Ver Perfil de Vendedor**:
1. El líder hace clic en "Ver Perfil" de un vendedor
2. El sistema muestra información completa:
   - **Datos Personales**: Contacto, fecha ingreso, referido por
   - **Estadísticas de Rendimiento**:
     - Ventas totales (histórico)
     - Ventas del mes
     - Promedio mensual
     - Total comisiones ganadas
     - Pedidos realizados
     - Ticket promedio
   - **Gráficos de Rendimiento**:
     - Evolución de ventas (6 meses)
     - Cumplimiento de metas (histórico)
     - Comisiones por mes
   - **Historial de Ventas**: Tabla con todos los pedidos
   - **Red de Referidos**: Personas que ha referido
   - **Metas Asignadas**: Actual e histórico
   - **Capacitaciones**: Completadas y pendientes
3. El sistema muestra acciones disponibles:
   - Asignar nueva meta
   - Enviar mensaje
   - Asignar capacitación
   - Exportar rendimiento
   - Ver pedidos completos

**Subflujo - Asignar Meta a Vendedor**:
1. El líder hace clic en "Asignar Meta"
2. El sistema muestra modal de asignación de meta
3. El sistema muestra sugerencia basada en:
   - Promedio histórico del vendedor
   - Desempeño del mes anterior
   - Meta general del equipo
4. El líder ingresa:
   - Monto de la meta
   - Período (mes/trimestre)
   - Fecha de inicio
   - Fecha de fin
   - Notas motivacionales (opcional)
5. El líder confirma la asignación
6. El sistema valida que la fecha sea futura
7. El sistema crea la meta para el vendedor
8. El sistema envía notificación al vendedor
9. El sistema registra en historial de metas
10. El sistema muestra mensaje de éxito

**Subflujo - Enviar Mensaje al Equipo**:
1. El líder hace clic en "Enviar Mensaje"
2. El sistema muestra modal de mensaje
3. El líder selecciona destinatarios:
   - Todo el equipo
   - Vendedores específicos
   - Vendedores con bajo rendimiento
   - Top performers
4. El líder ingresa:
   - Asunto del mensaje
   - Contenido del mensaje
   - Prioridad (normal/importante)
5. El líder hace clic en "Enviar"
6. El sistema valida el mensaje
7. El sistema envía notificación a los seleccionados
8. El sistema guarda en mensajes del líder
9. El sistema muestra confirmación de envío

**Subflujo - Exportar Historial de Vendedor**:
1. El líder hace clic en "Exportar Historial"
2. El sistema muestra opciones de exportación
3. El líder selecciona:
   - Formato (Excel, PDF)
   - Período de datos
   - Información a incluir (ventas, comisiones, metas)
4. El líder confirma
5. El sistema genera el archivo
6. El sistema descarga el archivo
7. El sistema registra la exportación

**Flujos Alternativos**:

**FA1 - Meta Inválida**:
- Sistema detecta fecha pasada o monto negativo
- Sistema muestra mensaje de error
- Retorna al modal de asignación

**FA2 - Vendedor sin Historial**:
- Sistema detecta que vendedor es nuevo
- Sistema adapta sugerencia de meta
- Sistema usa promedios del equipo

**Postcondiciones**:
- El líder visualiza el estado de su equipo
- Metas son asignadas a vendedores
- Mensajes son enviados y recibidos
- Reportes son exportados
- Notificaciones son enviadas a vendedores

**Reglas de Negocio**:
- RN-043: Un líder solo ve vendedores de su equipo
- RN-044: Las metas deben ser para períodos futuros
- RN-045: Los vendedores reciben notificación de nuevas metas
- RN-046: El historial de ventas es de solo lectura

---

### CU-203: Gestionar Capacitaciones

**Actor Principal**: Líder

**Descripción**: Permite al líder crear y asignar capacitaciones a su equipo.

**Precondiciones**:
- El usuario debe estar autenticado como Líder
- El líder debe tener vendedores en su equipo

**Flujo Principal - Ver Capacitaciones**:
1. El líder accede al menú "Capacitación"
2. El sistema muestra lista de capacitaciones creadas
3. Para cada capacitación se muestra:
   - Título
   - Descripción breve
   - Duración estimada
   - Vendedores asignados
   - Progreso general (%)
   - Fecha de creación
   - Estado (activa/completada/archivada)
4. El sistema muestra filtros:
   - Por estado
   - Por progreso
   - Por fecha

**Subflujo - Crear Capacitación**:
1. El líder hace clic en "Crear Capacitación"
2. El sistema muestra formulario de creación
3. El líder ingresa:
   - Título de la capacitación
   - Descripción detallada
   - Contenido (editor de texto enriquecido)
   - Duración estimada (en horas)
   - Recursos/archivos adjuntos (PDFs, videos, links)
   - Evaluación (opcional):
     - Preguntas de evaluación
     - Puntaje mínimo para aprobar
4. El líder hace clic en "Guardar"
5. El sistema valida los datos
6. El sistema procesa y almacena archivos adjuntos
7. El sistema crea la capacitación
8. El sistema muestra mensaje de éxito
9. El sistema redirige a vista de capacitación creada

**Subflujo - Asignar Capacitación**:
1. El líder selecciona una capacitación
2. El líder hace clic en "Asignar a Equipo"
3. El sistema muestra modal de asignación
4. El líder selecciona vendedores:
   - Todo el equipo
   - Vendedores específicos (checkbox multiple)
   - Vendedores nuevos (últimos 30 días)
   - Vendedores con bajo rendimiento
5. El líder establece:
   - Fecha límite de compleción (opcional)
   - Prioridad (baja/media/alta)
   - Obligatoria (sí/no)
6. El líder confirma
7. El sistema asigna la capacitación a los vendedores seleccionados
8. El sistema envía notificaciones a los asignados
9. El sistema registra la asignación
10. El sistema muestra confirmación

**Subflujo - Seguimiento de Progreso**:
1. El líder hace clic en una capacitación
2. El sistema muestra vista de seguimiento:
   - Progreso general (gauge chart)
   - Lista de vendedores asignados con:
     - Nombre y foto
     - Progreso individual (%)
     - Estado (no iniciado/en progreso/completado)
     - Tiempo invertido
     - Fecha de inicio
     - Fecha de compleción (si aplica)
     - Calificación (si tiene evaluación)
3. El sistema muestra gráfico de progreso temporal
4. El sistema muestra estadísticas:
   - Total asignados
   - Completados
   - En progreso
   - Sin iniciar
   - Tiempo promedio de compleción
5. El líder puede:
   - Enviar recordatorio a los que no han iniciado
   - Ver evaluaciones completadas
   - Exportar reporte de progreso

**Flujos Alternativos**:

**FA1 - Sin Contenido**:
- Sistema detecta que no hay contenido ingresado
- Sistema muestra mensaje de error
- Retorna al formulario

**FA2 - Archivo Muy Grande**:
- Sistema detecta archivo > 50MB
- Sistema muestra mensaje de límite
- Sistema sugiere usar enlaces externos
- Retorna al formulario

**Postcondiciones**:
- Capacitación es creada y almacenada
- Vendedores reciben notificaciones de asignación
- Progreso es rastreado automáticamente
- Líder puede hacer seguimiento del progreso

**Reglas de Negocio**:
- RN-047: Los archivos adjuntos no pueden superar 50MB cada uno
- RN-048: Las capacitaciones obligatorias aparecen destacadas
- RN-049: El progreso se calcula automáticamente
- RN-050: Los vendedores reciben recordatorios automáticos

---

## 7. CASOS DE USO - MÓDULO DE VENDEDOR

### CU-301: Crear Pedido como Vendedor

**Actor Principal**: Vendedor

**Descripción**: Permite al vendedor crear un nuevo pedido para un cliente.

**Precondiciones**:
- El usuario debe estar autenticado como Vendedor
- Deben existir productos disponibles en el catálogo
- El vendedor debe tener zonas asignadas

**Flujo Principal**:
1. El vendedor accede al menú "Pedidos"
2. El vendedor hace clic en "Crear Pedido"
3. **Paso 1: Seleccionar Cliente**
   - Sistema muestra buscador de clientes
   - Vendedor busca cliente por nombre, email o cédula
   - Sistema muestra resultados en tiempo real (autocomplete)
   - Vendedor selecciona cliente de la lista
   - Sistema carga datos del cliente:
     - Nombre completo
     - Email y teléfono
     - Dirección predeterminada
     - Historial de compras (resumen)
   - **O** Vendedor crea cliente nuevo:
     - Hace clic en "Crear Cliente"
     - Sistema muestra modal de registro rápido
     - Vendedor ingresa datos mínimos:
       - Nombre, apellidos, cédula
       - Email, teléfono
       - Dirección
     - Sistema valida y crea cliente
     - Sistema selecciona automáticamente el nuevo cliente
4. Vendedor hace clic en "Continuar"
5. **Paso 2: Seleccionar Productos**
   - Sistema muestra catálogo de productos activos
   - Sistema muestra buscador y filtros:
     - Búsqueda por nombre
     - Filtro por categoría
     - Ordenar por (nombre, precio, stock)
   - Para cada producto se muestra:
     - Imagen
     - Nombre
     - Precio
     - Stock disponible
     - Indicador de disponibilidad
   - Vendedor busca y selecciona productos:
     - Hace clic en "Agregar"
     - Ingresa cantidad deseada
     - Producto se agrega al carrito lateral
   - Sistema muestra carrito lateral con:
     - Productos agregados
     - Cantidades (editables con +/-)
     - Precio unitario
     - Subtotal por producto
     - Botón eliminar por producto
   - Sistema calcula automáticamente:
     - Subtotal general
     - IVA (0% por defecto, configurable)
     - Total
6. Vendedor hace clic en "Continuar"
7. **Paso 3: Aplicar Descuentos** (opcional)
   - Sistema muestra opciones de descuento:
     - Buscar cupón por código
     - Aplicar descuento manual (si tiene permiso)
   - Vendedor ingresa código de cupón (si tiene)
   - Sistema valida el cupón:
     - Existe y está activo
     - No ha expirado
     - No ha alcanzado límite de usos
     - Aplica a los productos seleccionados
     - Cumple monto mínimo
   - Sistema aplica el descuento
   - Sistema recalcula totales:
     - Muestra descuento aplicado en rojo
     - Muestra ahorro del cliente
     - Muestra total final
   - Vendedor hace clic en "Continuar"
8. **Paso 4: Datos de Entrega**
   - Sistema muestra formulario:
     - Dirección de entrega (pre-llenada con dirección del cliente)
     - Teléfono de contacto (pre-llenado)
     - Zona de entrega (select)
     - Fecha entrega estimada (date picker, mínimo mañana)
     - Hora entrega estimada (time picker)
     - Notas especiales (textarea opcional)
     - Método de pago (select: efectivo, transferencia, otro)
   - Sistema calcula y muestra precio de domicilio según zona
   - Sistema recalcula total final incluyendo domicilio
   - Vendedor ingresa/verifica los datos
   - Vendedor hace clic en "Continuar"
9. **Paso 5: Confirmar Pedido**
   - Sistema muestra resumen completo:
     - Datos del cliente
     - Lista de productos con cantidades y precios
     - Subtotal
     - Descuentos aplicados
     - IVA
     - Costo de domicilio
     - **Total Final** (grande, destacado)
     - Datos de entrega
     - Método de pago
   - Vendedor revisa todos los datos
   - Vendedor marca checkbox "He verificado los datos"
   - Vendedor hace clic en "Confirmar y Crear Pedido"
10. Sistema genera número único de pedido
11. Sistema crea el pedido con estado "pendiente"
12. Sistema embebe datos de cliente y vendedor
13. Sistema embebe productos con sus datos actuales
14. Sistema actualiza stock de cada producto:
    - Disminuye cantidad del stock
    - Crea MovimientoInventario tipo "salida"
15. Sistema calcula comisiones:
    - Comisión del vendedor (venta directa: 15%)
    - Busca si el vendedor tiene referidor
    - Calcula comisión de referidor nivel 1 (5%)
    - Busca si el referidor tiene referidor
    - Calcula comisión de referidor nivel 2 (2%)
16. Sistema crea registros de comisiones con estado "pendiente"
17. Sistema envía notificaciones:
    - Al cliente: confirmación de pedido con detalles
    - Al vendedor: confirmación de creación
    - Al líder (si tiene): notificación de nueva venta del equipo
18. Sistema registra en auditoría
19. Sistema muestra mensaje de éxito con número de pedido
20. Sistema redirige a vista de detalle del pedido creado

**Flujos Alternativos**:

**FA1 - Cliente No Encontrado**:
- Vendedor no encuentra el cliente en búsqueda
- Vendedor crea cliente nuevo
- Sistema valida datos
- Sistema crea cliente
- Continúa en paso 4

**FA2 - Producto sin Stock Suficiente**:
- Vendedor agrega producto con cantidad > stock
- Sistema muestra alerta
- Sistema sugiere cantidad máxima disponible
- Vendedor ajusta cantidad o remueve producto
- Continúa en paso 2

**FA3 - Cupón Inválido**:
- Vendedor ingresa código de cupón
- Sistema detecta que es inválido/vencido
- Sistema muestra mensaje específico
- Sistema permite continuar sin cupón
- Continúa en paso 3

**FA4 - Carrito Vacío**:
- Vendedor intenta continuar sin productos
- Sistema muestra alerta
- Sistema retorna a selección de productos

**FA5 - Confirmación sin Verificar**:
- Vendedor intenta confirmar sin marcar checkbox
- Sistema muestra alerta
- Sistema retorna al resumen

**Postcondiciones**:
- Pedido es creado en la base de datos
- Stock de productos es actualizado
- Movimientos de inventario son registrados
- Comisiones son calculadas y creadas
- Notificaciones son enviadas
- Auditoría registra la creación
- Estadísticas del vendedor son actualizadas

### CU-304: Gestionar Clientes

**Actor Principal**: Vendedor

**Descripción**: Permite al vendedor administrar su cartera de clientes, crear nuevos clientes, ver historial de compras y mantener seguimiento.

**Precondiciones**:
- El usuario debe estar autenticado como Vendedor
- El sistema debe estar disponible

**Pasos**:
1. El vendedor accede al menú "Mis Clientes"
2. El sistema muestra lista de todos los clientes del vendedor con su información básica y estadísticas de compras
3. El vendedor puede filtrar por actividad, total de compras, fecha de última compra o buscar por nombre/email/cédula
4. Para crear un cliente nuevo, el vendedor hace clic en "Crear Cliente"
5. El vendedor ingresa los datos del cliente: nombre, apellidos, cédula, email, teléfono, dirección completa
6. El vendedor selecciona si desea enviar credenciales por email al cliente
7. El sistema valida que el email y cédula sean únicos
8. El sistema genera contraseña temporal aleatoria
9. El sistema crea el cliente y lo asigna al vendedor
10. El sistema envía email con credenciales si fue seleccionado
11. El sistema muestra mensaje de éxito y redirige al perfil del cliente creado

**Precondición**:
- El vendedor debe tener sesión activa
- El vendedor debe tener permisos para gestionar clientes

**Postcondición**:
- El cliente es creado en la base de datos
- El cliente queda asignado al vendedor
- Se envían notificaciones por email (si aplica)
- El vendedor puede comenzar a crear pedidos para este cliente

**Reglas de Negocio**:
- RN-078: Un cliente debe estar asignado a un vendedor
- RN-079: Email y cédula deben ser únicos en el sistema
- RN-080: Un vendedor solo puede ver sus propios clientes
- RN-081: Las notas del vendedor sobre clientes son privadas

---

### CU-305: Ver Catálogo de Productos para Vendedor

**Actor Principal**: Vendedor

**Descripción**: Permite al vendedor consultar el catálogo completo de productos disponibles para vender con información detallada.

**Precondiciones**:
- El usuario debe estar autenticado como Vendedor
- Deben existir productos activos en el sistema

**Pasos**:
1. El vendedor accede al menú "Productos" o "Catálogo"
2. El sistema muestra catálogo de productos activos en vista de grid
3. Para cada producto se muestra: imagen, nombre, categoría, precio, stock disponible con indicador visual
4. El vendedor puede aplicar filtros por categoría, rango de precios, disponibilidad
5. El vendedor puede usar la barra de búsqueda por nombre o código
6. El vendedor puede ordenar por nombre, precio, stock o más vendidos
7. El vendedor hace clic en un producto para ver detalle completo
8. El sistema muestra vista detallada: galería de imágenes, información completa, especificaciones, ingredientes, stock exacto
9. El sistema muestra tabs con: estadísticas de ventas, historial de precios, productos relacionados
10. El vendedor puede agregar el producto a un pedido rápido o compartirlo por WhatsApp

**Precondición**:
- El vendedor debe estar autenticado
- Debe haber productos activos con stock

**Postcondición**:
- El vendedor visualiza el catálogo actualizado
- Puede agregar productos a pedidos
- Enlaces compartidos incluyen su código de referido

**Reglas de Negocio**:
- RN-085: Solo se muestran productos activos con stock > 0
- RN-086: Los precios son iguales para todos los vendedores
- RN-087: El vendedor no puede modificar productos
- RN-088: Enlaces compartidos incluyen código de referido del vendedor

---

### CU-306: Ver Dashboard Personal del Vendedor

**Actor Principal**: Vendedor

**Descripción**: Permite al vendedor visualizar su panel de control personal con métricas de rendimiento y accesos rápidos.

**Precondiciones**:
- El usuario debe estar autenticado como Vendedor

**Pasos**:
1. El vendedor inicia sesión en el sistema
2. El sistema verifica credenciales y rol
3. El sistema redirige automáticamente al dashboard del vendedor
4. El sistema calcula y muestra KPIs: ventas del día, ventas del mes, meta mensual y progreso, comisiones ganadas
5. El sistema muestra gráficos de rendimiento: evolución de ventas (7 días), cumplimiento de meta, comisiones por tipo
6. El sistema muestra panel de accesos rápidos: Crear Pedido, Ver Clientes, Ver Catálogo, Ver Comisiones, Mi Red
7. El sistema muestra últimos 5 pedidos con detalles básicos y enlace a ver completo
8. El sistema muestra panel de notificaciones con alertas importantes
9. El sistema muestra resumen de red de referidos: total, activos, comisiones generadas, código para compartir
10. El sistema actualiza automáticamente cada 5 minutos

**Precondición**:
- El vendedor debe tener sesión activa
- El sistema debe tener datos de ventas disponibles

**Postcondición**:
- El vendedor visualiza su rendimiento actual
- Dashboard actualizado con datos en tiempo real
- El vendedor puede navegar a funciones específicas

**Reglas de Negocio**:
- RN-091: El dashboard se actualiza cada 5 minutos
- RN-092: Solo se muestran datos del vendedor autenticado
- RN-093: Las metas se calculan para el mes en curso
- RN-094: El ranking se calcula diariamente

---

## CASOS DE USO DEL CLIENTE

### CU-403: Ver Historial de Pedidos

**Actor Principal**: Cliente

**Descripción**: Permite al cliente consultar todos sus pedidos históricos con filtros y opciones de búsqueda.

**Precondiciones**:
- El usuario debe estar autenticado como Cliente
- El cliente debe tener pedidos realizados

**Pasos**:
1. El cliente accede al menú "Mis Pedidos" o "Historial"
2. El sistema muestra lista completa de pedidos con: número, fecha, productos (resumen), total, estado, vendedor
3. El cliente puede filtrar por estado (todos, entregados, en proceso, cancelados)
4. El cliente puede filtrar por fecha (último mes, 3 meses, 6 meses, año, rango personalizado)
5. El cliente puede filtrar por rango de montos
6. El cliente puede ordenar por: más recientes, más antiguos, mayor monto, menor monto
7. El sistema muestra estadísticas: total de pedidos, total gastado, pedido promedio, frecuencia de compra
8. El cliente hace clic en un pedido para ver detalle completo
9. El sistema muestra: número, estado, timeline completo, productos con imágenes, totales, datos de entrega, vendedor
10. El cliente puede descargar factura (si entregado), repetir pedido, contactar vendedor o reportar problema

**Precondición**:
- El cliente debe estar autenticado
- Debe tener al menos un pedido realizado

**Postcondición**:
- El cliente visualiza su historial completo
- Puede descargar facturas
- Puede repetir pedidos fácilmente

**Reglas de Negocio**:
- RN-097: Solo se muestran pedidos del cliente autenticado
- RN-098: Facturas solo para pedidos "entregados"
- RN-099: Al repetir pedido se usan precios actuales
- RN-100: Historial ordenado por fecha descendente por defecto

---

### CU-404: Gestionar Productos Favoritos

**Actor Principal**: Cliente

**Descripción**: Permite al cliente marcar productos como favoritos y gestionar su lista para futuras compras.

**Precondiciones**:
- El usuario debe estar autenticado como Cliente
- Deben existir productos en el sistema

**Pasos**:
1. El cliente accede al menú "Mis Favoritos"
2. El sistema muestra lista de productos favoritos: imagen, nombre, categoría, precio, disponibilidad, fecha agregado
3. El cliente puede ver en vista grid o lista
4. El cliente puede filtrar por categoría, disponibilidad o rango de precios
5. El cliente puede ordenar por: recién agregados, nombre, precio
6. Para agregar un producto a favoritos, el cliente hace clic en el ícono de corazón (vacío) desde el catálogo
7. El sistema agrega el producto a favoritos y cambia el ícono a corazón lleno (rojo)
8. El sistema muestra notificación de confirmación
9. Para quitar de favoritos, el cliente hace clic en el corazón lleno
10. El sistema remueve el producto y actualiza la lista
11. El cliente puede agregar favoritos al carrito individualmente o todos a la vez
12. El sistema verifica disponibilidad y agrega productos disponibles al carrito

**Precondición**:
- El cliente debe estar autenticado
- Debe haber productos en el catálogo

**Postcondición**:
- Productos agregados/removidos de favoritos
- Lista actualizada correctamente
- Notificaciones de cambios configuradas
- Carrito incluye productos seleccionados

**Reglas de Negocio**:
- RN-102: Cada cliente tiene su propia lista de favoritos
- RN-103: Los favoritos persisten entre sesiones
- RN-104: Se notifica cuando precio de favorito baja >10%
- RN-105: Se notifica cuando favorito agotado vuelve a tener stock
- RN-107: Un producto solo puede estar una vez en favoritos
- RN-108: Agregar a favoritos requiere autenticación

---




