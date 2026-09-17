{{--
    Etiqueta de estado unificada para todo el sistema.
    Uso: <x-estado-badge :estado="$registro->estado" />
    Para registros sin columna estado (ej. hitos), pasale el texto ya calculado:
    <x-estado-badge estado="Completado" :crudo="false" />
--}}
@php
$crudo = $crudo ?? true;
$clave = $crudo ? ($estado ?? '') : ($texto ?? '');
$etiqueta = ucfirst(str_replace('_', ' ', $clave));
$colores = match ($clave) {
    'pendiente' => 'bg-amber-100 text-amber-800',
    'en_progreso' => 'bg-blue-100 text-blue-800',
    'completada', 'completado', 'aprobado', 'pagada', 'activo' => 'bg-emerald-100 text-emerald-800',
    'cancelada', 'vencida', 'inactivo', 'rechazada' => 'bg-red-100 text-red-800',
    'revisado', 'aceptada' => 'bg-indigo-100 text-indigo-800',
    default => 'bg-gray-100 text-gray-700',
};
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colores }}">{{ $etiqueta }}</span>
