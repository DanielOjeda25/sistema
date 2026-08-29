# CRUZNEGRA — Diagrama de Clases (Modelos Eloquent)

> Las 8 clases del modelo (`app/Models/`) con sus métodos de relación
> `belongsTo` / `hasMany`, tal como están implementadas.
>
> **Cómo verlo en VS Code:** extensión *"Markdown Preview Mermaid Support"* +
> vista previa (Ctrl+Shift+V). Para código puro, copiá el bloque `mermaid` a un `.mmd`.
>
> Lectura de las flechas: `Padre "1" --> "*" Hijo`. La etiqueta indica el método
> `hasMany` del padre y, entre corchetes, la FK. El método `belongsTo` inverso
> figura dentro de la clase hija.

```mermaid
---
id: 26c23357-88a0-4b99-8f3e-647b0b694548
---
classDiagram
    class Cliente {
        +proyectos() HasMany
        +usuarios() HasMany
    }
    class User {
        +cliente() BelongsTo
        +esCliente() bool
        +puedeVer(Model modelo) bool
        +proyectosComoPm() HasMany
        +tareasAsignadas() HasMany
        +solicitudesRealizadas() HasMany
        +entregablesGenerados() HasMany
        +facturasEmitidas() HasMany
    }
    class Proyecto {
        +cliente() BelongsTo
        +pm() BelongsTo
        +tareas() HasMany
        +hitos() HasMany
        +entregablesIa() HasMany
        +facturas() HasMany
        +solicitudesCambio() HasMany
    }
    class SolicitudCambio {
        +proyecto() BelongsTo
        +solicitante() BelongsTo
        +tareas() HasMany
    }
    class Tarea {
        +proyecto() BelongsTo
        +scopeVisiblePara(Builder, User) Builder
        +asignado() BelongsTo
        +solicitudCambio() BelongsTo
    }
    class Hito {
        +proyecto() BelongsTo
    }
    class EntregableIA {
        +proyecto() BelongsTo
        +generador() BelongsTo
    }
    class Factura {
        +proyecto() BelongsTo
        +emisor() BelongsTo
    }

    Cliente "1" --> "*" Proyecto : proyectos [cliente_id]
    Cliente "1" --> "*" User : usuarios [cliente_id]
    User "1" --> "*" Proyecto : proyectosComoPm [pm_id]
    User "1" --> "*" Tarea : tareasAsignadas [asignado_a]
    User "1" --> "*" SolicitudCambio : solicitudesRealizadas [solicitado_por]
    User "1" --> "*" EntregableIA : entregablesGenerados [generado_por]
    User "1" --> "*" Factura : facturasEmitidas [emitida_por]
    Proyecto "1" --> "*" Tarea : tareas [proyecto_id]
    Proyecto "1" --> "*" Hito : hitos [proyecto_id]
    Proyecto "1" --> "*" EntregableIA : entregablesIa [proyecto_id]
    Proyecto "1" --> "*" Factura : facturas [proyecto_id]
    Proyecto "1" --> "*" SolicitudCambio : solicitudesCambio [proyecto_id]
    SolicitudCambio "1" --> "*" Tarea : tareas [solicitud_cambio_id]
```

## Resumen de relaciones por clase

| Clase | belongsTo (lado N) | hasMany (lado 1) |
|---|---|---|
| `Cliente` | — | `proyectos`, `usuarios` |
| `User` | `cliente` | `proyectosComoPm`, `tareasAsignadas`, `solicitudesRealizadas`, `entregablesGenerados`, `facturasEmitidas` |
| `Proyecto` | `cliente`, `pm` | `tareas`, `hitos`, `entregablesIa`, `facturas`, `solicitudesCambio` |
| `SolicitudCambio` | `proyecto`, `solicitante` | `tareas` |
| `Tarea` | `proyecto`, `asignado`, `solicitudCambio` | — |
| `Hito` | `proyecto` | — |
| `EntregableIA` | `proyecto`, `generador` | — |
| `Factura` | `proyecto`, `emisor` | — |

> Todos los modelos usan además los traits `HasFactory` y `Auditable` (registro de
> cambios). `User` suma `Notifiable` y `HasRoles` (Spatie) para el manejo de roles.

## Scoping por cliente (agosto 2026)

- `User::cliente()` — la empresa que representa el usuario (solo rol Cliente;
  los internos tienen `cliente_id` en `NULL`).
- `User::esCliente()` / `User::puedeVer($modelo)` — los roles internos ven todo;
  un Cliente solo ve los registros cuyo proyecto pertenece a su empresa. Si intenta
  abrir por URL algo de otro cliente, el controller responde 403.
- `Tarea::scopeVisiblePara()` (y sus equivalentes en los demás modelos) — filtra
  los listados por la empresa del usuario en cada query de `index`.
