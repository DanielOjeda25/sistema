{{--
    Modal de confirmación para acciones destructivas (eliminar).

    Se usa junto con resources/js/crud-modal.js: el botón de eliminar lleva
    data-confirmar="Texto del aviso" y vive dentro del form de borrado; este
    modal se abre encima y, al aceptar, envía ese formulario. Incluírlo una
    vez por vista (solo puede haber uno porque el JS lo busca por id).
--}}
<x-crud-modal id="modal-confirmar" titulo="Confirmar acción">
    <p data-confirmar-mensaje class="text-sm text-gray-600">¿Seguro?</p>
    <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
        <button type="button" data-confirmar-cancelar class="text-sm text-gray-600 hover:underline">Cancelar</button>
        <button type="button" data-confirmar-aceptar
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
            Sí, eliminar
        </button>
    </div>
</x-crud-modal>
