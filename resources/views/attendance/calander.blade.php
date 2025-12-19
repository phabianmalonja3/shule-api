<x-layout>
    <x-slot:title>
        Weekly Attendance Report
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
             <livewire:student-attendance-calendar :student="$student"/>
            </div>
        </section>
    </div>

    <style>



    </style>
</x-layout>
