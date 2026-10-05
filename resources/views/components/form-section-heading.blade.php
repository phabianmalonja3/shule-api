@props(['number', 'title', 'description'])

<div class="mb-8 border-b border-slate-200 pb-6">
    <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-700">Section {{ $number }}</p>
    <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900">{{ $title }}</h2>
    <p class="mt-1 text-sm leading-6 text-slate-500">{{ $description }}</p>
</div>
