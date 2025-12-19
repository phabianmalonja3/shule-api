<x-layout>
    <x-slot:title>
        {{ __('Examination Results') }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <livewire:student-result />
               
            </div>
        </section>
    </div>
</x-layout>
