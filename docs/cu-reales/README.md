# CU Reales — recorridos con capturas reales del sistema

Complemento de los DSD (`docs/dsd/`): cada CU muestra **con pasos numerados**
cómo se ve y se ejecuta el caso en la web real (capturas de `localhost:8000`).

| PNG | CU | Usuario |
| --- | --- | --- |
| `png/cu1_pasos.png` | El PM crea un proyecto | Laura (PM) |
| `png/cu2_pasos.png` | El Programador consulta las tareas (index + filtro + detalle) | Sofía (Programador) |
| `png/cu3_pasos.png` | Edición de tarea desde el tablero (AJAX) | Laura (PM) |
| `png/cu4_pasos.png` | El PM elimina una tarea (DELETE AJAX + confirmación) | Laura (PM) |
| `png/cu5_pasos.png` | El Jefe aprueba una solicitud de cambio | Roberto (Jefe) |
| `png/cu6_pasos.png` | El PO gestiona los entregables IA | Diego (PO) |

- `capturas/` — capturas crudas de cada paso.
- `componer.php` — genera los PNG numerados a partir de las capturas
  (`php docs/cu-reales/componer.php`; usa la extensión GD de PHP).

Notas de fidelidad respecto al PDF de la materia: la edición/baja de tareas es
AJAX desde el tablero (no submit clásico), los formularios son modales (no
páginas aparte), la creación de proyectos redirige de vuelta al listado con
filtros, y el tablero es de solo lectura para el Programador.
