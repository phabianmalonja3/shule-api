<x-layout>
    <x-slot:title>Assignments</x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
               <livewire:assignment-list />
            </div>
        </section>
    </div>
</x-layout>