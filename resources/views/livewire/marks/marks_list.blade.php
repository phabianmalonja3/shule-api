<x-layout>
    <x-slot:title>
        Marks List
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <livewire:marks.marks-list  :class_result_flag="$class_result_flag" />
            </div>
        </section>
    </div>
    <script src="{{asset('js/jquery.min.js') }}"></script>

    
</x-layout>
