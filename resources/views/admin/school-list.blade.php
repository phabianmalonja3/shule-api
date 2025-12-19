<x-layout>
  <x-slot:title>
    School List Page
  </x-slot:title>
  <x-navbar />
  <x-admin.sidebar />
  <div class="main-content" style="min-height: 635px;">
    <section class="section">
      <div class="section-body">

       <livewire:school.school-list />
      </div>
    </section>

  </div>
</x-layout>