<x-layout>
    <x-slot:title>
      Examination Results
    </x-slot:title>
  
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
      <section class="section">
        <div class="section-body">
          <div class="row">
            <div class="col-12">
              <div class="p-3 card">
                <h4 class="mx-2">Examination Results</h4>

                <livewire:examinations.examination-results />
                
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </x-layout>
  