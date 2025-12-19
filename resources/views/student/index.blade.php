<x-layout>
    <x-slot:title>
      Student Dashboard
    </x-slot:title>
  
    <x-navbar />
    <x-admin.sidebar />
  
    <div class="main-content" style="min-height: 635px;">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4>Welcome to Your Dashboard, {{ auth()->user()->username }}</h4>
              </div>
              <div class="card-body">
                <div class="row">
  
                  <!-- Recent Assignments Section -->
                 <livewire:recent-assign-ment />
                  <!-- Recent Homeworks Section -->
                
  
                  <!-- Resources Section -->
                 <livewire:recent-resource />
                
                  <livewire:recent-home-work />
  
                  <!-- Results Section -->
                  <livewire:recent-student-mark />
  
                </div> <!-- End Row -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </x-layout>
  