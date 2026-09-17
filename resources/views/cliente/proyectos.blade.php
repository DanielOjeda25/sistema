<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis proyectos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-6">
            <p class="text-sm text-gray-500 mb-6">Acá podés seguir el avance de cada proyecto, sus hitos y el material aprobado para vos.</p>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($proyectos as $proyecto)
                    @php
                        $total = $proyecto->tareas_count;
                        $hechas = $proyecto->tareas_completadas;
                        $avance = $total > 0 ? (int) round($hechas * 100 / $total) : 0;
                    @endphp
                    <a href="{{ route('proyectos.show', $proyecto) }}"
                       class="group flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-bold text-gray-800 leading-snug group-hover:text-[#008c63] transition">{{ $proyecto->nombre }}</h3>
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                {{ $proyecto->estado === 'completado' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $proyecto->estado === 'en_progreso' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $proyecto->estado === 'pendiente' ? 'bg-gray-100 text-gray-600' : '' }}
                                {{ $proyecto->estado === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $proyecto->pm?->name ? 'A cargo de '.$proyecto->pm->name : '' }}
                            @if ($proyecto->fecha_inicio)
                                · desde {{ $proyecto->fecha_inicio->format('d/m/Y') }}
                            @endif
                        </p>

                        <div class="mt-auto pt-4">
                            <div class="flex justify-between text-[11px] text-gray-500 mb-1">
                                <span>Avance</span>
                                <span class="font-semibold text-[#008c63]">{{ $avance }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-[#00b87d] h-2 rounded-full transition-all" style="width: {{ $avance }}%"></div>
                            </div>

                            <div class="mt-3 flex gap-4 text-xs text-gray-600">
                                <span>✅ {{ $hechas }}/{{ $total }} tareas</span>
                                <span>🏁 {{ $proyecto->hitos_completados }} hitos</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm p-10 text-center text-gray-500">
                        Todavía no tenés proyectos asociados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
