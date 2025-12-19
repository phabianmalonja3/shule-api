<x-layout>
  <x-slot:title>
    Class Marsk Upload
  </x-slot:title>

  <x-navbar />
  <x-admin.sidebar />

  <div class="main-content" style="min-height: 635px;">
    <section class="section">
      <div class="section-body">

            <div class="p-3 card">
              <h4 class="mx-2">Class Marks Upload</h4>
             
                <div class="mx-2 alert alert-info" role="alert">
                  <strong><i class="fa fa-exclamation-circle"></i> Important Notice:</strong> Please download and use the template below. Note, a class MUST be selected before downloading the template.
                </div>
              <livewire:marks-upload :classupload="1"
                                     :selectedSubject="$selectedSubject"
                                     :subjectStatus="$subjectStatus" />
            </div>


      </div>
    </section>
  </div>
</x-layout>