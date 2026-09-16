<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis proyectos</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-6">
            <p class="text-sm text-gray-500 mb-6">Acá podés seguir el avance de cada proyecto, sus hitos y el material aprobado para vos.</p>

            @forelse ($proyectos as $proyecto)
                @php
                    $total = $proyecto->tareas_count;
                    $hechas = $proyecto->tareas_completadas;
                    $avance = $total > 0 ? (int) round($hechas * 100 / $total) : 0;
                @endphp
                <a href="{{ route('proyectos.show', $proyecto) }}"
                   class="block bg-white rounded-2xl shadow-sm hover:shadow-md transition p-6 mb-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $proyecto->nombre }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $proyecto->pm?->name ? 'A cargo de '.$proyecto->pm->name : '' }}
                                @if ($proyecto->fecha_inicio)
                                    · desde {{ $proyecto->fecha_inicio->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $proyecto->estado === 'completado' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $proyecto->estado === 'en_progreso' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $proyecto->estado === 'pendiente' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $proyecto->estado === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Avance del proyecto</span>
                            <span>{{ $avance }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-[#00b87d] h-2.5 rounded-full" style="width: {{ $avance }}%"></div>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-6 text-sm text-gray-600">
                        <span>✅ {{ $hechas }}/{{ $total }} tareas</span>
                        <span>🏁 {{ $proyecto->hitos_completados }} hitos completados</span>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow-sm p-10 text-center text-gray-500">
                    Todavía no tenés proyectos asociados.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
