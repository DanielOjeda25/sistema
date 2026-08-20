# CRUZNEGRA — Modelo Relacional (según migraciones)

> Diagrama entidad-relación que refleja el esquema **realmente implementado** en la base
> de datos `cruznegra` (las 8 migraciones de negocio). Versión simple, sin los campos
> extra del documento de análisis.
>
> **Cómo verlo en VS Code:** instalá la extensión *"Markdown Preview Mermaid Support"*
> y abrí la vista previa (Ctrl+Shift+V). Para el código puro, copiá el bloque `mermaid`
> a un archivo `.mmd` y usá la extensión *"Mermaid Preview"*.
>
> Convenciones: `PK` = clave primaria · `FK` = clave foránea · `UK` = único ·
> todas las relaciones son **1 : N** (`||--o{`). Todas las tablas tienen además
> `created_at` y `updated_at`. Los roles (Jefe, PM, PO, Programador, Cliente) se manejan
> con las tablas de Spatie Permission, no con una columna en `users`.

```mermaid
erDiagram
    clientes ||--o{ proyectos          : "cliente_id"
    users    ||--o{ proyectos          : "pm_id"

    proyectos ||--o{ solicitudes_cambio : "proyecto_id"
    users     ||--o{ solicitudes_cambio : "solicitado_por"

    proyectos        ||--o{ tareas      : "proyecto_id"
    users            ||--o{ tareas      : "asignado_a"
    solicitudes_cambio ||--o{ tareas    : "solicitud_cambio_id (nullable)"

    proyectos ||--o{ hitos              : "proyecto_id"

    proyectos ||--o{ entregables_ia     : "proyecto_id"
    users     ||--o{ entregables_ia     : "generado_por"

    proyectos ||--o{ facturas           : "proyecto_id"
    users     ||--o{ facturas           : "emitida_por"

    clientes {
        int      id          PK
        string   nombre
        string   apellido
        string   email       UK
        string   telefono
        string   empresa
        string   estado      "activo | inactivo"
    }

    users {
        int      id          PK
        string   name
        string   apellido
        string   email       UK
        string   estado      "activo | inactivo"
        string   password
    }

    proyectos {
        int      id                  PK
        int      cliente_id          FK
        int      pm_id               FK "users"
        string   nombre
        text     descripcion
        date     fecha_inicio
        date     fecha_fin_estimada
        string   estado              "pendiente | en_progreso | completado | cancelado"
    }

    solicitudes_cambio {
        int      id              PK
        int      proyecto_id     FK
        int      solicitado_por  FK "users"
        string   titulo
        text     descripcion
        string   estado          "pendiente | aprobada | rechazada"
        string   prioridad       "baja | media | alta"
    }

    tareas {
        int      id                   PK
        int      proyecto_id          FK
        int      asignado_a           FK "users"
        int      solicitud_cambio_id  FK "nullable"
        string   titulo
        text     descripcion
        string   estado               "pendiente | en_progreso | completada | cancelada"
        string   prioridad            "baja | media | alta"
        date     fecha_limite
    }

    hitos {
        int      id              PK
        int      proyecto_id     FK
        string   nombre
        text     descripcion
        date     fecha_objetivo
        bool     completado
    }

    entregables_ia {
        int      id            PK
        int      proyecto_id   FK
        int      generado_por  FK "users"
        string   titulo
        text     contenido
        string   tipo
        string   estado        "borrador | revisado | aprobado"
    }

    facturas {
        int      id                 PK
        int      proyecto_id        FK
        int      emitida_por        FK "users"
        string   numero             UK
        decimal  monto
        date     fecha_emision
        date     fecha_vencimiento
        string   estado             "pendiente | pagada | vencida"
        text     detalle
    }
```

## Relaciones e integridad referencial

- `proyectos` referencia un `clientes` (`cliente_id`) y un usuario PM (`pm_id`).
- `solicitudes_cambio`, `hitos`, `entregables_ia` y `facturas` cuelgan de `proyectos`.
- `tareas.solicitud_cambio_id` es **nullable**: solo se completa cuando la tarea surge
  de una solicitud de cambio.
- Las FK hacia `proyectos`, `clientes` y `users` son `ON DELETE CASCADE`, salvo
  `tareas.solicitud_cambio_id`, que es `ON DELETE SET NULL`.
- Todas las relaciones son **1 : N**; no hay tablas pivote (N:N) entre entidades de negocio.
