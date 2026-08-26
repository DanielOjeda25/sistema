@props([
    'id' => 'password',
    'name' => 'password',
    'minimo' => null,     // exige un mínimo de caracteres y avisa mientras se escribe
    'confirmaDe' => null, // id del campo con el que debe coincidir
])

{{--
    Campo de contraseña con botón para mostrar/ocultar lo escrito.

    El input sale con type="password" de verdad en el HTML y el botón lo cambia
    tocando el atributo, sin x-model: así el navegador y los gestores de
    contraseñas lo siguen tratando como un campo de contraseña normal, y si
    Alpine no cargara el campo funciona igual.

    Con `minimo` o `confirmaDe` avisa en el momento si falta algo, en vez de
    esperar a que la persona mande el formulario. La validación del servidor
    sigue siendo la que manda: esto solo evita que escriba dos veces.
--}}
<div
    x-data="{
        visible: false,
        largo: 0,
        tocado: false,
        minimo: {{ $minimo ?? 0 }},
        campo() { return document.getElementById('{{ $id }}') },
        alternar() {
            const c = this.campo();
            this.visible = !this.visible;
            c.type = this.visible ? 'text' : 'password';
        },
        @if ($confirmaDe)
        noCoincide() {
            const otro = document.getElementById('{{ $confirmaDe }}');
            return this.largo > 0 && otro && otro.value !== this.campo().value;
        },
        @endif
    }"
    class="mt-1"
>
    <div class="relative">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            x-on:input="largo = $el.value.length"
            x-on:blur="tocado = true"
            @if ($minimo) minlength="{{ $minimo }}" @endif
            {{ $attributes->merge(['class' => 'block w-full pe-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}
        >

        <button
            type="button"
            x-on:click="alternar()"
            class="absolute inset-y-0 end-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-indigo-600"
            :aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            :title="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            tabindex="-1"
        >
            {{-- Ojo abierto: se ve cuando el texto está oculto --}}
            <svg x-show="!visible" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>

            {{-- Ojo tachado: se ve cuando el texto está visible --}}
            <svg x-show="visible" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            </svg>
        </button>
    </div>

    @if ($minimo)
        <p x-show="largo === 0" class="mt-1 text-xs text-gray-500">
            Mínimo {{ $minimo }} caracteres.
        </p>
        <p x-show="largo > 0 && largo < minimo" x-cloak class="mt-1 text-xs text-amber-600">
            Faltan <span x-text="minimo - largo"></span> caracteres (mínimo {{ $minimo }}).
        </p>
        <p x-show="largo >= minimo" x-cloak class="mt-1 text-xs text-green-600">
            Longitud correcta.
        </p>
    @endif

    @if ($confirmaDe)
        <p x-show="tocado && noCoincide()" x-cloak class="mt-1 text-xs text-red-600">
            Las contraseñas no coinciden.
        </p>
        <p x-show="largo > 0 && !noCoincide()" x-cloak class="mt-1 text-xs text-green-600">
            Coinciden.
        </p>
    @endif
</div>
