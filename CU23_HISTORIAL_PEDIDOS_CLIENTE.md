# CU23: Historial de Pedidos del Cliente

## Descripción
Caso de uso que permite a los clientes consultar su historial completo de pedidos, ver detalles, estadísticas, reordenar pedidos anteriores y exportar su historial.

---

## Implementación

### Controller Creado
**Archivo:** `app/Http/Controllers/HistorialPedidosClienteController.php`

### Rutas Creadas
**Archivo:** `routes/cliente.php`

---

## Funcionalidades Implementadas

### 1. Listado de Historial de Pedidos
**Ruta:** `GET /cliente/historial`  
**Método:** `index(Request $request)`  
**Permiso:** `ver-mis-pedidos`

**Características:**
- Lista todos los pedidos del cliente autenticado
- Filtros disponibles:
  - Rango de fechas (fecha_desde, fecha_hasta)
  - Tipo de pedido (mesa, mostrador, web)
  - Estado del pedido
  - Búsqueda por número de pedido o productos
- Paginación de 15 pedidos por página
- Muestra estadísticas generales del cliente
- Ordenado por fecha descendente (más recientes primero)

**Ejemplo de uso:**
```
/cliente/historial
/cliente/historial?fecha_desde=2025-01-01&fecha_hasta=2025-01-31
/cliente/historial?tipo=web&estado=entregado
/cliente/historial?buscar=café
```

---

### 2. Detalle de Pedido
**Ruta:** `GET /cliente/historial/{id}`  
**Método:** `show($id)`  
**Permiso:** `ver-mis-pedidos`

**Características:**
- Muestra información completa del pedido
- Incluye:
  - Items del pedido con productos y cantidades
  - Mesa asignada (si aplica)
  - Usuario que atendió
  - Cobros realizados
  - Feedback del cliente
  - Promociones aplicadas
- Validación de propiedad (solo el cliente dueño puede ver el pedido)

---

### 3. Estadísticas del Cliente
**Ruta:** `GET /cliente/historial/estadisticas/resumen`  
**Método:** `estadisticas()`  
**Permiso:** `ver-mis-pedidos`

**Características:**
- **Estadísticas generales:**
  - Total de pedidos realizados
  - Total gastado
  - Promedio de gasto por pedido
  - Pedidos por tipo (mesa, mostrador, web)
  - Pedidos por estado
  - Último pedido realizado
  - Producto favorito

- **Productos más pedidos:**
  - Top 10 productos más solicitados
  - Cantidad total pedida
  - Número de veces pedido

- **Pedidos por mes:**
  - Últimos 12 meses
  - Total de pedidos por mes
  - Total gastado por mes

- **Gasto por categoría:**
  - Distribución de gasto por categorías de productos
  - Total gastado por categoría
  - Total de pedidos por categoría

---

### 4. Reordenar Pedido Anterior
**Ruta:** `GET /cliente/historial/{id}/reordenar`  
**Método:** `reordenar($id)`  
**Permiso:** `crear-pedidos-web`

**Características:**
- Permite crear un nuevo pedido basado en uno anterior
- Verifica disponibilidad de productos:
  - Productos disponibles (activos y con stock)
  - Productos no disponibles (inactivos o sin stock)
- Muestra cantidad original y stock disponible
- Permite ajustar cantidades antes de confirmar
- Validación de propiedad del pedido original

---

### 5. Procesar Reorden
**Ruta:** `POST /cliente/historial/{id}/reordenar`  
**Método:** `storeReorden(Request $request, $id)`  
**Permiso:** `crear-pedidos-web`

**Características:**
- Crea un nuevo pedido con los productos seleccionados
- Validaciones:
  - Productos existen y están activos
  - Stock suficiente para cada producto
  - Datos de entrega válidos (si aplica)
- Aplica precios vigentes (incluye especiales del día)
- Descuenta del inventario automáticamente
- Calcula descuentos de promociones
- Registra en bitácora el pedido original

**Datos requeridos:**
```php
[
    'productos' => [
        ['producto_id' => 1, 'cantidad' => 2, 'notas' => 'Sin azúcar'],
        ['producto_id' => 5, 'cantidad' => 1, 'notas' => null],
    ],
    'tipo' => 'web',
    'modalidad' => 'retiro', // o 'entrega'
    'direccion_entrega' => 'Calle 123', // requerido si modalidad=entrega
    'telefono_contacto' => '555-1234',
    'notas' => 'Pedido urgente',
]
```

---

### 6. Búsqueda en Historial
**Ruta:** `GET /cliente/historial/buscar/pedidos`  
**Método:** `buscar(Request $request)`  
**Permiso:** `ver-mis-pedidos`

**Características:**
- Búsqueda por:
  - Número de pedido
  - Nombre de productos
  - Notas del pedido
- Resultados paginados
- Ordenado por fecha descendente

**Ejemplo:**
```
/cliente/historial/buscar/pedidos?termino=café
/cliente/historial/buscar/pedidos?termino=123
```

---

### 7. Exportar Historial
**Ruta:** `GET /cliente/historial/exportar/reporte`  
**Método:** `exportar(Request $request)`  
**Permiso:** `ver-mis-pedidos`

**Características:**
- Formatos disponibles: PDF, Excel, CSV
- Filtros opcionales por rango de fechas
- Incluye todos los pedidos del cliente
- Ordenado por fecha descendente

**Ejemplo:**
```
/cliente/historial/exportar/reporte?formato=pdf
/cliente/historial/exportar/reporte?formato=excel&fecha_desde=2025-01-01
```

---

## Permisos Requeridos

### ver-mis-pedidos
Permite al cliente:
- Ver su historial de pedidos
- Ver detalles de sus pedidos
- Ver estadísticas personales
- Buscar en su historial
- Exportar su historial

### crear-pedidos-web
Permite al cliente:
- Reordenar pedidos anteriores
- Crear nuevos pedidos web

---

## Seguridad Implementada

### Triple Capa de Seguridad
1. **Middleware auth**: Usuario debe estar autenticado
2. **Middleware can**: Verificación de permisos específicos
3. **Validación en controller**: Verificación de propiedad de recursos

### Validaciones de Propiedad
Todas las operaciones verifican que:
- El pedido pertenece al cliente autenticado
- Solo el dueño puede ver/reordenar sus pedidos
- Retorna error 403 si no tiene permiso

### Ejemplo de validación:
```php
if ($pedido->cliente_id !== Auth::id()) {
    abort(403, 'No tienes permiso para ver este pedido.');
}
```

---

## Auditoría (Bitácora)

Todas las operaciones se registran en bitácora:

| Acción | Descripción |
|--------|-------------|
| `consulta_historial` | Cliente consulta su historial |
| `consulta_detalle_pedido` | Cliente ve detalle de un pedido |
| `consulta_estadisticas` | Cliente consulta sus estadísticas |
| `busqueda_historial` | Cliente busca en su historial |
| `exportacion_historial` | Cliente exporta su historial |
| `reordenar_pedido` | Cliente accede a reordenar |
| `pedido_reordenado` | Cliente completa un reorden |

---

## Estadísticas Calculadas

### Método privado: `calcularEstadisticas($clienteId)`

Retorna array con:
```php
[
    'total_pedidos' => 45,
    'total_gastado' => 1250.50,
    'promedio_gasto' => 27.79,
    'pedidos_por_tipo' => [
        'mesa' => 20,
        'mostrador' => 15,
        'web' => 10,
    ],
    'pedidos_por_estado' => [
        'entregado' => 40,
        'pendiente' => 3,
        'anulado' => 2,
    ],
    'ultimo_pedido' => Pedido {...},
    'producto_favorito' => (object)[
        'nombre' => 'Café Americano',
        'total' => 35
    ],
]
```

---

## Integración con Otros Módulos

### Inventario
- Al reordenar, descuenta automáticamente del inventario
- Verifica stock disponible antes de crear pedido

### Promociones
- Muestra promociones aplicadas en detalle de pedido
- Aplica promociones vigentes al reordenar

### Especiales del Día
- Aplica precios especiales al reordenar
- Muestra descuentos en detalle de pedido

### Feedback
- Muestra feedback del cliente en detalle de pedido
- Permite ver valoraciones anteriores

---

## Flujos de Uso

### Flujo 1: Consultar Historial
```
Cliente → Login → /cliente/historial
→ Aplica filtros (opcional)
→ Ve lista de pedidos
→ Click en pedido → Ver detalle
```

### Flujo 2: Reordenar Pedido
```
Cliente → Historial → Selecciona pedido
→ Click "Reordenar"
→ Sistema verifica disponibilidad
→ Cliente ajusta cantidades
→ Confirma pedido
→ Sistema crea nuevo pedido
→ Descuenta inventario
→ Redirección a detalle del nuevo pedido
```

### Flujo 3: Ver Estadísticas
```
Cliente → Historial → Estadísticas
→ Ve resumen general
→ Ve productos favoritos
→ Ve gráficos de consumo
→ Ve gasto por categoría
```

### Flujo 4: Exportar Historial
```
Cliente → Historial → Exportar
→ Selecciona formato (PDF/Excel/CSV)
→ Aplica filtros de fecha (opcional)
→ Sistema genera reporte
→ Descarga archivo
```

---

## Vistas Requeridas (Pendientes de Crear)

1. `resources/views/cliente/historial/index.blade.php`
   - Lista de pedidos con filtros
   - Tarjetas de estadísticas

2. `resources/views/cliente/historial/show.blade.php`
   - Detalle completo del pedido
   - Items, totales, promociones

3. `resources/views/cliente/historial/estadisticas.blade.php`
   - Dashboard de estadísticas
   - Gráficos y tablas

4. `resources/views/cliente/historial/reordenar.blade.php`
   - Formulario de reorden
   - Lista de productos disponibles/no disponibles

5. `resources/views/cliente/historial/buscar.blade.php`
   - Resultados de búsqueda
   - Filtros adicionales

6. `resources/views/cliente/historial/exportar.blade.php`
   - Vista previa de exportación
   - Opciones de formato

---

## Mejoras Futuras

### Funcionalidades Adicionales
- [ ] Notificaciones push cuando cambia estado de pedido
- [ ] Compartir pedido favorito con otros usuarios
- [ ] Programar pedidos recurrentes
- [ ] Sistema de favoritos/guardados
- [ ] Comparar precios históricos
- [ ] Alertas de ofertas en productos favoritos
- [ ] Integración con programa de lealtad/puntos

### Optimizaciones
- [ ] Cache de estadísticas (actualizar cada hora)
- [ ] Índices en base de datos para búsquedas
- [ ] Lazy loading de imágenes de productos
- [ ] Paginación infinita (scroll)

### Reportes
- [ ] Exportación real a PDF con gráficos
- [ ] Exportación a Excel con múltiples hojas
- [ ] Envío de reporte por email
- [ ] Programar reportes automáticos mensuales

---

## Ejemplo de Uso en Código

### Obtener historial del cliente autenticado
```php
$pedidos = Pedido::where('cliente_id', Auth::id())
    ->with(['items.producto', 'mesa'])
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

### Calcular total gastado
```php
$totalGastado = Pedido::where('cliente_id', Auth::id())
    ->whereNotIn('estado', ['anulado', 'cancelado'])
    ->sum('total');
```

### Obtener producto favorito
```php
$productoFavorito = DB::table('pedido_items')
    ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
    ->join('productos', 'pedido_items.producto_id', '=', 'productos.id')
    ->where('pedidos.cliente_id', Auth::id())
    ->select('productos.nombre', DB::raw('SUM(pedido_items.cantidad) as total'))
    ->groupBy('productos.id', 'productos.nombre')
    ->orderBy('total', 'desc')
    ->first();
```

---

## Testing Recomendado

### Tests Unitarios
- Cálculo de estadísticas
- Validación de propiedad de pedidos
- Verificación de stock al reordenar

### Tests de Integración
- Flujo completo de reorden
- Exportación de historial
- Búsqueda con múltiples criterios

### Tests de Seguridad
- Cliente no puede ver pedidos de otros
- Permisos correctamente aplicados
- Validación de datos de entrada

---

**Fecha de creación:** 2025-11-22  
**Versión:** 1.0  
**Estado:** Implementado (Controller y Rutas)  
**Pendiente:** Vistas Blade
