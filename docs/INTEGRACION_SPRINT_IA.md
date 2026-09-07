# Resumen de sprint con OpenRouter

## Modelo de datos

Un `Sprint` pertenece a un `Proyecto`. Las tarjetas existentes se vinculan a
un sprint mediante `tareas.sprint_id`, que es nullable para no romper los datos
actuales mientras se asignan las tarjetas.

La migración agrega `sprints.resumen_ia` como caché persistente. El endpoint no
envía el modelo Eloquent completo a OpenRouter: solamente incluye nombre,
descripción, estado y fechas del sprint, más `titulo`, `descripcion` y
`estado` de cada tarjeta y totales calculados por el servidor.

## Configuración local

Agregar en `.env` —sin subir nunca la clave—:

```env
OPENROUTER_ENABLED=true
OPENROUTER_API_KEY=clave-real-local
OPENROUTER_MODEL=meta-llama/llama-3.3-70b-instruct:free
OPENROUTER_FALLBACK_MODEL=qwen/qwen-2.5-coder-32b-instruct:free
```

El resto de los valores tiene defaults en `config/services.php`. Después de
cambiar variables de entorno, ejecutar:

```bash
php artisan optimize:clear
php artisan migrate
```

## Endpoint

```text
POST /sprints/{sprint}/resumen-ia
```

La ruta usa la autenticación de sesión y requiere uno de estos roles:
`Jefe`, `PM`, `PO` o `Programador`. Al ser una ruta web, una llamada desde
JavaScript debe enviar el token CSRF habitual.

Body opcional:

```json
{
  "forzar": true
}
```

Sin `forzar`, si el sprint ya tiene `resumen_ia`, se devuelve el valor guardado
sin consumir una llamada de OpenRouter. Con `forzar: true` se genera y guarda un
nuevo resumen.

Respuesta nueva (`201`):

```json
{
  "sprint_id": 1,
  "resumen": "...",
  "modelo": "meta-llama/llama-3.3-70b-instruct:free",
  "cacheado": false
}
```

Respuesta cacheada (`200`) mantiene el mismo formato y marca `cacheado` como
`true`. Una falla de configuración responde `503`; una falla de OpenRouter o
de red responde `502`, sin exponer la clave ni el detalle de la API.

## Ejemplo de asociación de tarjetas

La creación y edición de sprints queda fuera de este primer endpoint. Una vez
creado un sprint, las tarjetas se asocian con Eloquent:

```php
$sprint = Sprint::create([
    'proyecto_id' => $proyecto->id,
    'nombre' => 'Sprint 2',
    'descripcion' => 'Buscador, reportes y limpieza.',
    'fecha_inicio' => '2026-08-31',
    'fecha_fin' => '2026-09-11',
    'estado' => 'en_progreso',
]);

Tarea::where('proyecto_id', $proyecto->id)
    ->update(['sprint_id' => $sprint->id]);
```

La solicitud usa una sola llamada directa y un máximo de 900 tokens de salida,
en línea con el dimensionamiento del informe de carga del sprint. Si el modelo
principal responde con rate limit (`429`), un error transitorio del proveedor o
un error de conexión, se intenta el modelo fallback.
