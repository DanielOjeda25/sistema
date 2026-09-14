<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría del sistema</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-4 flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por evento o modelo..."
                   class="border-gray-300 rounded-md w-64">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Evento</th>
                        <th class="px-4 py-3">Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditoria as $registro)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $registro->user->name ?? 'Sistema' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    {{ $registro->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $registro->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $registro->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $registro->event }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ class_basename($registro->auditable_type) }} #{{ $registro->auditable_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditoria->links() }}
    </div>
</x-app-layout>