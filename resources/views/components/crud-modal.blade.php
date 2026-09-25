@props(['id', 'titulo', 'abrirConErrores' => false])

{{--
    Modal genérico para crear/editar sin dejar el listado.

    - Se abre con un botón `data-abrir-modal="{{ $id }}"`; si el botón trae
      `data-valores` (JSON nombre => valor) y/o `data-url`, el JS compartido
      (resources/js/crud-modal.js) rellena el formulario y su action.
    - Con abrirConErrores, vuelve abierto tras una validación fallida para
      que se vean los mensajes de error del form.
    --}}

<div id="{{ $id }}"
     class="@unless($abrirConErrores && $errors->any()) hidden @endunless fixed inset-0 overflow-y-auto"
     style="z-index: 9999"
     role="dialog" aria-modal="true" data-crud-modal>
    <div class="fixed inset-0 bg-gray-900/50" style="z-index: -1" data-crud-cerrar></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative">
            <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-white/95 backdrop-blur border-b border-gray-100">
                <h3 class="font-semibold text-lg text-gray-800">{{ $titulo }}</h3>
                <button type="button" data-crud-cerrar
                        class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Cerrar">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>

            <div class="px-6 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
