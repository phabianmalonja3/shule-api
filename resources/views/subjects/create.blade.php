<x-layout>
    <x-slot:title>
        {{ isset($subject) ? 'Edit Subject' : 'Add New Subject' }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                       <livewire:subjects.subject-create :subject="$subject" />
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
