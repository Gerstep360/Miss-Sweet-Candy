# Cafetería Miss Sweet Candy - Sistema de Gestión

Sistema web para administrar la operación diaria de **Miss Sweet Candy**: pedidos en mesa y mostrador, cobros en caja, productos, mesas, usuarios, roles y reportes.  
Construido con **Laravel 12**, **Livewire**, **Blade**, **TailwindCSS** y **Alpine.js**.

> Objetivo: digitalizar el flujo de atención y reducir errores, ofreciendo una experiencia rápida y moderna para el personal y los clientes.

---

## Tabla de Contenidos

- [Características Principales](#características-principales)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Requisitos](#requisitos)
- [Instalación y Configuración](#instalación-y-configuración)
- [Estructura de Carpetas](#estructura-de-carpetas)
- [Módulos del Sistema](#módulos-del-sistema)
- [Casos de Uso Implementados (MVP)](#casos-de-uso-implementados-mvp)
- [Seeders y Datos de Prueba](#seeders-y-datos-de-prueba)
- [Personalización y Temas](#personalización-y-temas)
- [Despliegue](#despliegue)
- [Licencia](#licencia)
- [Contacto](#contacto)

---

## Características Principales

- **Gestión de usuarios** con roles y permisos avanzados (Administrador, Cajero, Barista, Cliente).
- **Pedidos en mesa y para llevar (mostrador)** con selector de productos, cantidades y notas.
- **Gestión de productos y categorías** con imágenes, precios y unidades.
- **Gestión de mesas** (libre, ocupada, reservada, fusionada) y asignación de pedidos.
- **Cobros en caja** (efectivo/POS) para pedidos de mesa y mostrador, con comprobantes internos.
- **Panel de control** con KPIs, reportes y acciones rápidas.
- **Horarios de atención** configurables y estado público “abierto/cerrado”.
- **Menú público** para clientes, con filtro por categorías y buscador.
- **Autenticación** con registro, activación por email y recuperación de contraseña.
- **Interfaz moderna y responsiva** con TailwindCSS y componentes personalizados.
- **Permisos granulares** con Spatie Laravel Permission.

---

## Tecnologías Utilizadas

- **Backend:** Laravel 12 (PHP 8.4), Eloquent ORM
- **Frontend:** Blade, Livewire, Alpine.js, TailwindCSS
- **Base de datos:** MySQL/MariaDB
- **Autenticación:** Laravel Breeze (Livewire)
- **Permisos y roles:** Spatie Laravel Permission
- **Build tools:** Vite

---

## Requisitos

- PHP **8.4+**
- Composer **2+**
- MySQL/MariaDB
- Node **18+** o **20+** y NPM
- Extensiones PHP: `mbstring`, `openssl`, `pdo`, etc.

---

## Instalación y Configuración

### 1) Clonar el repositorio
```bash
git clone https://github.com/Gerstep360/Miss-Sweet-Candy.git
cd Miss-sweet-candy
```

### 2) Instalar dependencias
```bash
composer install
npm install
```

### 3) Variables de entorno
```bash
cp .env.example .env
```
Edita `.env` con tu configuración (DB, correo, URL).

### 4) Generar clave
```bash
php artisan key:generate
```

### 5) Migraciones + seeders
```bash
php artisan migrate --seed
php artisan storage:link
```

### 6) Compilar assets
```bash
npm run dev      # desarrollo
# o
npm run build    # producción
```

### 7) Iniciar servidor
```bash
php artisan serve
```
Navega a http://localhost:8000

---

## Estructura de Carpetas

- `app/Http/Controllers/` — Controladores por módulo
- `app/Models/` — Modelos Eloquent
- `database/migrations/` — Migraciones
- `database/seeders/` — Seeders (usuarios, roles, productos, mesas, etc.)
- `resources/views/` — Vistas Blade (admin, cajero, público, componentes)
- `resources/js/` — Scripts (Alpine/Livewire, modales, helpers)
- `resources/css/` — Tailwind y estilos
- `routes/web.php` — Rutas web
- `public/` — Assets públicos

---

## Módulos del Sistema

### Autenticación y Roles
- Registro, login, activación por correo, recuperación de contraseña.
- Roles: **Administrador**, **Cajero**, **Barista**, **Cliente**.
- Permisos granulares (Spatie Permission).

### Gestión de Usuarios
- Listado, búsqueda, filtros por rol.
- Crear, editar, eliminar usuarios y asignar roles.

### Gestión de Roles y Permisos
- CRUD de roles; asignación/sincronización de permisos.
- Vista de usuarios por rol.

### Gestión de Productos y Categorías
- CRUD de productos y categorías.
- Imágenes, precios, unidades y visibilidad pública.
- Filtros por categoría.

### Gestión de Mesas
- CRUD de mesas, capacidad y estados (libre, ocupada, reservada, fusionada).
- Asignación/traslado de pedidos entre mesas.

### Pedidos (Mesa y Mostrador)
- Crear/editar/anular pedidos.
- Envío a barra/cocina; estados: pendiente, enviado, servido, anulado/retirado.
- Selector de productos con buscador, categorías y cantidades.

### Cobros en Caja
- Lista de pedidos pendientes de cobro (mesa y mostrador).
- Registro de pagos (efectivo/POS).
- Comprobante interno y arqueo básico.

### Horarios
- Configuración de apertura/cierre por día.
- Estado público “abierto/cerrado”.

### Menú Público
- Menú sin login con filtros/buscador, imágenes y precios.

---

## Casos de Uso Implementados (MVP)

| Código | Nombre | Descripción técnica | Justificación |
|-------:|--------|---------------------|---------------|
| **CU01** | Iniciar sesión / Registrar cuenta | Autenticación por email/contraseña con hash; alta de clientes vía registro y e-mail de activación. | Acceso seguro y permisos por rol. |
| **CU02** | Cerrar sesión | Revoca sesión/refresh token y limpia contexto. | Evita accesos no autorizados en equipos compartidos. |
| **CU03** | Gestión de Usuarios | CRUD, búsqueda, activación/desactivación y reasignación de roles. | Mantiene padrón actualizado y trazable. |
| **CU04** | Gestión de Roles y Permisos | Definición de roles (admin, cajero, barista, cliente) y permisos granulares. | Separa responsabilidades y reduce errores. |
| **CU05** | Gestión de Categorías | Alta/edición/baja de categorías (café, sándwiches, repostería). | Ordena el menú y facilita filtros. |
| **CU06** | Gestión de Productos / Menú | CRUD con precio, unidad, descripción, imagen y visibilidad pública. | Mantiene el catálogo al día (POS y menú público). |
| **CU07** | Gestión de Horarios | Configura horarios y cambia automáticamente el estado “Abierto/Cerrado”. | Evita pedidos fuera de horario y mejora UX. |
| **CU08** | Gestión de Mesas | Alta, fusión y cambio de estado (libre/ocupada/reservada). | Planifica ocupación y asigna pedidos con claridad. |
| **CU09** | Tomar Pedido en Mesa | Registra/modifica/anula pedidos vinculados a mesa; envía ítems a barra/cocina. | Acelera servicio y reduce errores de transcripción. |
| **CU10** | Pedido Para Llevar / Mostrador | Crea órdenes “take-away”; imprime ticket de cocina y confirma retiro. | Atiende clientes de paso sin ocupar mesas. |
| **CU11** | Cobro en Caja | Registra pago en efectivo/POS, valida importes y emite comprobante interno. | Consolida ingresos y agiliza atención. |
| **CU27** | Consulta de Menú Público | Muestra menú actualizado con filtros y buscador sin login. | Facilita elección previa y apoya ventas. |

> **Nota:** Otros casos planificados (promociones, inventario avanzado, fidelidad, etc.) se encuentran en el backlog para siguientes ciclos.

---

## Seeders y Datos de Prueba

Incluye seeders para poblar:
- Roles y permisos (Spatie).
- Usuarios ejemplo (admin, cajero, barista, clientes).
- Categorías y productos.
- Mesas y horarios.
- Pedidos de muestra.

Ejecuta:
```bash
php artisan migrate --seed
```
> Revisa `database/seeders/` para credenciales demo y ajustes de roles/permisos.

---

## Personalización y Temas

- TailwindCSS para estilos utilitarios; override en `resources/css/app.css`.
- Layouts en `resources/views/components/layouts/`.
- Puedes adaptar colores, tipografías y componentes sin tocar la lógica.

---

## Despliegue

1. Configura variables `.env` de producción (DB, correo, storage).
2. Ejecuta migraciones:
   ```bash
   php artisan migrate --force
   ```
3. Compila assets:
   ```bash
   npm run build
   ```
4. Optimiza la app:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Servidor web apuntando a `public/` (Nginx/Apache).
6. Permisos de escritura para `storage/` y `bootstrap/cache/`.

---

## Licencia

Este proyecto es de uso interno para **Miss Sweet Candy**. Para uso comercial o redistribución, contactar al autor.

---

## Contacto

- **Desarrollador:** Germán — *gersoft.official@gmail.com*
- **Repositorio:** https://github.com/Gerstep360/Miss-Sweet-Candy
- **Soporte:** abre un *issue* o escribe por correo.

---

¡Gracias por usar el sistema de gestión de **Miss Sweet Candy**!
