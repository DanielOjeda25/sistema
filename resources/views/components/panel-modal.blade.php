{{--
    Panel grande que muestra una pagina completa dentro de un modal.
    Cualquier boton con data-panel="/url" hace que este componente traiga
    esa pagina por AJAX, extraiga su contenido principal y lo muestre aca,
    sin navegar. Intencionalmente NO usa Alpine: se maneja con clases,
    asi nunca queda desincronizado con el resto de la pagina.
--}}
<div id="panel-modal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" data-panel-cerrar></div>

    <div class="min-h-full flex items-start justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-6 overflow-hidden">
            <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 bg-white/95 backdrop-blur border-b border-gray-100">
                <h3 class="font-semibold text-lg text-gray-800" id="panel-modal-titulo">Detalle</h3>
                <button type="button" data-panel-cerrar
                        class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Cerrar">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>

            <div id="panel-modal-cuerpo" class="relative p-6 max-h-[80vh] overflow-y-auto scroll-suave">
                <div id="panel-modal-cargando" class="hidden absolute inset-0 z-10 flex items-center justify-center bg-white/70">
                    <span class="h-10 w-10 rounded-full border-4 border-gray-200 border-t-[#00b87d] animate-spin"></span>
                </div>
                {{-- El panel reemplaza la navegacion: los links "Volver al listado"
                     del contenido traido no tienen sentido aca. --}}
                <style>#panel-modal-contenido .volver-listado { display: none; }</style>
                <div id="panel-modal-contenido" class="space-y-6"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const raiz = () => document.getElementById('panel-modal');

        window.abrirPanelModal = async function (url) {
            raiz().classList.remove('hidden');
            document.getElementById('panel-modal-cargando').classList.remove('hidden');
            document.getElementById('panel-modal-contenido').innerHTML = '';

            try {
                const respuesta = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await respuesta.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const h2 = doc.querySelector('main h2');
                document.getElementById('panel-modal-titulo').textContent = h2 ? h2.textContent.trim() : 'Detalle';

                // Contenido principal de la pagina traida: entre los bloques
                // py-12 nos quedamos con el mas pesado (el slot de la vista),
                // nunca con un contenedor vacio.
                const candidatos = [...doc.querySelectorAll('main .py-12')];
                const principal = candidatos.sort((a, b) => b.innerHTML.length - a.innerHTML.length)[0]
                    ?? doc.querySelector('main');
                document.getElementById('panel-modal-contenido').innerHTML = principal
                    ? principal.innerHTML
                    : '<p class="text-gray-500">No se pudo cargar el detalle.</p>';

                // Los modales incluidos en la pagina van al body, igual que en
                // las vistas normales, para que no queden recortados por el panel.
                document.querySelectorAll('#panel-modal-contenido [data-crud-modal]').forEach(m => document.body.appendChild(m));

                if (window.Alpine) window.Alpine.initTree(document.getElementById('panel-modal-contenido'));
            } catch (e) {
                document.getElementById('panel-modal-contenido').innerHTML =
                    '<p class="text-gray-500">No se pudo cargar el detalle. Intenta de nuevo.</p>';
            } finally {
                document.getElementById('panel-modal-cargando').classList.add('hidden');
            }
        };

        window.cerrarPanelModal = function () {
            raiz().classList.add('hidden');
            document.getElementById('panel-modal-contenido').innerHTML = '';
        };

        document.addEventListener('click', evento => {
            if (evento.target.closest('[data-panel]')) {
                evento.preventDefault();
                abrirPanelModal(evento.target.closest('[data-panel]').dataset.panel);
            }
            if (evento.target.closest('[data-panel-cerrar]')) cerrarPanelModal();
        });

        document.addEventListener('keydown', evento => {
            if (evento.key === 'Escape' && !raiz().classList.contains('hidden')) cerrarPanelModal();
        });
    })();
</script>
