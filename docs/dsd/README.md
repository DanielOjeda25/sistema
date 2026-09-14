# DSD corregidos — Casos de uso reales de CRUZNEGRA

Corrección de los DSD del trabajo (`LECCION CRUZ NEGRA.pdf`): los diagramas
originales idealizaban el código; estos reflejan **el código real de `main`**.

| PNG | CU | Correcciones respecto al PDF |
| --- | --- | --- |
| `01_pm_crea_proyecto.png` | PM crea proyecto | `descripcion` era omitido; el redirect ahora es condicional (`desde_modal ? back() : index`) por el CRUD en modales |
| `02_programador_ve_tareas.png` | Programador ve tareas | `paginate(15)` (no 10); faltaban el scope `visiblePara` (scoping por cliente) y los filtros de búsqueda/estado |
| `03_edita_tarea_tablero.png` | Edición de tarea | **Campos corregidos**: la tarea usa `titulo`, `fecha_limite`, `asignado_a` (no `nombre`/`fecha_inicio`/`asignado_id`); suma `sprint_id`; la edición real es AJAX desde el tablero con respuesta JSON (422 con errores / 200 con la tarea) |
| `04_pm_elimina_tarea.png` | Baja de tarea | La baja real es por DELETE AJAX con `{ok: true}`; el 404 lo da el Route Model Binding antes del controller |
| `05_jefe_aprueba_solicitud.png` | Aprobación de solicitud | Valores reales del enum (`pendiente|aprobada|rechazada`); quien aprueba es Jefe/PM/PO |
| `06_po_gestiona_entregables.png` | Gestión de entregables IA | Enum real `borrador|revisado|aprobado`; `paginate(15)`; redirección al index con flash |

## Regenerar

Los fuentes PlantUML están en [`src/`](src/) y los PNG en [`png/`](png/):

```bash
java -jar plantuml.jar -tpng src/*.puml -o ../png
```

`plantuml.jar` no se sube al repositorio.
