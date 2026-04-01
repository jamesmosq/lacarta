# LaCarta — Contexto del proyecto para Claude

## Descripcion
SaaS de menu digital para restaurantes. Cada restaurante es un tenant independiente con su propio schema PostgreSQL. Los clientes escanean un QR en la mesa, ven el menu y hacen su pedido desde el celular. El dueno gestiona todo desde un panel de administracion.

---

## Stack tecnico
- **Backend:** Laravel 12, PHP 8.3.6
- **Base de datos:** PostgreSQL 17, estrategia schema-based (un schema por tenant)
- **Multi-tenancy:** `stancl/tenancy` v3.9 con `PostgreSQLSchemaManager`
- **Tenancy por path:** `/{tenant}/admin/...` — sin subdominios
- **Frontend:** Tailwind CSS (CDN), SweetAlert2 (CDN), Chart.js (CDN), QRCode.js (CDN)
- **Sin Vite ni npm** — todo via CDN
- **Deploy:** Railway — `https://lacarta-production.up.railway.app`

---

## Base de datos
- **Local:** wamp64, DB `misaas`, usuario `postgres`, pass `base1234`
- **Railway:** interno `postgres.railway.internal:5432`, publico `mainline.proxy.rlwy.net:42458`, DB `railway`
- **Schema central:** `tenants`, `domains`, `sessions`, `cache`, `jobs`
- **Schema por tenant:** `users`, `categories`, `dishes`, `tables`, `orders`, `order_items`, `ingredients`, `dish_ingredients`

---

## Estructura de archivos clave

### Modelos
- `app/Models/Tenant.php` — columnas custom: `id, name, email, slug, plan, is_open, trial_ends_at`
- `app/Models/TenantUser.php` — usuario del tenant (tabla `users`), roles: `owner`, `waiter`, `kitchen`
- `app/Models/Category.php` — categorias del menu, scope `activeDishes()`
- `app/Models/Dish.php` — platos con `image` (storage/public), `available`, `syncAvailabilityFromIngredients()`
- `app/Models/RestaurantTable.php` — mesas (tabla `tables`), `active`, `qr_code`
- `app/Models/Order.php` — pedidos, estados: `pending`, `preparing`, `ready`, `delivered`
- `app/Models/Ingredient.php` — inventario, `stock`, `min_stock`, `syncDishesAvailability()`

### Controladores centrales
- `Central/HomeController.php`
- `Central/TenantRegisterController.php` — crea tenant + usuario owner, redirige con SweetAlert

### Controladores tenant
- `AuthController` — login con redireccion por rol, logout
- `DashboardController` — KPIs del dia, top platos, grafico ventas por hora, pedidos activos
- `MenuController` — CRUD categorias y platos con imagen upload (storage/public/dishes/{tenant})
- `OrderController` — lista pedidos, update estado, KDS (kitchen display)
- `TableController` — CRUD mesas
- `WaiterController` — panel mesero: ver mesas, tomar pedido con user_id
- `StaffController` — CRUD equipo (meseros y cocina), solo accesible por owner
- `ReportController` — reportes con filtros de periodo, export CSV
- `InventoryController` — CRUD ingredientes, restock, vinculacion a platos
- `SettingsController` — toggle is_open del restaurante

### Controladores publicos
- `Public/MenuPublicController.php` — menu publico, crear pedido, estado pedido, verifica is_open

### Middleware
- `app/Http/Middleware/CheckRole.php` — alias `role`, uso: `role:owner`, `role:owner,waiter`
- Registrado en `bootstrap/app.php`

### Rutas
- `routes/web.php` — rutas centrales (/, /registro)
- `routes/tenant.php` — todas las rutas del panel y menu publico, agrupadas por rol

### Migraciones
- `database/migrations/` — centrales
- `database/migrations/tenant/` — se ejecutan en cada schema al crear un tenant nuevo

---

## Sistema de roles

| Rol | Redireccion al login | Acceso |
|-----|---------------------|--------|
| `owner` | `/admin/dashboard` | Todo el panel completo |
| `waiter` | `/admin/mesero` | Solo panel de tomar pedidos por mesa |
| `kitchen` | `/admin/cocina` | Solo KDS (panel de cocina) |

Middleware `CheckRole` protege cada grupo de rutas. El sidebar del layout tenant muestra solo las opciones del rol activo.

---

## Flujo de pedidos

### Desde QR (cliente)
1. Escanea QR de la mesa → `/{tenant}/menu?mesa={table_id}`
2. Si `is_open = false` → muestra `public/closed.blade.php`
3. Agrega platos, confirma → `POST /{tenant}/menu/pedido` con `table_id`
4. Se crea `Order` con `status=pending`, se descuenta inventario automaticamente
5. Cliente ve estado en tiempo real en `/{tenant}/menu/pedido/{order}/estado`

### Desde panel (mesero/dueno)
1. Va a `/admin/mesero`, ve grid de mesas con indicador de pedidos activos
2. Selecciona mesa → menu con controles de cantidad + foto del plato
3. Confirma → `POST /admin/mesero/mesa/{table}/pedido` con `user_id = auth()->id()`

---

## Plan de trabajo

### COMPLETADO

#### Infraestructura base
- [x] Modelo Tenant con schema-based tenancy (PostgreSQLSchemaManager)
- [x] Migraciones centrales y de tenant
- [x] Rutas path-based (`/{tenant}/admin/...`)
- [x] Auth guard con TenantUser
- [x] Deploy en Railway con SESSION_DRIVER=cookie y trustProxies

#### Panel de administracion (owner)
- [x] Dashboard con KPIs del dia (ingresos, pedidos, ticket promedio, pendientes)
- [x] Grafico de ventas por hora del dia (Chart.js)
- [x] Top 5 platos mas vendidos del dia con foto e ingresos
- [x] Cierre del dia con export CSV directo
- [x] Accesos rapidos a cocina y pedidos desde el dashboard
- [x] Toggle abierto/cerrado del restaurante (is_open en tabla tenants)

#### Menu
- [x] CRUD categorias con nombre y orden de aparicion
- [x] CRUD platos con nombre, descripcion, precio, categoria
- [x] Upload de imagen por plato (storage/public/dishes/{tenant}), preview antes de subir
- [x] Opcion de eliminar/reemplazar foto en edicion
- [x] Toggle disponible/no disponible por plato
- [x] Miniatura de plato en la lista del menu admin

#### Mesas y QR
- [x] CRUD de mesas (nombre, activa/inactiva)
- [x] QR generado en el navegador con QRCode.js (sin paquete PHP)
- [x] URL del QR: `/{tenant}/menu?mesa={table_id}`
- [x] Boton "Imprimir QR" abre ventana con QR grande y dispara window.print()
- [x] Toggle activa/inactiva por mesa
- [x] Confirmacion de eliminacion con SweetAlert

#### Pedidos
- [x] Lista de pedidos del dia ordenados por estado
- [x] Cambio de estado con select + boton actualizar
- [x] Fecha en espanol Colombia (America/Bogota) via Intl del browser
- [x] Indicador visual de estado (colores por badge)

#### Cocina (KDS)
- [x] Panel de cocina con cards por pedido
- [x] Borde de color: amarillo=pendiente, azul=preparando
- [x] Boton "Iniciar" (pending → preparing) y "Listo" (preparing → ready)
- [x] Polling automatico via fetch cada 8 segundos
- [x] Indicador verde "En vivo" + timestamp ultima actualizacion
- [x] Al marcar listo, el pedido desaparece del KDS

#### Equipo y roles
- [x] Modelo TenantUser con roles: owner, waiter, kitchen
- [x] Middleware CheckRole con alias `role`
- [x] Login redirige automaticamente segun rol
- [x] CRUD equipo (meseros y cocina) — solo accesible por owner
- [x] Avatar con iniciales + badge de rol en lista de equipo
- [x] URL de acceso compartible en el footer de la vista equipo
- [x] Sidebar adaptativo: cada rol ve solo sus opciones

#### Panel mesero
- [x] Grid de mesas activas con indicador de pedido activo
- [x] Formulario de pedido: browse del menu con fotos, controles +/-, resumen lateral
- [x] Barra superior flotante con contador y total mientras selecciona
- [x] Pedido creado con user_id del mesero autenticado
- [x] Boton de envio deshabilitado hasta tener al menos 1 item

#### Reportes
- [x] Selector de periodo: hoy, semana, mes, personalizado
- [x] KPIs: ingresos, pedidos, ticket promedio, entregados
- [x] Grafico de barras de ingresos por dia (Chart.js)
- [x] Tabla top 10 platos mas vendidos con unidades e ingresos
- [x] Distribucion de pedidos por estado con barras de progreso
- [x] Export CSV con BOM para Excel, datos completos del periodo

#### Inventario
- [x] CRUD de ingredientes con unidad, stock actual y stock minimo
- [x] Restock (suma al stock existente)
- [x] Vinculacion de ingredientes a platos con cantidad por receta
- [x] Auto-desactivacion de plato cuando stock <= min_stock al hacer pedido
- [x] Auto-activacion de plato cuando se restockea por encima del minimo

#### UX global
- [x] SweetAlert2 global en layout tenant (toasts top-right para success/error/warning)
- [x] Confirmaciones destructivas con SweetAlert en todo el sistema (eliminar plato, categoria, mesa, usuario)
- [x] SVG (Heroicons outline) en lugar de emojis en todas las vistas
- [x] Fechas en espanol Colombia en dashboard y pedidos
- [x] Formularios de crear/editar centrados con max-w-lg mx-auto
- [x] SweetAlert en registro: muestra URL del panel y redirige automaticamente

#### Menu publico (cliente)
- [x] Vista cerrado (public/closed.blade.php) cuando is_open = false
- [x] Muestra nombre de la mesa en el header al escanear QR
- [x] table_id se pasa automaticamente en el pedido
- [x] Carrito con contador, resumen y total
- [x] Vista de estado del pedido tras confirmar

---

### PENDIENTE — Por orden de prioridad

#### Alta prioridad
- [ ] **Notificaciones en tiempo real al cliente** — cuando el pedido pasa a "listo", el cliente recibe aviso en su pantalla sin recargar (WebSockets o polling en order-status.blade.php)
- [ ] **Editar pedido antes de enviarlo a cocina** — un mesero pueda agregar/quitar items a un pedido pendiente
- [ ] **Historial de pedidos por mesa** — en el panel mesero, ver los pedidos anteriores de una mesa en el mismo dia

#### Media prioridad
- [ ] **Whitelabel / dominio propio** — el restaurante usa `menu.mirestaurante.com` en vez de lacarta.app
- [ ] **Multiples idiomas en menu publico** — espanol/ingles toggle en la vista del cliente
- [ ] **App PWA instalable** — manifest.json + service worker para instalar el panel como app en movil
- [ ] **Cierre de sesion automatico** — meseros y cocina con sesion de X horas

#### Baja prioridad (Tier 3)
- [ ] **Pagos integrados (Wompi/PSE)** — el cliente paga desde la mesa antes de enviar el pedido
- [ ] **Modo multicaja** — mas de un punto de venta por restaurante
- [ ] **Estadisticas por mesero** — cuantos pedidos tomo cada mesero, su ticket promedio

---

## Notas importantes

### Railway / Produccion
- `SESSION_DRIVER=cookie` en variables de Railway (no database)
- `APP_URL=https://lacarta-production.up.railway.app`
- `trustProxies(at: '*')` en `bootstrap/app.php`
- Storage de imagenes NO persiste entre deploys en Railway — migrar a S3 o disco persistente antes de ir a produccion real

### Storage / Imagenes
- `php artisan storage:link` ya ejecutado en local
- Imagenes en `storage/app/public/dishes/{tenant_id}/`
- Acceso: `asset('storage/' . $dish->image)`

### Convenciones del proyecto
- No usar `Co-Authored-By` en commits
- No usar emojis en vistas — usar SVG Heroicons (outline, stroke-width 1.5 o 2)
- SweetAlert2 para todas las confirmaciones destructivas y notificaciones
- Fechas siempre en espanol Colombia via `Intl` del browser, no `now()` de PHP (servidor en UTC)
- `tenant('id')` devuelve el slug del tenant (ej: `miprincesa`)
- Al crear tenant nuevo, las migraciones de `database/migrations/tenant/` se ejecutan automaticamente
- Para correr migraciones tenant en tenants existentes: `php artisan tenants:migrate --tenants=slug`
