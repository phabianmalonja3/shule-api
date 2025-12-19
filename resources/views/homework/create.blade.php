<x-layout>
    <x-slot:title>
        {{ isset($homework) ? 'Edit Homework' : 'Upload Homework' }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">

                
               <livewire:homeworks.home-work-create />
            </div>
        </section>
    </div>
</x-layout>
