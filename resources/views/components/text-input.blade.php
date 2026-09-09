@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm']) }}>
