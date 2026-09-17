    <x-crud-modal id="modal-factura-editar" titulo="Editar Factura">
        <form method="POST" action="{{ route('facturas.store') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('facturas._campos', ['factura' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
