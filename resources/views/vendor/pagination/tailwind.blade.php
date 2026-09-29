@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination navigation') }}" class="mt-5">
        {{-- Resumen de lo que se esta viendo --}}
        <p class="text-center text-xs text-gray-400 mb-3">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </p>

        <div class="flex items-center justify-center gap-1.5">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs font-semibold text-gray-300 cursor-not-allowed">
                    <x-heroicon-o-chevron-left class="h-4 w-4" /> Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-600 hover:border-[#00b87d] hover:text-[#008c63]">
                    <x-heroicon-o-chevron-left class="h-4 w-4" /> Anterior
                </a>
            @endif

            {{-- Numeros de pagina (con separador cuando hay salto) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-xs text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center min-w-10 px-3 py-2 rounded-lg border border-[#00b87d] bg-[#00b87d] text-xs font-bold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center min-w-10 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-600 hover:border-[#00b87d] hover:text-[#008c63]">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-600 hover:border-[#00b87d] hover:text-[#008c63]">
                    Siguiente <x-heroicon-o-chevron-right class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-xs font-semibold text-gray-300 cursor-not-allowed">
                    Siguiente <x-heroicon-o-chevron-right class="w-4 h-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
