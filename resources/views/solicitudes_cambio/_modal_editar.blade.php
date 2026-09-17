    <x-crud-modal id="modal-solicitud-editar" titulo="Editar Solicitud de Cambio">
        <form method="POST" action="{{ route('solicitudes-cambio.store') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('solicitudes_cambio._campos', ['solicitud' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
