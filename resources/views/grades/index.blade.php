<x-layout>
    <x-slot:title>
        Grade List
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card">                    
                    <div class="card-header">
                        <h4>Grades</h4>
                    </div>
                    <livewire:grade-manager />
                </div>
        </section>
    </div>



 

</x-layout>
