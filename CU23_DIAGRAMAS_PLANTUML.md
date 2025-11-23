# Diagramas de Comunicación PlantUML - CU23: Historial de Pedidos del Cliente

## Archivos Generados

### 1. CU23_DIAGRAMA_COMUNICACION.puml
Diagrama completo con todos los flujos de comunicación numerados.

**Incluye:**
- 5 flujos principales completos
- 48 mensajes numerados
- Todos los objetos del sistema
- Notas explicativas

### 2. CU23_DIAGRAMA_COMUNICACION_SIMPLE.puml
Diagrama simplificado enfocado en los 3 flujos más importantes.

**Incluye:**
- 3 flujos principales (Consultar, Reordenar, Estadísticas)
- 32 mensajes numerados
- Objetos principales
- Notas explicativas

---

## Cómo Visualizar los Diagramas

### Opción 1: PlantUML Online
1. Ir a: http://www.plantuml.com/plantuml/uml/
2. Copiar el contenido del archivo `.puml`
3. Pegar en el editor
4. Ver el diagrama generado

### Opción 2: Visual Studio Code
1. Instalar extensión: "PlantUML" de jebbs
2. Abrir el archivo `.puml`
3. Presionar `Alt + D` para previsualizar
4. O hacer clic derecho → "Preview Current Diagram"

### Opción 3: IntelliJ IDEA / PhpStorm
1. Instalar plugin: "PlantUML integration"
2. Abrir el archivo `.puml`
3. El diagrama se mostrará automáticamente

### Opción 4: Exportar a Imagen
```bash
# Instalar PlantUML
brew install plantuml  # macOS
apt-get install plantuml  # Linux

# Generar imagen PNG
plantuml CU23_DIAGRAMA_COMUNICACION.puml

# Generar imagen SVG
plantuml -tsvg CU23_DIAGRAMA_COMUNICACION.puml
```

---

## Descripción de los Flujos

### Flujo 1: Consultar Historial (Mensajes 1-10)
```
Cliente → Controller → User (Auth) → Pedido → Database
→ Calcular Estadísticas → Bitácora → Vista
```

**Pasos:**
1. Cliente solicita historial con filtros
2. Controller obtiene ID del cliente autenticado
3. Consulta pedidos del cliente en BD
4. Calcula estadísticas (total gastado, pedidos por tipo, etc.)
5. Registra en bitácora
6. Retorna vista con datos

### Flujo 2: Reordenar Pedido (Mensajes 11-23)
```
Cliente → Controller → Pedido Original → Producto → Inventario
→ Crear Nuevo Pedido → Crear Items → Descontar Stock
→ Bitácora → Commit → Redirect
```

**Pasos:**
1. Cliente solicita reordenar pedido anterior
2. Controller busca pedido original
3. Inicia transacción de BD
4. Verifica disponibilidad de productos
5. Verifica stock en inventario
6. Crea nuevo pedido
7. Crea items del pedido con precios vigentes
8. Descuenta stock del inventario
9. Registra en bitácora
10. Confirma transacción
11. Redirige con mensaje de éxito

### Flujo 3: Ver Estadísticas (Mensajes 24-32)
```
Cliente → Controller → Pedido → Database (agregaciones)
→ PedidoItem (productos más pedidos) → Bitácora → Vista
```

**Pasos:**
1. Cliente solicita estadísticas
2. Controller consulta pedidos del cliente
3. Ejecuta consultas agregadas (COUNT, SUM, AVG)
4. Obtiene productos más pedidos (TOP 10)
5. Registra en bitácora
6. Retorna dashboard con métricas

### Flujo 4: Ver Detalle (Mensajes 10-17)
```
Cliente → Controller → Pedido → Database
→ Verificar Propiedad → Obtener Promociones
→ Bitácora → Vista Detalle
```

### Flujo 5: Exportar Historial (Mensajes 43-48)
```
Cliente → Controller → Pedido → Database
→ Bitácora → Archivo (PDF/Excel)
```

---

## Objetos del Diagrama

### HistorialPedidosClienteController
**Responsabilidad:** Gestionar historial de pedidos del cliente

**Métodos:**
- `index()` - Lista historial con filtros
- `show(id)` - Detalle de pedido
- `reordenar(id)` - Formulario de reorden
- `storeReorden(Request, id)` - Procesar reorden
- `estadisticas()` - Dashboard de métricas
- `exportar(Request)` - Exportar datos
- `buscar(Request)` - Búsqueda avanzada

### Pedido
**Responsabilidad:** Entidad principal de pedido

**Atributos:**
- id, cliente_id, tipo, estado, total

**Relaciones:**
- belongsTo(User) - cliente
- hasMany(PedidoItem) - items
- hasMany(CobroCaja) - cobros

### PedidoItem
**Responsabilidad:** Items individuales del pedido

**Atributos:**
- id, pedido_id, producto_id, cantidad, precio_unitario

### Producto
**Responsabilidad:** Catálogo de productos

**Atributos:**
- id, nombre, precio, activo

**Relaciones:**
- hasOne(InventarioProducto)

### InventarioProducto
**Responsabilidad:** Control de stock

**Métodos:**
- `incrementarStock(cantidad)`
- `decrementarStock(cantidad)`
- `requiereAlerta()`

### User
**Responsabilidad:** Usuario del sistema

**Métodos:**
- `Auth::id()` - Obtener usuario autenticado

### BitacoraController
**Responsabilidad:** Auditoría del sistema

**Métodos:**
- `registrar(accion, modelo, id, datos, Request)`

### Database
**Responsabilidad:** Persistencia de datos

**Métodos:**
- `beginTransaction()`
- `commit()`
- `rollBack()`

---

## Numeración de Mensajes

Los mensajes están numerados secuencialmente para mostrar el orden de ejecución:

- **1-10:** Flujo de consulta de historial
- **11-23:** Flujo de reorden de pedido
- **24-32:** Flujo de estadísticas
- **33-42:** Flujo de estadísticas detalladas (diagrama completo)
- **43-48:** Flujo de exportación (diagrama completo)

---

## Características del Diagrama

### Orientación Horizontal
```plantuml
left to right direction
```
Los objetos se organizan de izquierda a derecha para mejor legibilidad.

### Actor Estilizado
```plantuml
skinparam actorStyle awesome
```
El cliente se muestra como un muñeco moderno.

### Colores y Estilos
- **Verde claro:** Objetos del sistema
- **Amarillo claro:** Notas explicativas
- **Flechas negras:** Mensajes entre objetos

### Notas Explicativas
Cada diagrama incluye notas que explican:
- Responsabilidades de los objetos
- Métodos principales
- Relaciones importantes
- Lógica de negocio

---

## Ejemplo de Uso

### Caso: Cliente reordena un pedido anterior

**Secuencia:**
1. Cliente ve su historial de pedidos
2. Selecciona un pedido anterior
3. Click en "Reordenar"
4. Sistema verifica disponibilidad de productos
5. Sistema verifica stock en inventario
6. Cliente confirma el reorden
7. Sistema crea nuevo pedido
8. Sistema descuenta stock
9. Sistema registra en bitácora
10. Cliente recibe confirmación

**Mensajes involucrados:** 11-23

---

## Ventajas del Diagrama de Comunicación

✅ **Muestra el flujo completo** de mensajes entre objetos  
✅ **Numeración secuencial** facilita seguir la ejecución  
✅ **Vista horizontal** mejora la legibilidad  
✅ **Incluye todos los objetos** del sistema involucrados  
✅ **Notas explicativas** ayudan a entender la lógica  
✅ **Fácil de mantener** en formato texto PlantUML  

---

## Diferencias entre Versiones

| Característica | Completo | Simple |
|----------------|----------|--------|
| Flujos | 5 | 3 |
| Mensajes | 48 | 32 |
| Objetos | 8 | 8 |
| Notas | 3 | 3 |
| Complejidad | Alta | Media |
| Uso recomendado | Documentación técnica | Presentaciones |

---

**Sistema:** Cafetería/Pastelería - CU23 Historial de Pedidos  
**Tipo:** Diagrama de Comunicación (Colaboración)  
**Formato:** PlantUML  
**Versión:** 1.0
