/*
 * Tablero de tareas estilo Trello: drag & drop con SortableJS, creación de
 * tarjetas inline por columna y edición rápida en un modal, todo vía AJAX.
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
        const proyecto = cfg.proyectoFiltrado ? '' : `
            <span class="truncate max-w-[120px]" title="${esc(tarea.proyecto?.nombre)}">${esc(tarea.proyecto?.nombre)}</span>`;

        return `
            <article class="tarjeta bg-white rounded-lg shadow-sm p-3 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow" data-id="${tarea.id}" data-tarea='${tareaAttr(tarea)}'>
                <a href="${cfg.urls.show.replace(':id:', tarea.id)}" class="font-medium text-gray-800 hover:text-indigo-600">${esc(tarea.titulo)}</a>
                <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="px-2 py-0.5 rounded-full font-medium ${prioridad}">${esc(tarea.prioridad.charAt(0).toUpperCase() + tarea.prioridad.slice(1))}</span>
                    ${fecha ? `<span class="px-2 py-0.5 rounded-full font-medium ${fecha.vencida ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600'}">${fecha.texto}</span>` : ''}
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

    function mostrarError(elemento, mensaje) {
        elemento.textContent = mensaje;
        elemento.classList.remove('hidden');
    }

    function ocultarError(elemento) {
        elemento.classList.add('hidden');
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
            .catch(() => {
                alert('No se pudo guardar el movimiento. La página se va a recargar para mostrar el estado real.');
                location.reload();
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

    // ------------------------------------------------- creación inline (AJAX)

    tablero.querySelectorAll('form[data-agregar]').forEach(form => {
        form.addEventListener('submit', async evento => {
            evento.preventDefault();

            const datos = Object.fromEntries(new FormData(form));
            const error = form.querySelector('.error');

            ocultarError(error);
            try {
                const tarea = await enviar(cfg.urls.store, 'POST', datos);
                const lista = form.closest('.columna').querySelector('.tarjetas');
                lista.querySelector('.sin-tareas')?.remove();
                lista.insertAdjacentHTML('beforeend', tarjetaHTML(tarea));
                actualizarContadores();
                form.reset();
                form.querySelector('textarea')?.focus();
            } catch (e) {
                mostrarError(error, primerError(e.datos));
            }
        });

        // Enter envía, Shift+Enter salta de línea (como en Trello).
        form.querySelector('textarea').addEventListener('keydown', evento => {
            if (evento.key === 'Enter' && !evento.shiftKey) {
                evento.preventDefault();
                form.requestSubmit();
            }
        });
    });

    tablero.querySelectorAll('[data-alternar-agregar]').forEach(boton => {
        boton.addEventListener('click', () => {
            const form = boton.closest('.agregar-tarjeta').querySelector('form');
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) form.querySelector('textarea').focus();
        });
    });

    // ------------------------------------------------------- modal de edición

    const modal = document.getElementById('modal-tarea');
    const formEditar = document.getElementById('form-editar-tarea');
    const errorEditar = formEditar.querySelector('.error');
    let tarjetaAbierta = null;

    function abrirModal(tarjetaEl) {
        tarjetaAbierta = tarjetaEl;
        const tarea = JSON.parse(tarjetaEl.dataset.tarea);

        formEditar.reset();
        formEditar.action = cfg.urls.update.replace(':id:', tarea.id);        formEditar.titulo.value = tarea.titulo;
        formEditar.descripcion.value = tarea.descripcion ?? '';
        formEditar.estado.value = tarea.estado;
        formEditar.prioridad.value = tarea.prioridad;
        formEditar.fecha_limite.value = soloFecha(tarea.fecha_limite);
        formEditar.proyecto_id.value = tarea.proyecto_id;
        formEditar.asignado_a.value = tarea.asignado_a ?? '';
        formEditar.querySelector('.ver-detalle').href = cfg.urls.show.replace(':id:', tarea.id);

        ocultarError(errorEditar);
        modal.classList.remove('hidden');
        formEditar.titulo.focus();
    }

    function cerrarModal() {
        modal.classList.add('hidden');
        tarjetaAbierta = null;
    }

    if (cfg.puedeEditar) {
        // Clic en una tarjeta: si ya está dentro de una columna (los botones
        // de agregar no cuentan) se abre el modal en vez de navegar.
        tablero.addEventListener('click', evento => {
            if (evento.target.closest('form[data-agregar]') || evento.target.closest('button')) return;

            const tarjeta = evento.target.closest('.tarjeta');
            if (tarjeta) {
                evento.preventDefault();
                abrirModal(tarjeta);
            }
        });

        modal.querySelectorAll('[data-cerrar-modal]').forEach(el =>
            el.addEventListener('click', cerrarModal));
        document.addEventListener('keydown', evento => {
            if (evento.key === 'Escape' && !modal.classList.contains('hidden')) cerrarModal();
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
                mostrarError(errorEditar, primerError(e.datos));
            }
        });

        formEditar.querySelector('.eliminar-tarea').addEventListener('click', async () => {
            if (!confirm('¿Eliminar esta tarea? Esta acción no se puede deshacer.')) return;

            ocultarError(errorEditar);
            try {
                await enviar(formEditar.action, 'DELETE');
                const estadoAnterior = tarjetaAbierta.closest('.columna').dataset.estado;
                tarjetaAbierta.remove();
                guardarMovimiento(estadoAnterior, estadoAnterior);
                actualizarContadores();
                cerrarModal();
            } catch (e) {
                mostrarError(errorEditar, primerError(e.datos));
            }
        });
    }
});
