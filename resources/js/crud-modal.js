/*
 * Modales CRUD compartidos (componente <x-crud-modal>).
 *
 * Apertura: botones con data-abrir-modal="<id del modal>". Opcionalmente
 * llevan data-valores (JSON nombre => valor) para rellenar el formulario
 * del modal y data-url para sobreescribir su action (edición).
 */

/*
 * Los modales de lectura (<x-*-view-modal>) viven dentro del contenedor
 * del contenido, cuya animacion de entrada deja un transform activo: eso
 * hace que su position:fixed se calcule respecto de ese contenedor y no
 * del viewport (quedan descentrados). Los pasamos al <body> ANTES de que
 * arranque Alpine: moverlos despues de inicializados les rompe los
 * listeners de window y dejan de abrir.
 */
document.querySelectorAll('[data-teleportar]').forEach(el => document.body.appendChild(el));

document.addEventListener('DOMContentLoaded', () => {
    // Validación inmediata del formulario de proyectos. Los mensajes se
    // muestran junto al campo y se mantiene la validación definitiva del servidor.
    document.querySelectorAll('[data-proyecto-form]').forEach(form => {
        const mensajes = {
            nombre: 'Ingresá el nombre del proyecto.',
            fecha_inicio: 'Seleccioná la fecha de inicio.',
            estado: 'Seleccioná un estado.',
            cliente_id: 'Seleccioná un cliente.',
            pm_id: 'Seleccioná un Project Manager.',
        };

        const valor = nombre => String(form.elements[nombre]?.value ?? '').trim();

        function errorDe(nombre) {
            if (mensajes[nombre] && !valor(nombre)) return mensajes[nombre];
            if (nombre === 'nombre' && valor(nombre).length > 255) return 'El nombre no puede superar los 255 caracteres.';
            if (nombre === 'fecha_inicio' && form.dataset.proyectoForm === 'crear') {
                const hoy = new Date();
                const fechaLocal = `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;
                if (valor(nombre) && valor(nombre) < fechaLocal) return 'La fecha de inicio debe ser hoy o posterior.';
            }
            if (nombre === 'fecha_fin_estimada' && valor(nombre) && valor('fecha_inicio') && valor(nombre) < valor('fecha_inicio')) {
                return 'El fin estimado no puede ser anterior a la fecha de inicio.';
            }
            return '';
        }

        function mostrarError(nombre) {
            const contenedor = form.querySelector(`[data-inline-field="${nombre}"]`);
            if (!contenedor) return true;

            let mensaje = contenedor.querySelector('[data-inline-error]');
            if (!mensaje) {
                mensaje = document.createElement('p');
                mensaje.dataset.inlineError = '';
                mensaje.className = 'mt-2 text-sm text-red-600';
                mensaje.setAttribute('role', 'alert');
                contenedor.appendChild(mensaje);
            }

            const error = errorDe(nombre);
            mensaje.textContent = error;
            mensaje.classList.toggle('hidden', !error);

            const campoVisible = contenedor.querySelector('input:not([type="hidden"]), textarea');
            campoVisible?.classList.toggle('border-red-500', Boolean(error));
            campoVisible?.setAttribute('aria-invalid', error ? 'true' : 'false');
            return !error;
        }

        const nombres = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin_estimada', 'estado', 'cliente_id', 'pm_id'];

        form.addEventListener('submit', evento => {
            form.dataset.validacionIntentada = '1';
            const valido = nombres.map(mostrarError).every(Boolean);
            if (valido) return;

            evento.preventDefault();
            form.querySelector('[aria-invalid="true"]')?.focus();
        });

        form.addEventListener('focusout', evento => {
            const nombre = evento.target.name || evento.target.closest('[data-inline-field]')?.dataset.inlineField;
            if (nombre && nombres.includes(nombre)) mostrarError(nombre);
        });

        form.addEventListener('input', evento => {
            if (!form.dataset.validacionIntentada) return;
            const nombre = evento.target.name || evento.target.closest('[data-inline-field]')?.dataset.inlineField;
            if (nombre && nombres.includes(nombre)) mostrarError(nombre);
        });

        form.addEventListener('change', evento => {
            const nombre = evento.target.name;
            if (nombre && nombres.includes(nombre)) mostrarError(nombre);
            if (nombre === 'fecha_inicio') mostrarError('fecha_fin_estimada');
        });
    });

    /*
     * Modales de solo lectura (x-buscador... no; los <x-*-view-modal>):
     * el boton lleva data-dispatch="<evento>" y data-valores (JSON). Al hacer
     * click se emite el evento a nivel window, que el modal escucha con
     * @<evento>.window para abrirse con esos datos. Va por delegacion para
     * no depender de que Alpine haya inicializado cada boton.
     */
    document.addEventListener('click', evento => {
        const boton = evento.target.closest('[data-dispatch]');
        if (!boton) return;

        let datos = null;
        if (boton.dataset.valores) {
            try { datos = JSON.parse(boton.dataset.valores); } catch { datos = null; }
        }
        window.dispatchEvent(new CustomEvent(boton.dataset.dispatch, { detail: datos }));
    });

    function modal(id) {
        return document.querySelector(`[data-crud-modal]#${id}`);
    }

    function abrir(el, valores, url) {
        document.body.appendChild(el);
        el.classList.remove('hidden');

        // Los formularios de alta siempre comienzan limpios al abrirlos.
        // form.reset() recupera los valores declarados en Blade (por ejemplo,
        // Estado = Activo) y evita reutilizar lo escrito en una apertura previa.
        const form = el.querySelector('form');
        if (!valores && form?.matches('[data-crud-create]')) {
            form.querySelectorAll('[data-no-autofill]').forEach(campo => campo.setAttribute('readonly', ''));
            form.reset();
            if (form.dataset.crudDefaults) {
                Object.entries(JSON.parse(form.dataset.crudDefaults)).forEach(([nombre, valor]) => {
                    if (form.elements[nombre]) form.elements[nombre].value = valor;
                });
            }
            form.removeAttribute('data-validacion-intentada');
            form.querySelectorAll('[data-inline-error]').forEach(mensaje => mensaje.remove());
            form.querySelectorAll('.border-red-500').forEach(campo => campo.classList.remove('border-red-500'));
            form.querySelectorAll('[aria-invalid]').forEach(campo => campo.removeAttribute('aria-invalid'));
            form.querySelectorAll('input[type="hidden"][name]').forEach(campo =>
                campo.dispatchEvent(new Event('change', { bubbles: true })));
        }

        if (valores) {
            if (form) {
                Object.entries(valores).forEach(([nombre, valor]) => {
                    const campo = form.elements[nombre];
                    if (campo) {
                        campo.value = valor ?? '';
                        // Los buscadores-select escuchan el change del oculto
                        // para reflejar el valor en su cuadro visible.
                        campo.dispatchEvent(new Event('change', { bubbles: true }));
                    }
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

    /*
     * Confirmación de borrado con modal (nada de alert() nativo).
     *
     * El botón de eliminar lleva data-confirmar (el texto del aviso) y vive
     * DENTRO del <form method="POST"> de borrado. Al clickearlo se abre el
     * modal data-crud-modal#modal-confirmar; si la persona acepta, se envía
     * ese formulario.
     */
    let formularioPendiente = null;

    document.addEventListener('click', evento => {
        const boton = evento.target.closest('[data-confirmar]');
        if (!boton) return;

        evento.preventDefault();
        formularioPendiente = boton.closest('form');
        const el = modal('modal-confirmar');
        if (!el || !formularioPendiente) return;

        el.querySelector('[data-confirmar-mensaje]').textContent =
            boton.dataset.confirmar;
        document.body.appendChild(el);
        el.classList.remove('hidden');
        el.querySelector('[data-confirmar-aceptar]')?.focus();
    });

    document.addEventListener('click', evento => {
        if (evento.target.closest('[data-confirmar-cancelar]')) {
            evento.target.closest('[data-crud-modal]')?.classList.add('hidden');
            formularioPendiente = null;
        }
        if (evento.target.closest('[data-confirmar-aceptar]')) {
            evento.target.closest('[data-crud-modal]')?.classList.add('hidden');
            formularioPendiente?.submit();
            formularioPendiente = null;
        }
    });
});
