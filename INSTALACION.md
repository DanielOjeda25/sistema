# CRUZNEGRA — Instalación del Proyecto

> Guía para correr el proyecto desde cero en una máquina nueva.
> Asume que estás en **Windows + Laragon** (el setup del equipo).
> Para construir el modelo de datos (tablas), ver [CRUZNEGRA_TAREAS.md](CRUZNEGRA_TAREAS.md).

---

## REQUISITOS PREVIOS

Antes de arrancar tenés que tener instalado:

| Software | Versión mínima | Cómo verificar |
|----------|----------------|----------------|
| **Laragon** | Full | Abrir Laragon → debe arrancar Apache + MySQL |
| **PHP** | 8.2 o superior | `php -v` |
| **Composer** | 2.x | `composer -V` |
| **Node.js** | 18 o superior | `node -v` |
| **npm** | viene con Node | `npm -v` |
| **Git** | cualquiera reciente | `git --version` |

> Laragon ya trae PHP, MySQL y Composer. Si te falta Node, bajalo de [nodejs.org](https://nodejs.org) (versión LTS).

---

## PASO 1 — Clonar el repositorio

Parado en `c:\laragon\www\`:

```bash
git clone https://github.com/DanielOjeda25/sistema.git
cd sistema
```

---

## PASO 2 — Instalar dependencias de PHP

```bash
composer install
```

Esto descarga todo lo que está en `composer.json` (Laravel 12, Spatie Permission, Breeze, Auditing, etc.) dentro de la carpeta `vendor/`.

> Si tira error de extensiones (ej. `ext-fileinfo`), abrir Laragon → Menú → PHP → Extensions → habilitar la que falta.

---

## PASO 3 — Instalar dependencias de JavaScript

```bash
npm install
```

Esto descarga Vite, TailwindCSS, Alpine.js y demás en `node_modules/`.

---

## PASO 4 — Crear el archivo `.env`

```bash
copy .env.example .env
```

> En PowerShell también funciona `Copy-Item .env.example .env`.

---

## PASO 5 — Configurar la base de datos en `.env`

Abrir el archivo `.env` recién creado y **modificar** estas líneas:

**Buscar esta línea:**
```env
DB_CONNECTION=sqlite
```

**Reemplazar por:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cruznegra
DB_USERNAME=root
DB_PASSWORD=
```

> `DB_DATABASE=cruznegra` es el nombre que le vamos a poner a la base. Si querés otro, anotalo.
> En Laragon el usuario por defecto es `root` sin contraseña.

---

## PASO 6 — Crear la base de datos en MySQL

1. Abrir Laragon → botón **Database** (abre HeidiSQL).
2. Conectar con usuario `root` (sin contraseña).
3. Click derecho sobre la lista de bases → **Create new** → **Database**.
4. Nombre: `cruznegra` (el mismo que pusiste en `.env`).
5. Charset: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
6. Aceptar.

> Alternativa por línea de comandos:
> ```bash
> mysql -u root -e "CREATE DATABASE cruznegra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
> ```

---

## PASO 7 — Generar la clave de la aplicación

```bash
php artisan key:generate
```

Esto rellena `APP_KEY=` en tu `.env` con una clave única. **No la compartas con nadie.**

---

## PASO 8 — Ejecutar las migraciones

```bash
php artisan migrate
```

Esto crea **todas las tablas** del sistema (las de Laravel + las del proyecto que están definidas en [CRUZNEGRA_TAREAS.md](CRUZNEGRA_TAREAS.md)).

### ¿Cómo saber si funcionó?

El output muestra cada migración con `DONE`. Si alguna tira error, **parar y avisar** — no avanzar.

### Si querés empezar desde cero (borra todo)

```bash
php artisan migrate:fresh
```

> Solo en desarrollo. **Nunca en producción.**

---

## PASO 9 — Levantar el proyecto

### Terminal 1 — Servidor Laravel

```bash
php artisan serve
```

Abre el sitio en [http://localhost:8000](http://localhost:8000).

> Alternativa: usar la URL que arma Laragon automáticamente (algo tipo `http://sistema.test`).

### Terminal 2 — Vite (para CSS/JS)

```bash
npm run dev
```

Vite queda escuchando cambios en assets y los recompila al vuelo. **Dejar la terminal abierta mientras trabajás.**

---

## VERIFICAR QUE TODO FUNCIONA

1. Abrir [http://localhost:8000](http://localhost:8000) en el navegador.
2. Debería verse la landing del proyecto con el menú de login/registro de Breeze.
3. Click en **Register** → crear un usuario de prueba.
4. Si entrás al dashboard sin errores → **listo, está todo funcionando**.

---

## PROBLEMAS COMUNES

| Error | Causa probable | Solución |
|-------|---------------|----------|
| `SQLSTATE[HY000] [1045] Access denied` | Mal usuario/contraseña en `.env` | Revisar `DB_USERNAME` y `DB_PASSWORD` |
| `SQLSTATE[HY000] [1049] Unknown database` | La DB no existe en MySQL | Crearla en HeidiSQL (PASO 6) |
| `Class "Pdo" not found` | Extensión PHP deshabilitada | Laragon → Menú → PHP → Extensions → habilitar `pdo_mysql` |
| `vite manifest not found` | Falta correr `npm run dev` o `npm run build` | Ejecutar uno de los dos |
| `Permission denied` al ejecutar `artisan` | Permisos | En Windows con Laragon no debería pasar — abrir terminal como Administrador |
| Cambiaste el `.env` y no se aplica | Caché de config | `php artisan config:clear` |

---

## RESUMEN — COMANDOS EN ORDEN

```bash
# 1. Bajar el código
git clone https://github.com/DanielOjeda25/sistema.git
cd sistema

# 2. Dependencias
composer install
npm install

# 3. Configuración
copy .env.example .env
# (editar .env — cambiar DB_CONNECTION a mysql y completar datos)

# 4. Base de datos
# (crear DB "cruznegra" en HeidiSQL — ver PASO 6)
php artisan key:generate
php artisan migrate

# 5. Levantar
php artisan serve         # terminal 1
npm run dev               # terminal 2
```

---

## SIGUIENTE PASO

Una vez que el proyecto corre, ver [CRUZNEGRA_TAREAS.md](CRUZNEGRA_TAREAS.md) para entender qué tablas se están construyendo y en qué paso va el equipo.
