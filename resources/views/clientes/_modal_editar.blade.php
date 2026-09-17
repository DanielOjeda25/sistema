        <x-crud-modal id="modal-cliente-editar" titulo="Editar Cliente">
            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4" data-crud-form>
                @csrf
                @method('PUT')
                <input type="hidden" name="desde_modal" value="1">
                @include('clientes._campos', ['prefijo' => 'editar-'])
                <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </x-crud-modal>
