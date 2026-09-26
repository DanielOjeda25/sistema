/*
 * Tablero de tareas estilo Trello: drag & drop con SortableJS, creación de
 * tarjetas desde un modal y edición rápida en otro modal, todo vía AJAX.
 *
 * El HTML lo renderiza el servidor (resources/views/tareas/tablero.blade.php);
 * este módulo solo mueve, crea y actualiza tarjetas sin recargar la página.
 */
import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', () => {
    const tablero = document.querySelector('[data-tablero]');
    if (!tablero) return;

    const cfg = window.TABLERO;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // ---------------------------------------------------------------- AJAX

    async function enviar(url, method, body) {
        const respuesta = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body ? JSON.stringify(body) : undefined,
        });

        if (!respuesta.ok) {
            const error = new Error('La petición falló');
            error.estado = respuesta.status;
            error.datos = await respuesta.json().catch(() => ({}));
            throw error;
        }

        return respuesta.status === 204 ? null : respuesta.json();
    }

    function primerError(datos) {
        const errores = datos?.errors;
        if (!errores) return datos?.message ?? 'Ocurrió un error inesperado.';
        return Object.values(errores).flat()[0];
    }

    // ------------------------------------------------------------- tarjetas

    const esc = valor => String(valor ?? '')
        .replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;').replaceAll("'", '&#039;');

    const hoy = new Date().toISOString().slice(0, 10);

    // Laravel serializa las fechas como ISO completo ("2026-09-10T00:00:00…");
    // con el slice queda "YYYY-MM-DD", que es lo que usa el input[type=date]
    // y la comparación de vencidas.
    const soloFecha = iso => (iso ?? '').slice(0, 10);

    // Mantiene el mismo formato d/m y el mismo resalte de vencidas que la
    // vista Blade, para que una tarjeta refrescada por AJAX no cambie de estilo.
    function fechaFormateada(tarea) {
        const fecha = soloFecha(tarea.fecha_limite);
        if (!fecha) return null;
        const [, mes, dia] = fecha.split('-');

        return {
            texto: `${dia}/${mes}`,
            vencida: fecha < hoy && !['completada', 'cancelada'].includes(tarea.estado),
        };
    }

    // El JSON viaja en un atributo delimitado por comillas simples: hay que
    // escapar & y ' para que el navegador no lo rompa al decodificar el HTML.
    // El resto de los caracteres es seguro dentro de ese atributo.
    const tareaAttr = tarea => JSON.stringify(tarea)
        .replaceAll('&', '&amp;').replaceAll("'", '&#039;');

    function tarjetaHTML(tarea) {
        const prioridad = cfg.prioridades[tarea.prioridad] ?? cfg.prioridades.media;
        const fecha = fechaFormateada(tarea);
        const asignado = esc(tarea.asignado?.name ?? 'Sin asignar');
        const sprint = tarea.sprint?.nombre
            ? `<span class="px-2 py-0.5 rounded-full font-medium bg-indigo-100 text-indigo-700" title="Sprint">${esc(tarea.sprint.nombre)}</span>`
            : '';
        const proyecto = cfg.proyectoFiltrado ? '' : `
            <span class="truncate max-w-[120px]" title="${esc(tarea.proyecto?.nombre)}">${esc(tarea.proyecto?.nombre)}</span>`;
        const eliminar = cfg.puedeEditar ? `
            <button type="button" class="eliminar-tarjeta absolute top-1.5 right-1.5 rounded-md p-1 text-gray-300 transition hover:bg-red-50 hover:text-red-600" title="Eliminar tarea" aria-label="Eliminar tarea">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
            </button>` : '';

        return `
            <article class="tarjeta group relative bg-white rounded-lg shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow" data-id="${tarea.id}" data-tarea='${tareaAttr(tarea)}'>
                <a href="${cfg.urls.show.replace(':id:', tarea.id)}" class="block pr-6 font-medium text-gray-800 hover:text-indigo-600">${esc(tarea.titulo)}</a>
                ${eliminar}
                <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-full font-medium ${prioridad}">${esc(tarea.prioridad.charAt(0).toUpperCase() + tarea.prioridad.slice(1))}</span>
                    ${fecha ? `<span class="px-2 py-0.5 rounded-full font-medium ${fecha.vencida ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600'}">${fecha.texto}</span>` : ''}
                    ${sprint}
                </div>
                <div class="mt-2 text-xs text-gray-500 flex justify-between gap-2">
                    <span class="truncate">${asignado}</span>
                    ${proyecto}
                </div>
            </article>`;
    }

    function columna(estado) {
        return tablero.querySelector(`.columna[data-estado="${estado}"] .tarjetas`);
    }

    function actualizarContadores() {
        tablero.querySelectorAll('.columna').forEach(col => {
            col.querySelector('[data-contador]').textContent =
                col.querySelectorAll('.tarjeta').length;
        });
    }

    // Error puntual dentro del formulario de un modal (mostrarError() es el
    // modal global de errores del tablero).
    function mostrarErrorEn(elemento, mensaje) {
        elemento.textContent = mensaje;
        elemento.classList.remove('hidden');
    }

    function ocultarError(elemento) {
        elemento.classList.add('hidden');
    }

    // ------------------------------------------------ modales de error/confirmación

    // Reemplazan a alert() y confirm(): mismo propósito, pero con modales
    // propios del sistema en vez de los diálogos nativos del navegador.
    // Solo existen en la vista cuando el usuario puede editar; sin esto el
    // tablero de solo lectura rompería al intentar enganchar los listeners.
    const modalError = document.getElementById('modal-error');
    const modalConfirmar = document.getElementById('modal-confirmar');
    let resolverConfirmacion = null;

    function mostrarError(mensaje) {
        if (!modalError) return;
        document.getElementById('modal-error-mensaje').textContent = mensaje;
        document.body.appendChild(modalError);
        modalError.classList.remove('hidden');
    }

    function cerrarError() {
        modalError?.classList.add('hidden');
    }

    function confirmarAccion(mensaje) {
        if (!modalConfirmar) return Promise.resolve(false);
        document.getElementById('modal-confirmar-mensaje').textContent = mensaje;
        document.body.appendChild(modalConfirmar);
        modalConfirmar.classList.remove('hidden');

        return new Promise(resolver => { resolverConfirmacion = resolver; });
    }

    function responderConfirmacion(aceptado) {
        modalConfirmar?.classList.add('hidden');
        if (resolverConfirmacion) {
            resolverConfirmacion(aceptado);
            resolverConfirmacion = null;
        }
    }

    if (modalError) {
        document.querySelectorAll('[data-cerrar-error]').forEach(el =>
            el.addEventListener('click', cerrarError));
        document.getElementById('btn-error-recargar').addEventListener('click', () => location.reload());
    }

    if (modalConfirmar) {
        document.getElementById('btn-confirmar-cancelar').addEventListener('click', () => responderConfirmacion(false));
        document.getElementById('btn-confirmar-aceptar').addEventListener('click', () => responderConfirmacion(true));
        modalConfirmar.querySelector('.fixed.inset-0').addEventListener('click', () => responderConfirmacion(false));
    }

    // ----------------------------------------------------------- drag & drop

    function idsDe(estado) {
        return [...columna(estado).querySelectorAll('.tarjeta')].map(el => parseInt(el.dataset.id));
    }

    function guardarMovimiento(estadoOrigen, estadoDestino) {
        const columnas = [...new Set([estadoOrigen, estadoDestino])]
            .map(estado => ({ estado, ids: idsDe(estado) }));

        enviar(cfg.urls.mover, 'PATCH', { columnas })
            .then(actualizarContadores)
            .catch(e => {
                // 419 = sesión/CSRF vencido: la página se quedó abierta mucho
                // tiempo y el token ya no sirve; hay que recargar sí o sí.
                const detalle = e.estado === 419
                    ? 'Tu sesión expiró. Recargá la página e intentá de nuevo.'
                    : 'El movimiento no quedó guardado. Recargá la página para ver el estado real de las tareas.';
                mostrarError(detalle);
            });
    }

    if (cfg.puedeEditar) {
        tablero.querySelectorAll('.tarjetas').forEach(lista => {
            new Sortable(lista, {
                group: 'tareas',
                animation: 150,
                ghostClass: 'opacity-40',
                onEnd: ({ from, to }) => {
                    guardarMovimiento(from.closest('.columna').dataset.estado,
                                      to.closest('.columna').dataset.estado);
                },
            });
        });
    }

    // ------------------------------------------------- creación (modal AJAX)

    if (cfg.puedeEditar) {
        const modalCrear = document.getElementById('modal-tarea-crear');
        const formCrear = document.getElementById('form-crear-tarea');
        const errorCrear = formCrear.querySelector('.error');

        function cerrarModalCrear() {
            modalCrear.classList.add('hidden');
        }

        function abrirModalCrear(estado) {
            // Igual que el modal de edición: al final del <body> para que
            // ningún elemento del tablero o del layout quede por encima.
            document.body.appendChild(modalCrear);
            formCrear.reset();
            // La columna desde la que se abrió define el estado inicial; el
            // resto (proyecto, sprint, responsable) viene prefijado del HTML.
            formCrear.estado.value = estado;
            ocultarError(errorCrear);
            modalCrear.classList.remove('hidden');
            formCrear.titulo.focus();
        }

        tablero.querySelectorAll('[data-agregar-estado]').forEach(boton =>
            boton.addEventListener('click', () => abrirModalCrear(boton.dataset.agregarEstado)));

        modalCrear.querySelectorAll('[data-cerrar-modal]').forEach(el =>
            el.addEventListener('click', cerrarModalCrear));

        document.addEventListener('keydown', evento => {
            if (evento.key === 'Escape' && !modalCrear.classList.contains('hidden')) cerrarModalCrear();
        });

        formCrear.addEventListener('submit', async evento => {
            evento.preventDefault();

            ocultarError(errorCrear);
            try {
                const tarea = await enviar(cfg.urls.store, 'POST', Object.fromEntries(new FormData(formCrear)));
                const lista = columna(tarea.estado);
                lista.querySelector('.sin-tareas')?.remove();
                lista.insertAdjacentHTML('beforeend', tarjetaHTML(tarea));
                actualizarContadores();
                cerrarModalCrear();
            } catch (e) {
                mostrarErrorEn(errorCrear, primerError(e.datos));
            }
        });
    }

    // ------------------------------------------------------- modal de edición

    const modal = document.getElementById('modal-tarea');
    const formEditar = document.getElementById('form-editar-tarea');
    const errorEditar = formEditar.querySelector('.error');
    let tarjetaAbierta = null;

    function abrirModal(tarjetaEl) {
        tarjetaAbierta = tarjetaEl;
        const tarea = JSON.parse(tarjetaEl.dataset.tarea);

        // El modal se porta al final del <body> para que ningún elemento del
        // tablero o del layout quede por encima del overlay.
        document.body.appendChild(modal);

        formEditar.reset();
        formEditar.action = cfg.urls.update.replace(':id:', tarea.id);        formEditar.titulo.value = tarea.titulo;
        formEditar.descripcion.value = tarea.descripcion ?? '';
        formEditar.estado.value = tarea.estado;
        formEditar.prioridad.value = tarea.prioridad;
        formEditar.fecha_limite.value = soloFecha(tarea.fecha_limite);
        formEditar.proyecto_id.value = tarea.proyecto_id;
        formEditar.sprint_id.value = tarea.sprint_id ?? '';
        formEditar.asignado_a.value = tarea.asignado_a ?? '';

        ocultarError(errorEditar);
        modal.classList.remove('hidden');
        formEditar.titulo.focus();
    }

    function cerrarModal() {
        modal.classList.add('hidden');
        tarjetaAbierta = null;
    }

    // Elimina una tarjeta (desde el tacho de la tarjeta o desde el modal de
    // edicion): pide confirmacion, borra por AJAX y reordena la columna.
    // Devuelve true si se elimino de verdad.
    async function eliminarTarjeta(tarjeta) {
        if (!await confirmarAccion('¿Eliminar esta tarea? Esta acción no se puede deshacer.')) return false;

        try {
            await enviar(cfg.urls.update.replace(':id:', tarjeta.dataset.id), 'DELETE');
            const estado = tarjeta.closest('.columna').dataset.estado;
            tarjeta.remove();
            guardarMovimiento(estado, estado);
            actualizarContadores();
            return true;
        } catch (e) {
            mostrarError(primerError(e.datos));
            return false;
        }
    }

    if (cfg.puedeEditar) {
        // Clic en una tarjeta: si ya está dentro de una columna (los botones
        // de agregar no cuentan) se abre el modal en vez de navegar.
        tablero.addEventListener('click', evento => {
            if (evento.target.closest('button')) return;

            const tarjeta = evento.target.closest('.tarjeta');
            if (tarjeta) {
                evento.preventDefault();
                abrirModal(tarjeta);
            }
        });

        // Tacho en la tarjeta: elimina directo (con confirmacion), sin abrir
        // el modal de edicion. Los botones se excluyen solos del click de arriba.
        tablero.addEventListener('click', evento => {
            const botonEliminar = evento.target.closest('.eliminar-tarjeta');
            if (botonEliminar) eliminarTarjeta(botonEliminar.closest('.tarjeta'));
        });

        modal.querySelectorAll('[data-cerrar-modal]').forEach(el =>
            el.addEventListener('click', cerrarModal));
        document.addEventListener('keydown', evento => {
            if (evento.key !== 'Escape') return;
            if (!modal.classList.contains('hidden')) cerrarModal();
            if (modalDetalle && !modalDetalle.classList.contains('hidden')) cerrarDetalle();
            if (modalError && !modalError.classList.contains('hidden')) cerrarError();
            if (modalConfirmar && !modalConfirmar.classList.contains('hidden')) responderConfirmacion(false);
        });

        formEditar.addEventListener('submit', async evento => {
            evento.preventDefault();

            ocultarError(errorEditar);
            try {
                const tarea = await enviar(formEditar.action, 'PATCH', Object.fromEntries(new FormData(formEditar)));
                const html = tarjetaHTML(tarea).trim();
                const estadoAnterior = tarjetaAbierta.closest('.columna').dataset.estado;

                if (estadoAnterior === tarea.estado) {
                    tarjetaAbierta.insertAdjacentHTML('beforebegin', html);
                    tarjetaAbierta.remove();
                } else {
                    // Cambió de columna: va al final de la nueva, como al arrastrar.
                    columna(tarea.estado).insertAdjacentHTML('beforeend', html);
                    tarjetaAbierta.remove();
                    guardarMovimiento(estadoAnterior, tarea.estado);
                }

                actualizarContadores();
                cerrarModal();
            } catch (e) {
                mostrarErrorEn(errorEditar, primerError(e.datos));
            }
        });

        formEditar.querySelector('.eliminar-tarea').addEventListener('click', async () => {
            ocultarError(errorEditar);
            if (await eliminarTarjeta(tarjetaAbierta)) cerrarModal();
        });

        // ------------------------------------------------ detalle (solo lectura)

        const modalDetalle = document.getElementById('modal-detalle-tarea');
        let tarjetaEnDetalle = null;

        function cerrarDetalle() {
            modalDetalle.classList.add('hidden');
            tarjetaEnDetalle = null;
        }

        function abrirDetalle(tarjeta) {
            const tarea = JSON.parse(tarjeta.dataset.tarea);
            tarjetaEnDetalle = tarjeta;
            document.body.appendChild(modalDetalle);

            document.getElementById('detalle-titulo').textContent = tarea.titulo;

            const badgeEstado = document.getElementById('detalle-estado');
            badgeEstado.textContent = (cfg.columnas[tarea.estado] ?? [tarea.estado])[0];
            badgeEstado.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider '
                + ((cfg.columnas[tarea.estado] ?? [])[1] ?? 'bg-gray-100 text-gray-600');

            const badgePrioridad = document.getElementById('detalle-prioridad');
            badgePrioridad.textContent = tarea.prioridad.charAt(0).toUpperCase() + tarea.prioridad.slice(1);
            badgePrioridad.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider '
                + (cfg.prioridades[tarea.prioridad] ?? 'bg-gray-100 text-gray-600');

            document.getElementById('detalle-proyecto').textContent = tarea.proyecto?.nombre ?? 'Sin proyecto';
            document.getElementById('detalle-sprint-wrapper').classList.toggle('hidden', !tarea.sprint?.nombre);
            document.getElementById('detalle-sprint').textContent = tarea.sprint?.nombre ?? '';
            document.getElementById('detalle-descripcion').textContent = tarea.descripcion || 'Sin descripción.';
            document.getElementById('detalle-responsable').textContent = tarea.asignado?.name ?? 'Sin asignar';

            const fecha = soloFecha(tarea.fecha_limite);
            document.getElementById('detalle-fecha').textContent = fecha ? fecha.split('-').reverse().join('/') : 'Sin fecha';

            modalDetalle.classList.remove('hidden');
        }

        // "Ver detalle" en el modal de edicion: muestra la informacion aca,
        // sin ir a otra pantalla.
        formEditar.querySelector('.ver-detalle').addEventListener('click', () => {
            const tarjeta = tarjetaAbierta;
            cerrarModal();
            if (tarjeta) abrirDetalle(tarjeta);
        });

        modalDetalle.querySelectorAll('[data-cerrar-detalle]').forEach(el =>
            el.addEventListener('click', cerrarDetalle));

        document.getElementById('btn-detalle-editar').addEventListener('click', () => {
            const tarjeta = tarjetaEnDetalle;
            cerrarDetalle();
            if (tarjeta) abrirModal(tarjeta);
        });
    }
});
