<x-layout>
    <x-slot:title>
        Marks List
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <livewire:marks.marks-list 
                    :class_result_flag="$class_result_flag"
                    :editStatus="$editStatus" 
                    :selectedexaminationsType="$selectedexaminationsType"
                    :selectedClass="$selectedClass"
                    :selectedSubject="$selectedSubject"
                    :selectedStream="$selectedStream"
                    :search="$search"
                    :accordionKey="$accordionKey"
                    :subjectStatus="$subjectStatus" />
            </div>
        </section>
    </div>
    <script src="{{asset('js/jquery.min.js') }}"></script>
    <script>

        function openAccordionPanel(monthNumber) {
            if (monthNumber) {
                const targetId = `#panel-body-inside-${monthNumber}`;
                $(targetId).collapse('show');
            }
        }

        window.addEventListener('open-accordion-panel', function (event) {
            const monthNumber = event.detail.monthNumber;
            openAccordionPanel(monthNumber);
        });
    </script>
</x-layout>