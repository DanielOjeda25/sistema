# Instalación con Docker

El proyecto se ejecuta con dos contenedores persistentes:

- `daniel-grupo-php`: Laravel, PHP 8.3, Composer y Node.
- `daniel-grupo-mysql`: MySQL 8.4.

El archivo `compose.yaml` usa el proyecto Compose `daniel-grupo`, la red
`daniel-grupo-network` y el volumen `daniel-grupo-mysql-data`.

## Primer arranque

Desde la raíz del proyecto, con Docker Desktop iniciado:

```powershell
docker compose up -d --build
docker compose exec php php artisan key:generate --force
docker compose exec php php artisan migrate --seed
docker compose exec php npm install
docker compose exec php npm run build
```

La aplicación queda disponible en `http://localhost:8000`. El puerto de MySQL
del host es `3307`; Laravel se conecta internamente usando `DB_HOST=mysql` y
`DB_PORT=3306`.

## Uso diario

```powershell
docker compose up -d
docker compose exec php php artisan test
docker compose logs -f php
```

Para desarrollo con recarga de Vite, ejecutar en otra terminal:

```powershell
docker compose exec php npm run dev -- --host=0.0.0.0
```

## OpenRouter

Configurar en `.env` la clave y habilitar el proveedor:

```env
OPENROUTER_ENABLED=true
OPENROUTER_API_KEY=clave-real-local
```

Luego limpiar la configuración:

```powershell
docker compose exec php php artisan optimize:clear
```

## Detener sin borrar datos

```powershell
docker compose stop
```

`docker compose down` elimina los contenedores, pero conserva el volumen de
MySQL. No ejecutar `docker compose down -v` si se necesitan los datos locales.
