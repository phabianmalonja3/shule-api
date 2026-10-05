@props([
    'label',
    'error' => null,
    'required' => false,
    'type' => 'text',
])

<label class="block">
    <span class="mb-2 block text-sm font-bold text-slate-700">
        {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
    </span>
    <input
        type="{{ $type }}"
        @if ($required) required @endif
        {{ $attributes->class([
            'h-12 w-full rounded-xl border bg-white px-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10',
            'border-red-400' => $error,
            'border-slate-300' => ! $error,
        ]) }}
    >
    @if ($error)<span class="mt-1.5 block text-xs font-medium text-red-600">{{ $error }}</span>@endif
</label>
