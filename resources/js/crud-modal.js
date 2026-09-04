/*
 * Modales CRUD compartidos (componente <x-crud-modal>).
 *
 * Apertura: botones con data-abrir-modal="<id del modal>". Opcionalmente
 * llevan data-valores (JSON nombre => valor) para rellenar el formulario
 * del modal y data-url para sobreescribir su action (edición).
 */
document.addEventListener('DOMContentLoaded', () => {
    function modal(id) {
        return document.querySelector(`[data-crud-modal]#${id}`);
    }

    function abrir(el, valores, url) {
        document.body.appendChild(el);
        el.classList.remove('hidden');

        if (valores) {
            const form = el.querySelector('form');
            if (form) {
                Object.entries(valores).forEach(([nombre, valor]) => {
                    const campo = form.elements[nombre];
                    if (campo) campo.value = valor ?? '';
                });
            }
        }

        if (url) {
            const form = el.querySelector('form');
            if (form) form.action = url;
        }

        const primero = el.querySelector('input:not([type=hidden]), select, textarea');
        primero?.focus();
    }

    document.addEventListener('click', evento => {
        const boton = evento.target.closest('[data-abrir-modal]');
        if (!boton) return;

        const el = modal(boton.dataset.abrirModal);
        if (!el) return;

        evento.preventDefault();
        let valores = null;
        if (boton.dataset.valores) valores = JSON.parse(boton.dataset.valores);
        abrir(el, valores, boton.dataset.url);
    });

    // Cierre: botones/fondo con data-crud-cerrar dentro del modal, o Escape.
    document.addEventListener('click', evento => {
        if (evento.target.closest('[data-crud-cerrar]')) {
            evento.target.closest('[data-crud-modal]')?.classList.add('hidden');
        }
    });

    document.addEventListener('keydown', evento => {
        if (evento.key !== 'Escape') return;
        document.querySelectorAll('[data-crud-modal]:not(.hidden)').forEach(el =>
            el.classList.add('hidden'));
    });
});
