# Sistema de Promociones - Miss Sweet Candy

## 📋 Características Implementadas

### 1. Notificaciones Automáticas
- ✅ Notificaciones en tiempo real cuando se crea una promoción
- ✅ Notificaciones para promociones próximas a expirar (3 días o menos)
- ✅ Sistema de notificaciones integrado con el modelo `Notificacion`

### 2. Emails Automáticos
- ✅ Email de bienvenida cuando se crea una promoción
- ✅ Email de alerta para promociones próximas a expirar
- ✅ Envío a administradores y cajeros
- ✅ Diseño responsivo con HTML/CSS

### 3. Aplicación Automática de Promociones
- ✅ Servicio `PromocionService` para gestionar toda la lógica
- ✅ Validación de vigencia (fechas, horarios, días de la semana)
- ✅ Aplicación automática en pedidos (mesa, mostrador, web)
- ✅ Soporte para múltiples tipos de descuento:
  - Porcentaje
  - Monto fijo
  - 2x1
  - Combos

### 4. Integración con Ventas
- ✅ Descuentos reflejados en el total del pedido
- ✅ Métodos `getTotalConPromocionesAttribute()` y `calcularDescuentoPromociones()`
- ✅ Listado de promociones aplicadas a cada pedido
- ✅ Tope de descuento configurable

### 5. Verificación Automática
- ✅ Comando artisan `promociones:verificar-expiracion`
- ✅ Programado para ejecutarse diariamente a las 9:00 AM
- ✅ Envío de alertas 3 días antes de expiración

## 🚀 Uso

### Crear una Promoción
```php
// Desde el controlador (ya implementado)
$promocion = Promocion::create([
    'nombre' => 'Happy Hour',
    'tipo' => 'porcentaje',
    'valor' => 20,
    'aplica_sobre' => 'pedido',
    'fecha_inicio' => now(),
    'fecha_fin' => now()->addDays(7),
    'hora_inicio' => '14:00',
    'hora_fin' => '17:00',
    'dias_semana' => ['lun', 'mar', 'mie', 'jue', 'vie'],
    'activo' => true,
]);

// Automáticamente se envían:
// - Notificaciones a todos los usuarios
// - Emails a administradores y cajeros
```

### Aplicar Promociones a un Pedido
```php
// Automático al crear/actualizar un pedido
$pedido = Pedido::find(1);

// Obtener total con descuentos
$totalFinal = $pedido->total_con_promociones;

// Obtener descuento aplicado
$descuento = $pedido->calcularDescuentoPromociones();

// Obtener detalles de promociones aplicadas
$promociones = $pedido->getPromocionesAplicadas();
```

### Verificar Promociones Próximas a Expirar
```bash
# Ejecutar manualmente
php artisan promociones:verificar-expiracion

# Se ejecuta automáticamente todos los días a las 9:00 AM
```

## 📧 Configuración de Email

Asegúrate de configurar el archivo `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicación
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="Miss Sweet Candy"
```

## 🔔 Tipos de Notificaciones

1. **promocion** - Nueva promoción creada
2. **promocion_expira** - Promoción próxima a expirar

## 📊 Tipos de Promociones

### Porcentaje
- Descuento del X% sobre items o pedido
- Puede tener tope de descuento

### Monto Fijo
- Descuento de $X sobre items o pedido
- Se aplica hasta agotar el monto

### 2x1
- Paga 1, lleva 2
- Se aplica sobre items específicos
- Automático para cantidades pares

### Combos
- Combinación de productos a precio especial
- Configuración flexible

## 🎯 Aplicación de Promociones

### Sobre Item
- Se aplica a productos o categorías específicas
- Cada item elegible recibe el descuento

### Sobre Pedido
- Se aplica al total del pedido
- Sin importar los productos

## ⏰ Restricciones

- **Fechas**: inicio y fin opcionales
- **Horarios**: rango horario opcional
- **Días de la semana**: selección múltiple
- **Prioridad**: orden de aplicación
- **Tope de descuento**: máximo descuento permitido

## 🔄 Flujo Completo

1. **Admin crea promoción** → Sistema valida y guarda
2. **Sistema crea notificaciones** → Para todos los usuarios
3. **Sistema envía emails** → A admins y cajeros
4. **Cliente hace pedido** → Promociones se aplican automáticamente
5. **Sistema calcula descuento** → Según reglas configuradas
6. **Pedido muestra total final** → Con descuentos aplicados
7. **Diariamente a las 9 AM** → Verificación de expiraciones
8. **3 días antes de expirar** → Alertas automáticas

## 📱 Archivos Creados/Modificados

### Nuevos Archivos
- `app/Mail/PromocionCreada.php`
- `app/Mail/PromocionPorExpirar.php`
- `app/Services/PromocionService.php`
- `app/Console/Commands/VerificarPromocionesExpirar.php`
- `resources/views/emails/promocion-creada.blade.php`
- `resources/views/emails/promocion-por-expirar.blade.php`

### Archivos Modificados
- `app/Http/Controllers/PromocionController.php`
- `app/Models/Pedido.php`
- `routes/console.php`

## 🎨 Personalización

### Cambiar Horario de Verificación
Edita `routes/console.php`:
```php
Schedule::command('promociones:verificar-expiracion')
    ->dailyAt('10:00') // Cambiar hora
    ->timezone('America/La_Paz');
```

### Cambiar Días de Alerta
Edita `VerificarPromocionesExpirar.php`:
```php
$tresDias = $hoy->copy()->addDays(5); // Alertar 5 días antes
```

## 🐛 Troubleshooting

### Las promociones no se aplican
1. Verificar que `activo = true`
2. Verificar fechas y horarios
3. Verificar días de la semana
4. Revisar productos/categorías asociadas

### No llegan los emails
1. Verificar configuración SMTP en `.env`
2. Verificar que usuarios tengan email configurado
3. Revisar logs: `storage/logs/laravel.log`

### Notificaciones no se crean
1. Verificar tabla `notificaciones` existe
2. Verificar que usuarios estén activos
3. Revisar permisos de base de datos

## 📈 Próximas Mejoras

- [ ] Reportes de promociones más utilizadas
- [ ] Límite de usos por cliente
- [ ] Códigos de cupón
- [ ] Promociones por geolocalización
- [ ] Historial de promociones aplicadas por pedido
