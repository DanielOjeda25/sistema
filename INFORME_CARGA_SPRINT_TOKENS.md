# Especificación Técnica para Agente: Análisis de Sprint con IA (OpenRouter Free Tier)

> **Proyecto:** grupo-daniel / CRUZNEGRA
> **Destinatario:** Agente de Implementación / Dev Backend
> **Fecha:** 2026-09-04
> **Objetivo:** Implementar la generación automática de un resumen ejecutivo de Sprint en texto plano para clientes no técnicos, ejecutándose en Laravel MVC tradicional mediante la API gratuita de OpenRouter.

---

## 1. Contexto de la Aplicación y Stack

* **Framework:** Laravel (MVC tradicional, monolítico).
* **Vistas:** Blade (sin Livewire ni SPAs independientes).
* **Rutas:** `routes/web.php` (no se utiliza `api.php`, Sanctum ni Passport).
* **Autenticación:** Sesiones tradicionales de Laravel y protección `@csrf`.
* **Frontend interactivo:** JavaScript puntual (`fetch`/`axios` o envío de formularios estándar).
* **Origen de datos:** Las tareas ya están almacenadas en la base de datos relacional y asociadas al Sprint vía Eloquent (`$sprint->tareas`).

---

## 2. Dimensionamiento de Tokens y Payload (DB vs API)

Al extraer las tareas directamente de la base de datos (descartando archivos markdown y bloques de código de especificaciones técnicas), la carga disminuye sustancialmente:

| Métrica                          | Valor Estimado (Sprint de 9 tareas)                           |
| -------------------------------- | ------------------------------------------------------------- |
| **Campos extraídos por tarea**   | `titulo`, `descripcion`, `estado`                             |
| **Caracteres por tarea**         | ~300 – 600 chars                                              |
| **Caracteres totales Sprint**    | ~3.000 – 5.500 chars                                          |
| **Tokens de entrada (Input)**    | **~800 – 1.500 tokens**                                       |
| **Tokens de salida (Output)**    | **~500 – 900 tokens** (texto plano para cliente)              |
| **Arquitectura de llamadas**     | **1 sola llamada directa** (se descarta pipeline fragmentado) |
| **Tiempo de respuesta esperado** | 3 a 6 segundos                                                |
| **Costo API**                    | **$0.00 USD** (OpenRouter `:free`)                            |

---

## 3. Parámetros de OpenRouter (Free Tier)

* **Endpoint:** `https://openrouter.ai/api/v1/chat/completions`
* **Modelo Principal:** `meta-llama/llama-3.3-70b-instruct:free`
* **Modelo Fallback:** `qwen/qwen-2.5-coder-32b-instruct:free` o `deepseek/deepseek-r1:free`
* **Headers requeridos:**
  * `Authorization: Bearer {OPENROUTER_API_KEY}`
  * `HTTP-Referer: http://localhost` (o dominio de la app)
  * `X-Title: CRUZNEGRA-Sprint-Analysis`
* **Límites operativos:** 20 RPM / 50–200 RPD. Al requerir solo 1 llamada por sprint, el consumo de cuota diaria es <1%.

---

## 4. Persistencia en Base de Datos

Para evitar llamadas redundantes a la API en cada recarga de página (`F5`), el resultado se almacena en la entidad del sprint.

### Migración requerida
```php
Schema::table('sprints', function (Blueprint $table) {
    $table->mediumText('resumen_ia')->nullable()->after('estado');
});
```

---

## 5. Implementación Backend (Controlador Laravel)

### Reglas para el Agente:
1. **No serializar el modelo completo:** No enviar `$sprint->tareas->toJson()` a la IA para no filtrar IDs, timestamps ni metadatos irrelevantes.
2. **Control de Caché/Persistencia:** Si `$sprint->resumen_ia` ya existe y no se solicita forzar regeneración, devolver el texto existente.
3. **Manejo de Fallbacks y Errores:** Capturar códigos `429` (Rate Limit) o fallas de red para intentar con el modelo secundario antes de fallar.

### Ejemplo de Lógica en Controlador (`SprintController.php`):
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sprint;

public function generarResumenIa(Request $request, Sprint $sprint)
{
    // Si ya existe y no se fuerza regeneración, retornar existente
    if ($sprint->resumen_ia && !$request->has('forzar')) {
        return back()->with('resumen_ia', $sprint->resumen_ia);
    }

    // Filtrar y preparar contexto semántico de las tareas
    $tareasContexto = $sprint->tareas->map(function ($t) {
        return "- [Estado: {$t->estado}] Título: {$t->titulo}. Detalle: {$t->descripcion}";
    })->implode("
");

    $systemPrompt = "Eres un asistente de gestión de proyectos para clientes finales de la plataforma CRUZNEGRA. "
        . "Tu objetivo es redactar un reporte claro, ejecutivo y comprensible para una persona sin conocimientos técnicos ni de programación. "
        . "Reglas: "
        . "1. Entrega únicamente TEXTO PLANO legible, sin bloques de código, sin etiquetas HTML y sin markdown pesado. "
        . "2. Resume las tareas en lenguaje funcional (qué beneficios o funciones recibe el usuario/cliente). "
        . "3. Agrupa por: Resumen general, Mejoras principales, y Próximos pasos.";

    $userPrompt = "Sprint: {$sprint->nombre}
"
        . "Fechas: {$sprint->fecha_inicio} al {$sprint->fecha_fin}

"
        . "Tareas registradas:
{$tareasContexto}

"
        . "Genera el informe ejecutivo en texto plano para el cliente.";

    // Intentar llamada a modelo principal con fallback
    $modelos = [
        'meta-llama/llama-3.3-70b-instruct:free',
        'qwen/qwen-2.5-coder-32b-instruct:free'
    ];

    $resultadoTexto = null;

    foreach ($modelos as $modelo) {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => 'CRUZNEGRA',
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $modelo,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.4,
                'max_tokens' => 1200,
            ]);

            if ($response->successful()) {
                $resultadoTexto = $response->json('choices.0.message.content');
                break;
            }
        } catch (\Exception $e) {
            continue; // Intentar fallback
        }
    }

    if (!$resultadoTexto) {
        return back()->withErrors(['error_ia' => 'No fue posible generar el informe con el proveedor gratuito. Intente nuevamente en unos minutos.']);
    }

    // Persistir resultado
    $sprint->update(['resumen_ia' => $resultadoTexto]);

    return back()->with('success', 'Informe generado correctamente.');
}
```

---

## 6. Integración en Vistas Blade

### Definición de Ruta (`routes/web.php`)
```php
Route::post('/sprints/{sprint}/generar-resumen-ia', [SprintController::class, 'generarResumenIa'])
    ->name('sprints.generarResumenIa')
    ->middleware(['auth']);
```

### Renderizado Seguro en Blade (`resources/views/sprints/show.blade.php`)
Para preservar los saltos de línea del texto plano sin riesgos de inyección XSS:

```blade
<div class="card my-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Informe Ejecutivo para Cliente</h5>

        <form action="{{ route('sprints.generarResumenIa', $sprint) }}" method="POST">
            @csrf
            @if($sprint->resumen_ia)
                <input type="hidden" name="forzar" value="1">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Regenerar Informe</button>
            @else
                <button type="submit" class="btn btn-sm btn-primary">Generar Informe con IA</button>
            @endif
        </form>
    </div>

    <div class="card-body">
        @if($sprint->resumen_ia)
            <div style="white-space: pre-wrap; font-family: inherit; line-height: 1.6; background: #f8f9fa; padding: 1.25rem; border-radius: 6px;">
                {{ $sprint->resumen_ia }}
            </div>
        @else
            <p class="text-muted mb-0">No se ha generado un informe ejecutivo para este ciclo todavía.</p>
        @endif
    </div>
</div>
```

---

## 7. Formato de Salida Esperado (Ejemplo de Texto Plano)

```text
======================================================================
REPORTE DE PROGRESO Y ALCANCE DEL SPRINT — SISTEMA CRUZNEGRA
======================================================================

Estimado cliente,

Le presentamos el resumen del ciclo actual de trabajo en el sistema:

1. MEJORAS INCORPORADAS
• Agilidad de búsqueda: Se sumaron filtros para localizar registros al instante en Proyectos, Tareas, Solicitudes y Facturación.
• Panel de Indicadores: La dirección ahora cuenta con métricas claras sobre ingresos, tareas activas y estado general del negocio en tiempo real.
• Rendimiento y Estabilidad: Se ejecutaron pruebas completas para garantizar que los procesos de registro y facturación operen de forma fluida.

2. ESTADO DEL EQUIPO
• Tareas programadas: 9 objetivos cubiertos.
• Nivel de avance: Conforme a lo planificado para este ciclo.

3. PRÓXIMOS PASOS
Se continuará con la validación de carga de datos reales y optimizaciones visuales para acceso móvil.
