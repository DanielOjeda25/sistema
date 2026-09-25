@props([
    'name' => 'buscador_select',
    'label' => null,
    'opciones' => [],          // array: [id => etiqueta]
    'seleccionado' => null,    // id elegido actualmente
    'placeholder' => 'Buscar...',
    'textoTodos' => 'Todos',
])

{{-- Buscador con lista desplegable filtrable: útil cuando el select tiene muchas opciones --}}
<div {{ $attributes }}>
    @if ($label)
        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">{{ $label }}</label>
    @endif

    <div class="relative"
         x-data="buscadorSelect({{ \Illuminate\Support\Js::from($seleccionado) }}, {{ \Illuminate\Support\Js::from($opciones) }})"
         @click.outside="abierto = false">
        {{-- Valor real que se envía en el formulario --}}
        <input type="hidden" name="{{ $name }}" :value="valor">

        {{-- Campo visible: muestra la etiqueta elegida o el texto de búsqueda --}}
        <input type="text" x-model="texto" @focus="abrir()" @input="abierto = true; filtrar()"
               :placeholder="valor ? '' : '{{ $placeholder }}'"
               class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] text-sm"
               autocomplete="off">

        {{-- Botón para limpiar la selección --}}
        <button type="button" x-show="valor" @click="limpiar()" x-cloak
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                title="Limpiar selección">
            <x-heroicon-o-x-mark class="w-4 h-4" />
        </button>

        {{-- Lista de opciones filtradas por lo escrito; se abre hacia arriba si no hay espacio abajo --}}
        <div x-show="abierto" x-cloak
             class="absolute z-20 w-full max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
             :class="arriba ? 'bottom-full mb-1' : 'mt-1'">
            <button type="button" @click="elegir('', '{{ $textoTodos }}')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:bg-gray-50"
                    x-show="texto === '' || '{{ $textoTodos }}'.toLowerCase().includes(texto.toLowerCase())">
                {{ $textoTodos }}
            </button>
            <template x-for="(etiqueta, id) in filtradas" :key="id">
                <button type="button" @click="elegir(id.toString(), etiqueta)"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50"
                        :class="valor === id ? 'bg-emerald-50 font-medium text-[#008c63]' : 'text-gray-700'"
                        x-text="etiqueta"></button>
            </template>
            <p class="px-3 py-2 text-sm text-gray-400" x-show="Object.keys(filtradas).length === 0">
                Sin resultados
            </p>
        </div>
    </div>
</div>

<script>
    // Lógica del buscador-select: filtra las opciones según el texto escrito.
    // La guarda evita registrar el componente más de una vez si hay varios en la página.
    if (!window.buscadorSelectRegistrado) {
        window.buscadorSelectRegistrado = true;
        document.addEventListener('alpine:init', () => {
            Alpine.data('buscadorSelect', (seleccionado = '', opciones = {}) => ({
            opciones: {},
            filtradas: {},
            valor: '',
            texto: '',
            abierto: false,
            arriba: false,

            init() {
                this.opciones = opciones ?? {};
                this.valor = seleccionado && this.opciones[seleccionado] ? String(seleccionado) : '';
                if (this.valor) {
                    this.texto = this.opciones[this.valor];
                }
                this.filtrar();
            },

            // Abre la lista hacia el lado con más espacio disponible
            abrir() {
                const rect = this.$el.getBoundingClientRect();
                this.arriba = rect.top > window.innerHeight - rect.bottom;
                this.abierto = true;
                this.filtrar();
            },

            filtrar() {
                const q = this.texto.toLowerCase();
                // Si el texto coincide exactamente con lo seleccionado, no filtrar
                if (this.valor && this.texto === this.opciones[this.valor]) {
                    this.filtradas = { ...this.opciones };
                    return;
                }
                this.filtradas = Object.fromEntries(
                    Object.entries(this.opciones).filter(([, etiqueta]) =>
                        etiqueta.toLowerCase().includes(q)
                    )
                );
            },

            elegir(id, etiqueta) {
                this.valor = id;
                this.texto = id === '' ? '' : etiqueta;
                this.abierto = false;
                this.filtrar();
            },

            limpiar() {
                this.elegir('', '{{ $textoTodos }}');
            },
        }));
    });
}
</script>
