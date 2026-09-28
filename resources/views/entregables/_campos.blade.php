{{--
    Campos de Entregable IA, compartidos entre pantallas completas y modales
    del listado. $entregable es null al crear.
--}}
<div>
    <x-input-label for="titulo" value="Título" />
    <x-text-input maxlength="255" id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $entregable->titulo ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
</div>

<div>
    <x-input-label for="contenido" value="Contenido" />
    <textarea id="contenido" name="contenido" rows="6" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>{{ old('contenido', $entregable->contenido ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('contenido')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="tipo" value="Tipo" />
        <x-buscador-select name="tipo" :conBuscador="false" textoTodos=""
                           :opciones="\App\Models\EntregableIA::TIPOS"
                           :seleccionado="old('tipo', $entregable->tipo ?? 'documento')"
                           placeholder="Tipo..." />
        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
    </div>
    <div>
        <x-input-label for="estado" value="Estado" />
        <x-buscador-select name="estado" :conBuscador="false" textoTodos=""
                           :opciones="['borrador' => 'Borrador', 'revisado' => 'Revisado', 'aprobado' => 'Aprobado']"
                           :seleccionado="old('estado', $entregable->estado ?? 'borrador')"
                           placeholder="Estado..." />
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <x-buscador-select name="proyecto_id"
                           :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                           :seleccionado="old('proyecto_id', $entregable->proyecto_id ?? '')"
                           placeholder="Buscar proyecto..." textoTodos="Ninguno" />
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="generado_por" value="Generado por" />
        <x-buscador-select name="generado_por"
                           :opciones="$usuarios->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
                           :seleccionado="old('generado_por', $entregable->generado_por ?? '')"
                           placeholder="Buscar usuario..." textoTodos="Nadie" />
        <x-input-error class="mt-2" :messages="$errors->get('generado_por')" />
    </div>
</div>
