# 🎁 INTEGRACIÓN DE PROMOCIONES EN LA UI

## 📋 Resumen

Se ha integrado el sistema de promociones en el componente **product-selector** para mostrar automáticamente:
- 🏷️ **Badges de promoción** en productos elegibles
- 💰 **Descuentos aplicados** en tiempo real
- 📊 **Resumen de promociones** en el carrito
- ✨ **Total con descuento** en header y footer

---

## 🚀 ¿Cómo Usar?

### 1. Pasar Promociones desde el Controlador

En tus controladores de **pedidos** (create/edit), debes cargar las promociones **activas y vigentes**:

```php
// app/Http/Controllers/PedidoController.php

public function createMesa(Mesa $mesa)
{
    $productos = Producto::with(['categoria', 'inventario'])->get();
    $categorias = Categoria::all();
    
    // ✅ NUEVO: Cargar promociones activas y vigentes
    $promociones = Promocion::with(['productos', 'categorias'])
        ->where('activo', true)
        ->get()
        ->filter(function ($promocion) {
            return $promocion->esta_vigente; // Usa el accessor
        })
        ->values(); // Re-indexar después del filter

    return view('admin.pedidos.create-mesa', compact(
        'mesa',
        'productos',
        'categorias',
        'promociones' // ✅ Pasar promociones
    ));
}

public function createMostrador()
{
    $productos = Producto::with(['categoria', 'inventario'])->get();
    $categorias = Categoria::all();
    
    // ✅ NUEVO: Cargar promociones activas y vigentes
    $promociones = Promocion::with(['productos', 'categorias'])
        ->where('activo', true)
        ->get()
        ->filter(fn($p) => $p->esta_vigente)
        ->values();

    return view('admin.pedidos.create-mostrador', compact(
        'productos',
        'categorias',
        'promociones' // ✅ Pasar promociones
    ));
}

public function edit(Pedido $pedido)
{
    $productos = Producto::with(['categoria', 'inventario'])->get();
    $categorias = Categoria::all();
    
    // ✅ NUEVO: Cargar promociones
    $promociones = Promocion::with(['productos', 'categorias'])
        ->where('activo', true)
        ->get()
        ->filter(fn($p) => $p->esta_vigente)
        ->values();

    return view('admin.pedidos.edit-mesa', compact(
        'pedido',
        'productos',
        'categorias',
        'promociones' // ✅ Pasar promociones
    ));
}
```

---

### 2. Actualizar las Vistas

En tus vistas **Blade**, pasa las promociones al componente:

#### **create-mesa.blade.php**
```blade
{{-- ANTES ❌ --}}
<x-product-selector :productos="$productos" :categorias="$categorias" />

{{-- DESPUÉS ✅ --}}
<x-product-selector 
    :productos="$productos" 
    :categorias="$categorias" 
    :promociones="$promociones" 
/>
```

#### **create-mostrador.blade.php**
```blade
<x-product-selector 
    :productos="$productos" 
    :categorias="$categorias" 
    :promociones="$promociones" 
/>
```

#### **edit-mesa.blade.php**
```blade
<x-product-selector 
    :productos="$productos" 
    :categorias="$categorias" 
    :selectedItems="$pedido->items"
    :promociones="$promociones" 
/>
```

#### **edit-mostrador.blade.php**
```blade
<x-product-selector 
    :productos="$productos" 
    :categorias="$categorias" 
    :selectedItems="$pedido->items"
    :promociones="$promociones" 
/>
```

---

## 🎨 Características Implementadas

### 1. Badge de Promoción en Productos 🎁

Los productos con promociones aplicables muestran un **badge animado**:

```blade
<!-- Badge con gradiente purple-pink y animación pulse -->
<span class="bg-gradient-to-r from-purple-500/30 to-pink-500/30 
             text-purple-300 border border-purple-400/50 
             animate-pulse">
    🎁 PROMO
</span>
```

**Posición:**
- Desktop: Esquina inferior derecha de la imagen
- Mobile: Mismo lugar, tamaño reducido

**Lógica:**
```javascript
tienePromocion(producto) {
    return this.promociones.some(promo => {
        if (promo.aplica_sobre === 'pedido') return true;
        if (promo.aplica_sobre === 'item') {
            return promo.productos_ids?.includes(producto.id) ||
                   promo.categorias_ids?.includes(producto.categoria_id);
        }
        return false;
    });
}
```

---

### 2. Cálculo Automático de Descuentos 💰

El sistema calcula automáticamente los descuentos según la configuración:

#### **Promoción sobre Pedido Completo**
```javascript
if (promo.aplica_sobre === 'pedido') {
    const subtotal = this.items.reduce((sum, it) => 
        sum + (Number(it.cantidad) * Number(it.precio)), 0
    );
    
    if (promo.tipo === 'porcentaje') {
        descuento = subtotal * (Number(promo.valor) / 100);
        if (promo.tope_descuento) {
            descuento = Math.min(descuento, Number(promo.tope_descuento));
        }
    } else if (promo.tipo === 'monto_fijo') {
        descuento = Math.min(Number(promo.valor), subtotal);
    }
}
```

#### **Promoción sobre Items Específicos**
```javascript
if (promo.aplica_sobre === 'item') {
    const itemsElegibles = this.items.filter(it => {
        return promo.productos_ids?.includes(it.producto_id) || 
               promo.categorias_ids?.includes(producto.categoria_id);
    });

    if (promo.tipo === '2x1') {
        descuento = subtotalElegibles * 0.5; // 50% del total
    }
}
```

#### **Prioridad de Promociones**
```javascript
// Se aplican en orden de prioridad (mayor primero)
const promocionesOrdenadas = [...this.promociones].sort((a, b) => 
    (Number(b.prioridad) || 0) - (Number(a.prioridad) || 0)
);
```

---

### 3. Resumen en el Carrito 📊

El carrito muestra:

#### **Sección de Promociones Aplicadas**
```blade
<div class="bg-gradient-to-br from-purple-900/20 to-pink-900/20 
            border border-purple-500/30">
    🎁 Promociones Aplicadas
    
    <div x-for="promo in promocionesAplicadas">
        {{ promo.nombre }}
        {{ promo.tipo }} • Prioridad {{ promo.prioridad }}
        -${{ promo.descuento }}
    </div>
</div>
```

#### **Total con Descuento**
```blade
<!-- Precio Original Tachado -->
<p class="line-through text-zinc-500">
    ${{ total }}
</p>

<!-- Nuevo Precio con Descuento -->
<p class="text-amber-400 font-bold text-3xl">
    ${{ totalConPromociones }}
</p>

<!-- Ahorro Total -->
<p class="text-green-300">
    Ahorras con promociones: -${{ descuentoPromociones }}
</p>
```

---

### 4. Indicadores en Header 🏷️

El header del modal muestra:

```blade
<div class="text-center">
    <div>Total 🎁</div> <!-- Emoji si hay promociones -->
    
    <!-- Precio tachado si hay descuento -->
    <div class="line-through text-white/50">$50.00</div>
    
    <!-- Precio con descuento en verde -->
    <div class="text-green-300 font-bold">$35.00</div>
</div>
```

---

### 5. Botón Flotante Móvil 📱

El botón "Ver Carrito" muestra:

```blade
<button class="...">
    <div>
        🛒 Ver Carrito
        <span>5</span> <!-- Cantidad -->
        🎁 <!-- Si hay promociones -->
    </div>
    
    <div class="flex flex-col">
        <span class="line-through text-sm">$50.00</span>
        <span class="text-green-300 text-xl">$35.00</span>
    </div>
</button>
```

---

## 🔧 Estructura de Datos

### Formato de Promociones

El componente espera un array de promociones con esta estructura:

```php
[
    [
        'id' => 1,
        'nombre' => 'Happy Hour 2x1',
        'tipo' => '2x1', // o 'porcentaje', 'monto_fijo', 'combo'
        'valor' => 50,
        'tope_descuento' => null,
        'aplica_sobre' => 'item', // o 'pedido'
        'prioridad' => 8,
        'activo' => true,
        'productos' => [
            ['id' => 5, 'nombre' => 'Café Latte'],
            ['id' => 7, 'nombre' => 'Cappuccino'],
        ],
        'categorias' => [
            ['id' => 2, 'nombre' => 'Bebidas Calientes'],
        ],
    ],
    // ... más promociones
]
```

**Relaciones necesarias:**
```php
Promocion::with(['productos', 'categorias'])
```

**Campos importantes:**
- `productos`: Array de productos elegibles (si `aplica_sobre='item'`)
- `categorias`: Array de categorías elegibles (si `aplica_sobre='item'`)
- `prioridad`: Orden de aplicación (mayor prioridad primero)

---

## 📱 Responsividad

Todos los elementos mantienen el diseño responsive:

### Mobile (< 1024px)
- Badge "PROMO" muestra solo emoji 🎁
- Tamaños reducidos (text-[9px], py-0.5)
- Grid de 2 columnas para productos
- Botón flotante con total comprimido

### Desktop (>= 1024px)
- Badge "PROMO" muestra "🎁 PROMO"
- Tamaños completos (text-xs, py-1)
- Grid de 4-5 columnas para productos
- Sidebar con carrito visible siempre

---

## 🎯 Casos de Uso

### Caso 1: Promoción 2x1 en Cafés

**Configuración:**
```
Tipo: 2x1
Aplica sobre: Items específicos
Productos: Café Latte, Cappuccino
Valor: 50 (automático)
```

**Resultado:**
- Badge 🎁 en Café Latte y Cappuccino
- Al agregar 2 cafés: descuento de 50%
- Total: $20 → $10 (ahorro $10)

### Caso 2: 20% en Todo el Pedido

**Configuración:**
```
Tipo: Porcentaje
Aplica sobre: Pedido completo
Valor: 20
Tope: $50
```

**Resultado:**
- Badge 🎁 en TODOS los productos
- Descuento del 20% en total
- Máximo descuento: $50

### Caso 3: $15 en Postres

**Configuración:**
```
Tipo: Monto Fijo
Aplica sobre: Items específicos
Categorías: Postres
Valor: 15
```

**Resultado:**
- Badge 🎁 solo en postres
- Descuento de $15 en postres
- Si postres suman $10: descuento = $10

---

## 🧪 Testing

### Test Manual

1. **Crear promoción 2x1 en Cafés**
   ```
   - Crear promoción con tipo "2x1"
   - Seleccionar productos: Café Latte, Cappuccino
   - Activar promoción
   ```

2. **Abrir selector de productos**
   ```
   - Ir a crear pedido (mesa o mostrador)
   - Abrir modal de productos
   - Verificar badge 🎁 en cafés
   ```

3. **Agregar productos y verificar descuento**
   ```
   - Agregar 2 Café Latte ($10 c/u)
   - Verificar en carrito:
     ✓ Subtotal: $20
     ✓ Promoción "2x1": -$10
     ✓ Total: $10
   ```

4. **Verificar en diferentes pantallas**
   ```
   - Mobile: Badge pequeño, totales comprimidos
   - Desktop: Badge completo, sidebar visible
   - Tablet: Transición suave entre estilos
   ```

### Checklist de Verificación

- [ ] Badge de promoción aparece en productos elegibles
- [ ] Badge se oculta en productos NO elegibles
- [ ] Descuento se calcula correctamente
- [ ] Promociones se aplican en orden de prioridad
- [ ] Total con descuento se muestra en header
- [ ] Total con descuento se muestra en carrito
- [ ] Total con descuento se muestra en botón móvil
- [ ] Responsive funciona en mobile/tablet/desktop
- [ ] Animaciones suaves (pulse, transitions)
- [ ] No hay errores en consola

---

## ⚠️ Consideraciones Importantes

### 1. Accessor `esta_vigente`

El modelo `Promocion` debe tener el accessor:

```php
// app/Models/Promocion.php

protected $appends = ['esta_vigente'];

public function getEstaVigenteAttribute(): bool
{
    $now = Carbon::now();
    
    // Verificar fechas
    if ($this->fecha_inicio && $now->lt($this->fecha_inicio)) return false;
    if ($this->fecha_fin && $now->gt($this->fecha_fin)) return false;
    
    // Verificar horas
    if ($this->hora_inicio || $this->hora_fin) {
        $currentTime = $now->format('H:i:s');
        if ($this->hora_inicio && $currentTime < $this->hora_inicio) return false;
        if ($this->hora_fin && $currentTime > $this->hora_fin) return false;
    }
    
    // Verificar días
    if ($this->dias_semana) {
        $hoy = strtolower($now->locale('es')->dayName);
        $dias = is_array($this->dias_semana) 
            ? $this->dias_semana 
            : explode(',', $this->dias_semana);
        if (!in_array($hoy, array_map('trim', $dias))) return false;
    }
    
    return true;
}
```

### 2. Relaciones Eager Loading

**Siempre cargar con `with()`:**
```php
Promocion::with(['productos', 'categorias'])
```

**Nunca:**
```php
Promocion::all() // ❌ Falta eager loading
```

### 3. Filtrado Correcto

```php
// ✅ CORRECTO
$promociones = Promocion::with(['productos', 'categorias'])
    ->where('activo', true)
    ->get()
    ->filter(fn($p) => $p->esta_vigente)
    ->values(); // Re-indexar después de filter

// ❌ INCORRECTO
$promociones = Promocion::where('activo', true)
    ->where('esta_vigente', true) // No es una columna de BD
    ->get();
```

### 4. Performance

Si tienes muchas promociones:

```php
// Cachear por 5 minutos
$promociones = Cache::remember('promociones_vigentes', 300, function () {
    return Promocion::with(['productos', 'categorias'])
        ->where('activo', true)
        ->get()
        ->filter(fn($p) => $p->esta_vigente)
        ->values();
});
```

---

## 📚 Archivos Modificados

### ✏️ Editados

1. **resources/views/components/product-selector.blade.php**
   - Props: Agregado `promociones`
   - Badge de promoción en grid de productos
   - Header: Total con descuento
   - Botón móvil: Total con descuento
   - JavaScript: Funciones de cálculo de promociones

2. **resources/views/components/product-selector/cart.blade.php**
   - Sección de promociones aplicadas
   - Total con precio tachado
   - Ahorro con promociones

### 📝 A Editar (Pendiente)

1. **app/Http/Controllers/PedidoController.php**
   - Método `createMesa()`: Pasar `$promociones`
   - Método `createMostrador()`: Pasar `$promociones`
   - Método `edit()`: Pasar `$promociones`

2. **resources/views/admin/pedidos/create-mesa.blade.php**
   - Componente: Agregar `:promociones="$promociones"`

3. **resources/views/admin/pedidos/create-mostrador.blade.php**
   - Componente: Agregar `:promociones="$promociones"`

4. **resources/views/admin/pedidos/edit-mesa.blade.php**
   - Componente: Agregar `:promociones="$promociones"`

5. **resources/views/admin/pedidos/edit-mostrador.blade.php**
   - Componente: Agregar `:promociones="$promociones"`

---

## 🎉 ¡Listo para Usar!

El sistema de promociones está completamente integrado en la UI. Solo necesitas:

1. ✅ Pasar `$promociones` desde tus controladores
2. ✅ Actualizar los componentes en las vistas
3. ✅ Verificar que el accessor `esta_vigente` funciona
4. ✅ ¡Probar y disfrutar! 🚀

---

## 🆘 Soporte

Si encuentras algún problema:

1. Verificar que las promociones tienen `activo = true`
2. Verificar que `esta_vigente` devuelve `true`
3. Verificar que las relaciones están cargadas (`with()`)
4. Revisar consola del navegador para errores JS
5. Revisar logs de Laravel: `storage/logs/laravel.log`

**Documentación relacionada:**
- `RESUMEN_FINAL_PROMOCIONES.md` - Sistema de promociones completo
- `MEJORAS_PROMOCIONES.md` - Mejoras UI/UX de promociones

---

**🎊 ¡Sistema de Promociones en la UI Completado!**

Ahora tus clientes verán en tiempo real qué productos tienen promociones y cuánto están ahorrando. 💰✨
