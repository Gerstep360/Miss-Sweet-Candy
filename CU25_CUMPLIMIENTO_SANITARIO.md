# CU25: Cumplimiento Sanitario (SENASAG)

## Descripción
Caso de uso que gestiona el registro y seguimiento de actividades de cumplimiento sanitario para cumplir con las normativas del SENASAG (Servicio Nacional de Sanidad Agropecuaria e Inocuidad Alimentaria).

Incluye registro de actividades de limpieza, control de temperaturas de equipos y alimentos, carga de evidencias fotográficas y generación de reportes para auditorías.

---

## Implementación

### Controller Creado
**Archivo:** `app/Http/Controllers/CumplimientoSanitarioController.php`

### Rutas Creadas
**Archivo:** `routes/cumplimiento_sanitario.php`

### Migración de Base de Datos
**Archivo:** `database/migrations/2025_11_22_000001_create_cumplimiento_sanitario_tables.php`

---

## Funcionalidades Implementadas

### 1. Dashboard de Cumplimiento Sanitario
**Ruta:** `GET /cumplimiento-sanitario`  
**Método:** `index(Request $request)`  
**Permiso:** `ver-cumplimiento-sanitario`

**Características:**
- Vista general del día seleccionado
- Registros de limpieza del día
- Registros de temperatura del día
- Estadísticas de cumplimiento
- Alertas activas
- Filtro por fecha

**Estadísticas mostradas:**
- Total de limpiezas realizadas
- Total de controles de temperatura
- Temperaturas fuera de rango
- Equipos en estado crítico
- Áreas limpiadas
- Estado de cumplimiento (completo/parcial)

---

### 2. Registro de Actividades de Limpieza

#### 2.1 Formulario de Registro
**Ruta:** `GET /cumplimiento-sanitario/limpieza/crear`  
**Método:** `createLimpieza()`  
**Permiso:** `registrar-cumplimiento-sanitario`

**Áreas predefinidas:**
- Cocina
- Barra de café
- Salón comedor
- Baños
- Almacén
- Refrigeradores
- Equipos de cocina
- Mesas y sillas
- Pisos
- Paredes

**Tipos de limpieza:**
- Limpieza rutinaria
- Limpieza profunda
- Desinfección
- Sanitización

#### 2.2 Guardar Registro
**Ruta:** `POST /cumplimiento-sanitario/limpieza`  
**Método:** `storeLimpieza(Request $request)`  
**Permiso:** `registrar-cumplimiento-sanitario`

**Datos requeridos:**
```php
[
    'area' => 'cocina',
    'tipo_limpieza' => 'desinfeccion',
    'productos_usados' => 'Cloro, detergente industrial',
    'observaciones' => 'Limpieza completa',
    'evidencias' => [archivo1.jpg, archivo2.jpg], // Opcional
]
```

**Validaciones:**
- Área: requerida, máx 100 caracteres
- Tipo limpieza: requerido, máx 50 caracteres
- Productos usados: requerido, máx 500 caracteres
- Observaciones: opcional, máx 1000 caracteres
- Evidencias: imágenes JPEG/PNG, máx 5MB cada una

---

### 3. Control de Temperaturas

#### 3.1 Formulario de Registro
**Ruta:** `GET /cumplimiento-sanitario/temperatura/crear`  
**Método:** `createTemperatura()`  
**Permiso:** `registrar-cumplimiento-sanitario`

**Equipos monitoreados:**
- Refrigerador 1 (Lácteos): 0-4°C
- Refrigerador 2 (Carnes): 0-4°C
- Congelador 1: -18 a -15°C
- Vitrina refrigerada: 2-8°C
- Cámara fría: 0-4°C
- Barra caliente: 60-70°C
- Horno: 180-250°C
- Plancha: 150-200°C

#### 3.2 Guardar Registro
**Ruta:** `POST /cumplimiento-sanitario/temperatura`  
**Método:** `storeTemperatura(Request $request)`  
**Permiso:** `registrar-cumplimiento-sanitario`

**Datos requeridos:**
```php
[
    'equipo' => 'refrigerador_1',
    'temperatura' => 3.5,
    'temperatura_minima' => 0,
    'temperatura_maxima' => 4,
    'estado_equipo' => 'normal', // normal, alerta, critico
    'observaciones' => 'Funcionamiento correcto',
    'evidencias' => [termometro.jpg], // Opcional
]
```

**Validaciones:**
- Equipo: requerido, máx 100 caracteres
- Temperatura: requerida, numérica, entre -50 y 300
- Temperaturas min/max: opcionales, numéricas
- Estado equipo: requerido (normal, alerta, crítico)
- Observaciones: opcional, máx 1000 caracteres
- Evidencias: imágenes JPEG/PNG, máx 5MB

**Alertas automáticas:**
- Se genera alerta si temperatura está fuera de rango
- Se genera alerta si equipo está en estado crítico
- Notificación a usuarios con permiso correspondiente

---

### 4. Detalle de Registros

#### 4.1 Detalle de Limpieza
**Ruta:** `GET /cumplimiento-sanitario/limpieza/{id}`  
**Método:** `showLimpieza($id)`  
**Permiso:** `ver-cumplimiento-sanitario`

**Información mostrada:**
- Área limpiada
- Tipo de limpieza
- Productos utilizados
- Observaciones
- Responsable
- Fecha y hora
- Evidencias fotográficas

#### 4.2 Detalle de Temperatura
**Ruta:** `GET /cumplimiento-sanitario/temperatura/{id}`  
**Método:** `showTemperatura($id)`  
**Permiso:** `ver-cumplimiento-sanitario`

**Información mostrada:**
- Equipo monitoreado
- Temperatura registrada
- Rango permitido
- Estado del equipo
- Fuera de rango (sí/no)
- Observaciones
- Responsable
- Fecha y hora
- Evidencias fotográficas

---

### 5. Historial de Registros
**Ruta:** `GET /cumplimiento-sanitario/historial`  
**Método:** `historial(Request $request)`  
**Permiso:** `ver-cumplimiento-sanitario`

**Filtros disponibles:**
- Tipo: limpieza o temperatura
- Rango de fechas (inicio - fin)
- Área específica (para limpieza)
- Equipo específico (para temperatura)

**Características:**
- Paginación de 20 registros por página
- Ordenado por fecha y hora descendente
- Vista unificada de registros

**Ejemplo de uso:**
```
/cumplimiento-sanitario/historial?tipo=limpieza&fecha_inicio=2025-11-01&fecha_fin=2025-11-30
/cumplimiento-sanitario/historial?tipo=temperatura&equipo=refrigerador_1
```

---

### 6. Alertas Activas
**Ruta:** `GET /cumplimiento-sanitario/alertas`  
**Método:** `alertas()`  
**Permiso:** `ver-cumplimiento-sanitario`

**Tipos de alertas:**

1. **Temperaturas fuera de rango** (últimas 24 horas)
   - Equipos con temperatura fuera del rango permitido
   - Nivel: Warning

2. **Equipos en estado crítico** (últimas 24 horas)
   - Equipos que requieren atención inmediata
   - Nivel: Danger

3. **Áreas sin limpieza** (últimas 24 horas)
   - Áreas que no han sido limpiadas
   - Nivel: Info

---

### 7. Reporte SENASAG
**Ruta:** `GET /cumplimiento-sanitario/reporte-senasag`  
**Método:** `reporteSenasag(Request $request)`  
**Permiso:** `generar-reportes-sanitarios`

**Parámetros:**
```php
[
    'fecha_inicio' => '2025-11-01',
    'fecha_fin' => '2025-11-30',
    'tipo_reporte' => 'completo', // completo, limpieza, temperatura
    'formato' => 'pdf', // pdf, excel
]
```

**Contenido del reporte:**
- Resumen ejecutivo del período
- Registros de limpieza
- Registros de temperatura
- Estadísticas de cumplimiento
- Incumplimientos detectados
- Acciones correctivas
- Evidencias fotográficas

**Estadísticas incluidas:**
- Total de limpiezas realizadas
- Total de controles de temperatura
- Incumplimientos de temperatura
- Días del período
- Promedio de limpiezas por día
- Promedio de controles por día
- Tasa de cumplimiento (%)

---

### 8. Exportación de Registros
**Ruta:** `GET /cumplimiento-sanitario/exportar`  
**Método:** `exportar(Request $request)`  
**Permiso:** `generar-reportes-sanitarios`

**Parámetros:**
```php
[
    'tipo' => 'limpieza', // limpieza o temperatura
    'fecha_inicio' => '2025-11-01',
    'fecha_fin' => '2025-11-30',
    'formato' => 'excel', // pdf, excel, csv
]
```

**Formatos disponibles:**
- PDF: Documento formateado para impresión
- Excel: Hoja de cálculo con datos tabulados
- CSV: Archivo de valores separados por comas

---

### 9. Gestión de Evidencias
**Ruta:** `DELETE /cumplimiento-sanitario/evidencia/{id}`  
**Método:** `eliminarEvidencia($id)`  
**Permiso:** `registrar-cumplimiento-sanitario`

**Características:**
- Elimina archivo físico del storage
- Elimina registro de base de datos
- Registra acción en bitácora
- Solo usuarios autorizados

---

## Estructura de Base de Datos

### Tabla: cumplimiento_limpieza
```sql
id                  BIGINT PRIMARY KEY
area                VARCHAR(100)
tipo_limpieza       VARCHAR(50)
productos_usados    VARCHAR(500)
observaciones       TEXT
responsable_id      BIGINT (FK users)
fecha_registro      DATE
hora_registro       TIME
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEXES:
- (fecha_registro, area)
- responsable_id
```

### Tabla: cumplimiento_temperatura
```sql
id                  BIGINT PRIMARY KEY
equipo              VARCHAR(100)
temperatura         DECIMAL(5,2)
temperatura_minima  DECIMAL(5,2)
temperatura_maxima  DECIMAL(5,2)
fuera_rango         BOOLEAN
estado_equipo       ENUM('normal','alerta','critico')
observaciones       TEXT
responsable_id      BIGINT (FK users)
fecha_registro      DATE
hora_registro       TIME
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEXES:
- (fecha_registro, equipo)
- fuera_rango
- estado_equipo
- responsable_id
```

### Tabla: cumplimiento_evidencias
```sql
id                  BIGINT PRIMARY KEY
tipo_registro       ENUM('limpieza','temperatura')
registro_id         BIGINT
ruta_archivo        VARCHAR(500)
nombre_original     VARCHAR(255)
created_at          TIMESTAMP

INDEXES:
- (tipo_registro, registro_id)
```

---

## Permisos Requeridos

### ver-cumplimiento-sanitario
Permite:
- Ver dashboard de cumplimiento
- Consultar historial de registros
- Ver detalles de registros
- Ver alertas activas

### registrar-cumplimiento-sanitario
Permite:
- Registrar actividades de limpieza
- Registrar controles de temperatura
- Subir evidencias fotográficas
- Eliminar evidencias

### generar-reportes-sanitarios
Permite:
- Generar reportes SENASAG
- Exportar registros en diferentes formatos
- Acceso a estadísticas completas

---

## Seguridad Implementada

### Triple Capa de Seguridad
1. **Middleware auth**: Usuario autenticado
2. **Middleware can**: Verificación de permisos
3. **Validación en controller**: Validación de datos

### Almacenamiento Seguro
- Evidencias guardadas en storage/public
- Nombres de archivo únicos (hash)
- Validación de tipo MIME
- Límite de tamaño (5MB por archivo)

### Auditoría Completa
Todas las operaciones registradas en bitácora:
- Consultas de dashboard
- Registros de limpieza
- Registros de temperatura
- Generación de reportes
- Eliminación de evidencias

---

## Flujos Principales

### Flujo 1: Registro de Limpieza
```
Usuario → Login → Dashboard
→ "Registrar Limpieza"
→ Formulario (área, tipo, productos, observaciones)
→ Subir evidencias (opcional)
→ Submit
→ Validación
→ Guardar en BD
→ Guardar evidencias en storage
→ Registrar en bitácora
→ Redirect a dashboard con mensaje éxito
```

### Flujo 2: Control de Temperatura
```
Usuario → Login → Dashboard
→ "Registrar Temperatura"
→ Formulario (equipo, temperatura, rango, estado)
→ Subir evidencias (opcional)
→ Submit
→ Validación
→ Verificar si está fuera de rango
→ Guardar en BD
→ Guardar evidencias
→ ¿Fuera de rango o crítico?
  → SÍ: Generar alerta + notificaciones
  → NO: Continuar
→ Registrar en bitácora
→ Redirect con mensaje (success/warning)
```

### Flujo 3: Generación de Reporte SENASAG
```
Usuario → Dashboard → "Generar Reporte"
→ Formulario (fechas, tipo, formato)
→ Submit
→ Validación
→ Consultar registros del período
→ Calcular estadísticas
→ Identificar incumplimientos
→ Generar documento (PDF/Excel)
→ Registrar en bitácora
→ Descargar archivo
```

### Flujo 4: Sistema de Alertas
```
Registro de temperatura → Guardar
→ Verificar condiciones:
  - ¿Fuera de rango?
  - ¿Estado crítico?
→ SI cumple condición:
  → Obtener usuarios con permiso
  → Crear notificación para cada usuario
  → Mensaje descriptivo con detalles
  → Guardar en tabla notificaciones
→ Usuario ve alerta en panel
```

---

## Cálculos y Métricas

### Estadísticas del Día
```php
- total_limpiezas: COUNT(registros del día)
- total_temperaturas: COUNT(registros del día)
- temperaturas_alerta: COUNT(fuera_rango = true)
- equipos_criticos: COUNT(estado = 'critico')
- areas_limpiadas: COUNT(DISTINCT area)
- cumplimiento_limpieza: >= 5 áreas ? 'completo' : 'parcial'
- cumplimiento_temperatura: >= 3 registros ? 'completo' : 'parcial'
```

### Estadísticas del Período
```php
- total_limpiezas: COUNT(período)
- total_temperaturas: COUNT(período)
- incumplimientos: COUNT(fuera_rango = true)
- dias_periodo: DATEDIFF(fin, inicio) + 1
- promedio_limpiezas_dia: total / dias
- promedio_temperaturas_dia: total / dias
- tasa_cumplimiento: ((total - incumplimientos) / total) * 100
```

---

## Normativas SENASAG Consideradas

### Control de Temperaturas
- **Refrigeración**: 0-4°C (alimentos perecederos)
- **Congelación**: -18°C o menos
- **Alimentos calientes**: > 60°C
- **Zona de peligro**: 5-60°C (evitar)

### Frecuencia de Limpieza
- **Áreas críticas** (cocina, barra): Diaria
- **Equipos de refrigeración**: Semanal
- **Áreas comunes**: Diaria
- **Baños**: Múltiples veces al día
- **Limpieza profunda**: Semanal/Mensual

### Documentación Requerida
- Registros diarios de temperatura
- Bitácora de limpieza y desinfección
- Evidencias fotográficas
- Acciones correctivas
- Capacitación del personal

---

## Mejoras Futuras

### Funcionalidades
- [ ] Programación de recordatorios automáticos
- [ ] Checklist digital de limpieza
- [ ] Integración con sensores IoT de temperatura
- [ ] Firma digital del responsable
- [ ] Códigos QR para áreas/equipos
- [ ] App móvil para registro rápido
- [ ] Dashboard en tiempo real
- [ ] Análisis predictivo de fallas

### Reportes
- [ ] Gráficos de tendencias de temperatura
- [ ] Mapas de calor de limpieza
- [ ] Comparativas mensuales
- [ ] Alertas por email/SMS
- [ ] Integración con sistema de mantenimiento

### Optimizaciones
- [ ] Compresión automática de imágenes
- [ ] Almacenamiento en la nube
- [ ] Cache de estadísticas
- [ ] Exportación asíncrona de reportes grandes

---

**Fecha de creación:** 2025-11-22  
**Versión:** 1.0  
**Sistema:** Cafetería/Pastelería - Módulo de Cumplimiento Sanitario  
**Estado:** Implementado (Controller, Rutas y Migración)  
**Pendiente:** Vistas Blade y generación real de PDF/Excel
