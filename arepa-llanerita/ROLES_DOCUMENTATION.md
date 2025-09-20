# 📋 Documentación del Módulo de Roles y Permisos

## 🎯 Funcionalidad de los Botones

### 🔑 **Ver Permisos**

- **Ubicación**: Botón azul en la parte superior
- **Función**: Muestra todos los permisos disponibles en el sistema
- **Qué hace**:
  - Lista los 32 permisos organizados por categorías
  - Muestra estadísticas de permisos
  - Diferencia entre permisos administrativos y generales
  - Sirve como referencia para saber qué permisos existen

### 🔄 **Inicializar Roles**

- **Ubicación**: Botón amarillo en la parte superior
- **Función**: Crea/actualiza los 4 roles predeterminados del sistema
- **Qué hace**:
  - Crea los roles: administrador, líder, vendedor, cliente
  - Asigna automáticamente los permisos correctos a cada rol
  - Útil para restaurar la configuración inicial
  - Solicita confirmación antes de ejecutar

### ➕ **Nuevo Rol**

- **Ubicación**: Botón verde en la parte superior
- **Función**: Permite crear un rol personalizado
- **Qué hace**:
  - Abre formulario para crear rol personalizado
  - Permite seleccionar permisos específicos
  - Se pueden crear roles con combinaciones únicas de permisos
  - Los roles personalizados se pueden editar y eliminar

## 📊 **Información de la Tabla**

### Columnas explicadas

- **Nombre**: Identificador único del rol (técnico)
- **Nombre Display**: Nombre amigable que se muestra al usuario
- **Descripción**: Explicación de qué hace el rol
- **Tipo**:
  - 🟡 **Sistema**: Roles predefinidos (no se pueden eliminar)
  - 🔵 **Personalizado**: Roles creados por el admin (editables)
- **Permisos**: Cantidad de permisos asignados al rol
- **Usuarios**: Cuántos usuarios tienen este rol asignado
- **Estado**: Si el rol está activo o inactivo

## 🎮 **Acciones por Rol**

### 👁️ **Ver** (Botón azul)

- Muestra todos los detalles del rol
- Lista los permisos específicos asignados
- Muestra usuarios que tienen el rol
- Permite navegar a edición o gestión de usuarios

### ✏️ **Editar** (Botón amarillo)

- Modifica nombre, descripción y permisos
- Los roles del sistema solo permiten editar permisos
- Se pueden agregar/quitar permisos individualmente

### 🔄 **Activar/Desactivar** (Botón gris/verde)

- Activa o desactiva el rol
- Los roles inactivos no otorgan permisos
- Útil para suspender temporalmente un rol

### 🗑️ **Eliminar** (Botón rojo)

- Solo disponible para roles personalizados
- No se puede eliminar si tiene usuarios asignados
- Los roles del sistema están protegidos

## 🔐 **Jerarquía de Roles Predeterminados**

1. **👑 Administrador**: Acceso completo al sistema
2. **🎯 Líder**: Gestión de equipos y reportes
3. **💼 Vendedor**: Gestión de ventas y clientes
4. **👤 Cliente**: Acceso básico al catálogo

## 🚀 **Flujo de Trabajo Recomendado**

1. **Inicializar Roles** (primera vez)
2. **Ver Permisos** (para conocer opciones)
3. **Crear Roles Personalizados** según necesidades
4. **Asignar Usuarios** a los roles apropiados
5. **Activar/Desactivar** según sea necesario

## ⚠️ **Notas Importantes**

- Los cambios en permisos afectan inmediatamente a los usuarios
- Los roles del sistema se pueden personalizar pero no eliminar
- Siempre debe haber al menos un usuario administrador activo
- La funcionalidad de permisos es granular y específica por módulo
