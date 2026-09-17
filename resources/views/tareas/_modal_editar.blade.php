    <x-crud-modal id="modal-tarea-editar" titulo="Editar Tarea">
        <form method="POST" action="{{ route('tareas.store') }}" class="space-y-4" data-crud-form>
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('tareas._campos', ['tarea' => null])
            <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
