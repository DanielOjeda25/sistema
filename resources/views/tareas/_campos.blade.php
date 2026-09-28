{{--
    Campos del formulario de Tarea, compartidos por los modales de crear y
    editar (index) y por el tablero. $tarea viene null al crear; al editar,
    cada campo arranca con old() (lo ultimo enviado si hubo error) o con el
    valor actual del modelo.
--}}
<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="titulo" value="Título" />
        <x-text-input id="titulo" name="titulo" type="text" maxlength="255" class="mt-1 block w-full" :value="old('titulo', $tarea?->titulo)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">{{ old('descripcion', $tarea?->descripcion) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
    </div>

    <div>
        <x-input-label for="estado" value="Estado" />
        <x-buscador-select name="estado" :conBuscador="false" textoTodos=""
                           :opciones="['pendiente' => 'Pendiente', 'en_progreso' => 'En progreso', 'completada' => 'Completada', 'cancelada' => 'Cancelada']"
                           :seleccionado="old('estado', $tarea?->estado ?? 'pendiente')"
                           placeholder="Estado..." />
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>

    <div>
        <x-input-label for="prioridad" value="Prioridad" />
        <x-buscador-select name="prioridad" :conBuscador="false" textoTodos=""
                           :opciones="['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']"
                           :seleccionado="old('prioridad', $tarea?->prioridad ?? 'media')"
                           placeholder="Prioridad..." />
        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
    </div>

    <div>
        <x-input-label for="fecha_limite" value="Fecha límite (opcional)" />
        <x-text-input id="fecha_limite" name="fecha_limite" type="date" class="mt-1 block w-full" :value="old('fecha_limite', $tarea?->fecha_limite?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_limite')" />
    </div>

    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <x-buscador-select name="proyecto_id"
                           :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                           :seleccionado="old('proyecto_id', $tarea?->proyecto_id)"
                           placeholder="Buscar proyecto..." textoTodos="Ninguno" />
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>

    <div>
        <x-input-label for="sprint_id" value="Sprint (opcional)" />
        <x-buscador-select name="sprint_id"
                           :opciones="$sprints->mapWithKeys(fn ($s) => [$s->id => trim($s->nombre . (($s->proyecto?->nombre ?? '') !== '' ? ' — ' . $s->proyecto->nombre : ''))])->all()"
                           :seleccionado="old('sprint_id', $tarea?->sprint_id)"
                           placeholder="Buscar sprint..." textoTodos="Sin sprint" />
        <x-input-error class="mt-2" :messages="$errors->get('sprint_id')" />
    </div>

    <div>
        <x-input-label for="asignado_a" value="Asignar a" />
        <x-buscador-select name="asignado_a"
                           :opciones="$usuarios->mapWithKeys(fn ($u) => [$u->id => trim($u->name . ' (' . $u->roles->pluck('name')->implode(', ') . ')')])->all()"
                           :seleccionado="old('asignado_a', $tarea?->asignado_a)"
                           placeholder="Buscar responsable..." textoTodos="Nadie" />
        <x-input-error class="mt-2" :messages="$errors->get('asignado_a')" />
    </div>

    <div>
        <x-input-label for="solicitud_cambio_id" value="Solicitud de cambio (opcional)" />
        <x-buscador-select name="solicitud_cambio_id"
                           :opciones="$solicitudes->mapWithKeys(fn ($s) => [$s->id => $s->titulo])->all()"
                           :seleccionado="old('solicitud_cambio_id', $tarea?->solicitud_cambio_id)"
                           placeholder="Buscar solicitud..." textoTodos="— Ninguna —" />
        <x-input-error class="mt-2" :messages="$errors->get('solicitud_cambio_id')" />
    </div>
</div>
