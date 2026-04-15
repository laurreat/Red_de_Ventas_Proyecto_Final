# Red de Ventas MLM - Arepa Llanerita
## Migración de Laravel a Next.js

---

## RESUMEN DEL PROYECTO

Sistema MLM (Multi-Level Marketing) para gestión de red de ventas de "Arepa la Llanerita" con:

### Roles de Usuario
1. **Administrador** - Panel de control completo
2. **Líder** - Gestión de equipo y ventas
3. **Vendedor** - Registro de ventas y seguimiento
4. **Cliente** - Compras y referidos

### Funcionalidades Principales
- Sistema de referidos multinivel con códigos únicos (REF####)
- Cálculo automático de comisiones
- Gestión de pedidos con múltiples estados
- Catálogo de productos con categorías
- Notificaciones en tiempo real (Livewire)
- Sistema de backup/restore de base de datos
- Reportes y estadísticas

---

## ESTRUCTURA DE DATOS (MongoDB)

### Collections:

```
users
├── _id (ObjectId)
├── name (string)
├── apellidos (string)
├── cedula (string, unique)
├── email (string, unique)
├── password (string, hashed)
├── telefono (string)
├── direccion (string)
├── ciudad (string)
├── departamento (string)
├── fecha_nacimiento (date)
├── rol (string: administrador|lider|vendedor|cliente)
├── codigo_referido (string, unique: REF####)
├── referidor_id (ObjectId, optional)
├── total_referidos (int)
├── comisiones_disponibles (Decimal128)
├── avatar (string, optional)
├── email_verified_at (datetime)
├── remember_token (string)
├── created_at (datetime)
└── updated_at (datetime)

pedidos
├── _id (ObjectId)
├── numero_pedido (string, unique)
├── cliente_id (ObjectId)
├── vendedor_id (ObjectId)
├── cliente_data (embedded)
├── vendedor_data (embedded)
├── productos (array of embedded)
│   ├── producto_id (ObjectId)
│   ├── nombre (string)
│   ├── cantidad (int)
│   ├── precio_unitario (Decimal128)
│   └── subtotal (Decimal128)
├── subtotal (Decimal128)
├── descuento (Decimal128)
├── total_final (Decimal128)
├── estado (string: pendiente|confirmado|en_preparacion|listo|en_camino|entregado|cancelado)
├── notas (string, optional)
├── created_at (datetime)
└── updated_at (datetime)

productos
├── _id (ObjectId)
├── nombre (string)
├── descripcion (string)
├── precio (Decimal128)
├── categoria_id (ObjectId)
├── stock (int)
├── imagen (string)
├── activo (boolean)
├── created_at (datetime)
└── updated_at (datetime)

categorias
├── _id (ObjectId)
├── nombre (string)
├── descripcion (string)
└── created_at (datetime)

comisiones
├── _id (ObjectId)
├── user_id (ObjectId)
├── pedido_id (ObjectId)
├── monto (Decimal128)
├── tipo (string: venta|referido|nivel)
├── nivel (int, optional)
├── estado (string: pendiente|pagada|cancelada)
├── created_at (datetime)
└── updated_at (datetime)

referidos
├── _id (ObjectId)
├── referidor_id (ObjectId)
├── referido_id (ObjectId)
├── nivel (int)
├── created_at (datetime)
└── updated_at (datetime)

roles
├── _id (ObjectId)
├── nombre (string)
├── permisos (array)
├── activo (boolean)
└── created_at (datetime)

notificaciones
├── _id (ObjectId)
├── user_id (ObjectId)
├── titulo (string)
├── mensaje (string)
├── tipo (string)
├── leida (boolean)
├── created_at (datetime)
└── updated_at (datetime)

configuraciones
├── _id (ObjectId)
├── clave (string)
├── valor (mixed)
└── updated_at (datetime)
```

---

## RUTAS DEL SISTEMA

### Rutas Públicas
```
GET  /                    → Welcome page
GET  /login              → Login
POST /login              → Authenticate
GET  /register           → Register
POST /register           → Create account
```

### Rutas Admin (middleware: auth, role:administrador)
```
/admin/dashboard         → Dashboard admin
/admin/users             → Gestión de usuarios
/admin/roles             → Gestión de roles
/admin/productos         → Gestión de productos
/admin/pedidos           → Gestión de pedidos
/admin/comisiones        → Comisiones
/admin/reportes          → Reportes
/admin/referidos         → Red de referidos
/admin/configuracion     → Configuración
/admin/logs              → Logs del sistema
/admin/notificaciones     → Notificaciones
/admin/perfil            → Mi perfil
```

### Rutas Líder (middleware: auth, role:lider,administrador)
```
/lider/dashboard         → Dashboard líder
/lider/equipo            → Mi equipo
/lider/referidos         → Mis referidos
/lider/ventas            → Ventas del equipo
/lider/comisiones        → Comisiones
/lider/metas             → Metas
/lider/reportes          → Reportes
/lider/capacitacion      → Capacitaciones
/lider/configuracion     → Configuración
/lider/perfil            → Mi perfil
```

### Rutas Vendedor (middleware: auth, role:vendedor,lider,administrador)
```
/vendedor/dashboard      → Dashboard vendedor
/vendedor/pedidos       → Mis pedidos
/vendedor/productos      → Productos
/vendedor/clientes       → Mis clientes
/vendedor/comisiones     → Comisiones
/vendedor/referidos      → Mis referidos
/vendedor/perfil        → Mi perfil
```

### Rutas Cliente (middleware: auth, role:cliente)
```
/cliente/dashboard       → Dashboard cliente
/cliente/perfil         → Mi perfil
/cliente/pedidos        → Mis pedidos
/cliente/referidos      → Mis referidos
/cliente/ayuda          → Ayuda
```

### API Routes
```
GET  /api/admin/dashboard
GET  /api/admin/users
GET  /api/admin/products
GET  /api/admin/orders
GET  /api/admin/commissions
GET  /api/admin/referrals
GET  /api/user          (auth:sanctum)
```

---

## AUTENTICACIÓN

### Login
- Validación de credenciales contra MongoDB
- Protección contra NoSQL injection
- Rate limiting (throttle)
- Redirección según rol del usuario

### Registro
- Campos: name, apellidos, cedula, email, password, telefono, direccion, ciudad, departamento, fecha_nacimiento, codigo_referido_usado
- Generación automática de código de referido (REF####)
- Notificación a admin de nuevo usuario
- Asignación de rol por defecto: cliente

---

## CÁLCULO DE COMISIONES

```php
// Servicio: ComisionService.php
- Comisión por venta directa: 10%
- Comisión por referido nivel 1: 5%
- Comisión por referido nivel 2: 3%
- Comisión por referido nivel 3: 1%
```

---

## ESTADOS DE PEDIDO

```
pendiente → confirmado → en_preparacion → listo → en_camino → entregado
                                              ↓
                                          cancelado
```

---

## PRÓXIMOS PASOS PARA MIGRACIÓN

### 1. Configurar Next.js
```bash
npx create-next-app@latest red-ventas-next
# Elegir: TypeScript, Tailwind, App Router, src/app
```

### 2. Instalar dependencias
```bash
npm install mongoose @auth/mongodb-adapter next-auth
npm install axios react-hook-form zod
npm install recharts lucide-react
npm install @tanstack/react-query
```

### 3. Estructura de carpetas sugerida
```
src/
├── app/
│   ├── (auth)/
│   │   ├── login/
│   │   └── register/
│   ├── (dashboard)/
│   │   ├── admin/
│   │   ├── lider/
│   │   ├── vendedor/
│   │   └── cliente/
│   ├── api/
│   │   └── [...nextauth]/
│   ├── layout.tsx
│   └── page.tsx
├── components/
│   ├── ui/
│   ├── layouts/
│   └── forms/
├── lib/
│   ├── mongodb.ts
│   ├── auth.ts
│   └── api/
├── models/
│   └── (Mongoose models)
└── types/
    └── index.ts
```

### 4. Configurar MongoDB
```typescript
// lib/mongodb.ts
import mongoose from 'mongoose';

const MONGODB_URI = process.env.MONGODB_URI!;

if (!MONGODB_URI) throw new Error('Please define MONGODB_URI');

let cached = (global as any).mongoose;

if (!cached) {
  cached = (global as any).mongoose = { conn: null, promise: null };
}

export async function connectDB() {
  if (cached.conn) return cached.conn;
  
  if (!cached.promise) {
    cached.promise = mongoose.connect(MONGODB_URI).then((mongoose) => {
      return mongoose;
    });
  }
  
  cached.conn = await cached.promise;
  return cached.conn;
}
```

### 5. Configurar NextAuth
```typescript
// lib/auth.ts
import { NextAuthOptions } from 'next-auth';
import CredentialsProvider from 'next-auth/providers/credentials';
import { connectDB } from './mongodb';
import User from '@/models/User';

export const authOptions: NextAuthOptions = {
  providers: [
    CredentialsProvider({
      name: 'credentials',
      credentials: {
        email: { label: "Email", type: "email" },
        password: { label: "Password", type: "password" }
      },
      async authorize(credentials) {
        // Validación y login
      }
    })
  ],
  session: { strategy: "jwt" },
  callbacks: {
    async jwt({ token, user }) {
      if (user) token.role = user.rol;
      return token;
    },
    async session({ session, token }) {
      if (session.user) session.user.rol = token.role;
      return session;
    }
  }
};
```

---

## META DE DISEÑO UI/UX

### Colores (mantener los existentes)
- Primary: #722F37 (vino)
- Secondary: #ffffff
- Success: #28a745
- Warning: #ffc107
- Danger: #dc3545

### Tipografía
- Inter (Google Fonts)
- Usado en todo el proyecto

### Componentes a migrar
- Dashboard cards con estadísticas
- Tablas de datos con paginación
- Modales de confirmación
- Sistema de toasts/notificaciones
- Sidebar con navegación
- Headers con dropdowns

---

## CONSIDERACIONES DE SEGURIDAD

1. **CSRF** - Next.js maneja esto automáticamente con _next/csrf
2. **XSS** - Usar sanitización en formularios
3. **Rate Limiting** - Implementar en API routes
4. **Validación** - Usar Zod para validación de schemas
5. **Auth** - NextAuth con JWT y refresh tokens
