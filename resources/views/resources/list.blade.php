<x-layout>
    <x-slot:title>
        Resource List - {{ $subject->name }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <div class="">
            @livewire('subject-resources', ['subject' => $subject,'resourcess'=>$resources])

        </div>
    </div>

</x-layout>
