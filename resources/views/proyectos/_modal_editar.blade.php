    <x-crud-modal id="modal-proyecto-editar" titulo="Editar Proyecto">
        <form method="POST" action="{{ route('proyectos.store') }}" class="flex flex-col gap-4" data-proyecto-form="editar" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('proyectos._campos', ['proyectoItem' => null, 'esCreacion' => false])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
