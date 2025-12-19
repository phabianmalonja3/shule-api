<x-layout>
    <x-slot:title>
        {{ isset($assignment) ? 'Edit Assignment' : 'Create Assignment' }}
        
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                      <livewire:assignments.assignment-create  :assignment="$assignment ?? null"/>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>