{{--
    Campos de Solicitud de Cambio, compartidos entre pantallas completas y
    modales del listado. $solicitud es null al crear.
--}}
<div>
    <x-input-label for="titulo" value="Título" />
    <x-text-input maxlength="255" id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $solicitud->titulo ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
</div>

<div>
    <x-input-label for="descripcion" value="Descripción" />
    <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>{{ old('descripcion', $solicitud->descripcion ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="estado" value="Estado" />
        <x-buscador-select name="estado" :conBuscador="false" textoTodos=""
                           :opciones="['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada']"
                           :seleccionado="old('estado', $solicitud->estado ?? 'pendiente')"
                           placeholder="Estado..." />
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
    <div>
        <x-input-label for="prioridad" value="Prioridad" />
        <x-buscador-select name="prioridad" :conBuscador="false" textoTodos=""
                           :opciones="['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']"
                           :seleccionado="old('prioridad', $solicitud->prioridad ?? 'media')"
                           placeholder="Prioridad..." />
        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
    </div>
    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <x-buscador-select name="proyecto_id"
                           :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                           :seleccionado="old('proyecto_id', $solicitud->proyecto_id ?? '')"
                           placeholder="Buscar proyecto..." textoTodos="Ninguno" />
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="solicitado_por" value="Solicitado por" />
        <x-buscador-select name="solicitado_por"
                           :opciones="$usuarios->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
                           :seleccionado="old('solicitado_por', $solicitud->solicitado_por ?? '')"
                           placeholder="Buscar usuario..." textoTodos="Nadie" />
        <x-input-error class="mt-2" :messages="$errors->get('solicitado_por')" />
    </div>
</div>
