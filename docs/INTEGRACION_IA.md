# Integración del modelo de IA para informes de proyecto

El flujo de datos, permisos, borradores, aprobación y publicación ya está
implementado. La persona responsable de integrar la IA solamente debe crear un
adaptador que implemente el contrato existente.

## Punto de integración

Contrato:

```text
app/Contracts/ProjectReportGenerator.php
```

Implementación local de referencia:

```text
app/Services/AI/FakeProjectReportGenerator.php
```

El método que debe implementar es:

```php
public function generate(array $context): string;
```

`$context` ya llega filtrado y contiene:

- datos generales del proyecto y del Cliente;
- porcentaje y cantidades calculadas por Laravel;
- tareas y sus estados;
- hitos y sus estados;
- actualizaciones marcadas como visibles para el Cliente.

No contiene facturación, notas internas, credenciales ni datos de otros
clientes. No se debe volver a consultar la base desde el adaptador.

## Crear el adaptador real

Ejemplo de nombre:

```text
app/Services/AI/OpenAIProjectReportGenerator.php
```

Estructura mínima:

```php
<?php

namespace App\Services\AI;

use App\Contracts\ProjectReportGenerator;

class OpenAIProjectReportGenerator implements ProjectReportGenerator
{
    public function generate(array $context): string
    {
        // 1. Convertir $context en el mensaje para el modelo.
        // 2. Ejecutar la solicitud con el SDK del proveedor.
        // 3. Devolver solamente el texto final del informe.
    }
}
```

El prompt debe pedir un informe claro, sin inventar información, diferenciando
avances, pendientes, problemas y próximos pasos. Las cifras de progreso que
llegan en `$context['progreso']` son la fuente oficial y no deben recalcularse
con el modelo.

## Configuración

En el `.env` local:

```env
AI_REPORTS_ENABLED=true
AI_PROVIDER=openai
AI_MODEL=nombre-del-modelo
AI_API_KEY=clave-del-proveedor
AI_PROMPT_VERSION=v1
AI_REPORT_GENERATOR="App\Services\AI\OpenAIProjectReportGenerator"
```

Después:

```bash
php artisan optimize:clear
```

Nunca escribir ni subir la clave real en `.env.example`, código PHP, archivos
BAT, pruebas, capturas o commits.

## Flujo ya implementado

1. Un usuario interno registra actualizaciones desde el detalle del proyecto.
2. Solo las actualizaciones marcadas para el Cliente entran al contexto.
3. “Generar borrador” llama a `ProjectReportGenerator`.
4. El resultado se guarda como borrador en `entregables_ia` junto con una copia
   del contexto, modelo y versión del prompt.
5. Jefe, PM o PO revisan y publican el informe.
6. El Cliente solamente puede ver informes aprobados y publicados de su propia
   empresa.
7. Si el proveedor falla, el sistema guarda el mensaje de error y no publica
   ningún contenido.

## Verificación

Ejecutar:

```bash
php artisan test tests/Feature/ProjectAIReportTest.php
php artisan test
```

También probar manualmente:

- generar un borrador como `jefe@example.com`;
- confirmar que `cliente@example.com` todavía no lo vea;
- aprobarlo como Jefe, PM o PO;
- confirmar que el Cliente ahora sí lo vea;
- retirar el informe y confirmar que desaparezca para el Cliente;
- provocar un error controlado del proveedor y confirmar que no se publique.
