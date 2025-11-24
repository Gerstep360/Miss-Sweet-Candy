# CU26 - Auditoría (Logs, Backups)

## Implementación Completa

Este documento describe la implementación del Caso de Uso CU26: Auditoría (logs, backups).

## Componentes Implementados

### 1. Modelos
- **Auditoria** (`app/Models/Auditoria.php`): Modelo mejorado con scopes para filtrado
- **LoginIntento** (`app/Models/LoginIntento.php`): Modelo para registrar intentos de login

### 2. Controladores
- **AuditoriaController** (`app/Http/Controllers/AuditoriaController.php`): Controlador principal con:
  - Filtros avanzados (usuario, acción, entidad, IP, fechas)
  - Exportación a PDF/Excel (pendiente de implementar librerías)
  - Gestión completa de backups
  - Detección de anomalías
  - Visualización de intentos de login

### 3. Servicios
- **BackupService** (`app/Services/BackupService.php`): Servicio para:
  - Crear backups (completo, base de datos, archivos)
  - Listar backups existentes
  - Eliminar backups antiguos
  - Restaurar desde backup
  - Configurar backups automáticos
  - Verificar espacio disponible

- **AnomaliaService** (`app/Services/AnomaliaService.php`): Servicio para:
  - Detectar intentos de login fallidos masivos
  - Detectar actividad en horarios no laborales
  - Detectar eliminaciones masivas
  - Detectar IPs no autorizadas
  - Detectar cambios en datos críticos
  - Detectar actividad inusual de usuarios

### 4. Eventos y Listeners
- **AccionAuditable** (`app/Events/AccionAuditable.php`): Evento para acciones auditables
- **RegistrarAuditoria** (`app/Listeners/RegistrarAuditoria.php`): Listener que registra en BD con fallback a log
- **RegistrarLoginIntento** (`app/Listeners/RegistrarLoginIntento.php`): Listener para eventos de autenticación

### 5. Comandos Artisan
- **BackupAutomatico** (`app/Console/Commands/BackupAutomatico.php`): Comando para ejecutar backups automáticos

### 6. Vistas
- `resources/views/admin/auditoria/index.blade.php`: Listado con filtros
- `resources/views/admin/auditoria/show.blade.php`: Detalle de auditoría
- `resources/views/admin/auditoria/backups.blade.php`: Gestión de backups
- `resources/views/admin/auditoria/anomalias.blade.php`: Detección de anomalías
- `resources/views/admin/auditoria/intentos-login.blade.php`: Intentos de login

### 7. Rutas
- `routes/auditoria.php`: Todas las rutas del módulo de auditoría

## Configuración

### 1. Permisos
Asegúrate de que el rol "admin" tenga acceso a las rutas de auditoría. Las rutas están protegidas con `role:admin`.

### 2. Backups Automáticos

Para configurar backups automáticos, agrega al cron del servidor:

```bash
# Editar crontab
crontab -e

# Agregar línea para backups diarios a las 2:00 AM
0 2 * * * cd /ruta/a/tu/proyecto && php artisan backup:automatico >> /dev/null 2>&1

# O para backups cada hora (ejemplo)
0 * * * * cd /ruta/a/tu/proyecto && php artisan backup:automatico >> /dev/null 2>&1
```

### 3. Configuración de IPs Autorizadas (Opcional)

Puedes configurar IPs autorizadas en `config/auditoria.php`:

```php
<?php

return [
    'ips_autorizadas' => [
        '192.168.1.1',
        '10.0.0.1',
        // Agrega más IPs según necesites
    ],
];
```

### 4. Requisitos del Sistema

- **mysqldump**: Para backups de base de datos
- **tar** y **gzip**: Para backups de archivos
- **Espacio en disco**: Verificar que haya suficiente espacio para backups

## Uso

### Acceder al Módulo de Auditoría

1. Inicia sesión como administrador
2. Navega a `/admin/auditoria`

### Crear Backup Manual

1. Ve a `/admin/auditoria/backups`
2. Selecciona el tipo de backup (completo, base de datos, archivos)
3. Haz clic en "Crear Backup Ahora"

### Configurar Backups Automáticos

1. Ve a `/admin/auditoria/backups`
2. Completa el formulario de configuración:
   - Frecuencia (diaria, semanal, mensual)
   - Hora de ejecución
   - Tipo de backup
   - Días de retención
3. Guarda la configuración
4. Configura el cron job en el servidor (ver arriba)

### Ver Anomalías

1. Ve a `/admin/auditoria/anomalias`
2. Haz clic en "Ejecutar Detección" para ejecutar manualmente
3. Las anomalías se detectan automáticamente cada hora (si se configura un cron)

### Ver Intentos de Login

1. Ve a `/admin/auditoria/intentos-login`
2. Usa los filtros para buscar intentos específicos

## Registro Automático

El sistema registra automáticamente:

- **Login/Logout**: Se registran en `login_intentos` y `auditorias`
- **Acciones CRUD**: Se registran cuando se usan los métodos de `BitacoraController::registrar()`
- **Eventos del sistema**: Cualquier evento `AccionAuditable` se registra automáticamente

## Manejo de Errores

### E1: Error al registrar en tabla de auditorías
- **Acción**: Se registra en archivo de log como fallback
- **Reintentos**: 3 intentos automáticos

### E2: Espacio de almacenamiento insuficiente
- **Acción**: Se notifica al administrador
- **Suspensión**: Se suspenden nuevos backups

### E3: Backup corrupto
- **Acción**: Se detiene la restauración
- **Sugerencia**: Usar backup anterior

### E4: Permisos insuficientes
- **Acción**: Redirige a página 403
- **Registro**: Se registra el intento de acceso no autorizado

## Notas Importantes

1. **Seguridad**: Los backups pueden contener información sensible. Asegúrate de protegerlos adecuadamente.

2. **Rendimiento**: Los backups grandes pueden afectar el rendimiento. Ejecuta backups en horarios de bajo tráfico.

3. **Retención**: Configura una política de retención adecuada para no llenar el disco.

4. **Pruebas**: Prueba la restauración de backups periódicamente para asegurar que funcionan correctamente.

## Próximos Pasos (Opcional)

1. Implementar exportación a PDF usando DomPDF o similar
2. Implementar exportación a Excel usando Maatwebsite/Excel
3. Agregar notificaciones por email cuando se detecten anomalías críticas
4. Crear dashboard con estadísticas de auditoría
5. Implementar alertas en tiempo real para anomalías críticas


