<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="titulo" value="Título" />
        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $tarea?->titulo)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">{{ old('descripcion', $tarea?->descripcion) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
    </div>

    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            @foreach (['pendiente' => 'Pendiente', 'en_progreso' => 'En progreso', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $valor => $label)
                <option value="{{ $valor }}" @selected(old('estado', $tarea?->estado ?? 'pendiente') === $valor)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>

    <div>
        <x-input-label for="prioridad" value="Prioridad" />
        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            @foreach (['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta'] as $valor => $label)
                <option value="{{ $valor }}" @selected(old('prioridad', $tarea?->prioridad ?? 'media') === $valor)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
    </div>

    <div>
        <x-input-label for="fecha_limite" value="Fecha límite (opcional)" />
        <x-text-input id="fecha_limite" name="fecha_limite" type="date" class="mt-1 block w-full" :value="old('fecha_limite', $tarea?->fecha_limite?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_limite')" />
    </div>

    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un proyecto —</option>
            @foreach ($proyectos as $p)
                <option value="{{ $p->id }}" @selected(old('proyecto_id', $tarea?->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>

    <div>
        <x-input-label for="sprint_id" value="Sprint (opcional)" />
        <select id="sprint_id" name="sprint_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">
            <option value="">Sin sprint</option>
            @foreach ($sprints->groupBy(fn ($s) => $s->proyecto?->nombre ?? 'Sin proyecto') as $nombreProyecto => $sprintsProyecto)
                <optgroup label="{{ $nombreProyecto }}">
                    @foreach ($sprintsProyecto as $s)
                        <option value="{{ $s->id }}" @selected(old('sprint_id', $tarea?->sprint_id) == $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('sprint_id')" />
    </div>

    <div>
        <x-input-label for="asignado_a" value="Asignar a" />
        <select id="asignado_a" name="asignado_a" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un responsable —</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('asignado_a', $tarea?->asignado_a) == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('asignado_a')" />
    </div>

    <div>
        <x-input-label for="solicitud_cambio_id" value="Solicitud de cambio (opcional)" />
        <select id="solicitud_cambio_id" name="solicitud_cambio_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">
            <option value="">— Ninguna —</option>
            @foreach ($solicitudes as $s)
                <option value="{{ $s->id }}" @selected(old('solicitud_cambio_id', $tarea?->solicitud_cambio_id) == $s->id)>{{ $s->titulo }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('solicitud_cambio_id')" />
    </div>
</div>
