<x-layout>
  <x-slot:title>
    Upload Marks
  </x-slot:title>

  <x-navbar />
  <x-admin.sidebar />

  <div class="main-content" style="min-height: 635px;">
    <section class="section">
      <div class="section-body">
        <div class="row">
          <div class="col-12">
            <div class="p-3 card">
              <h4 class="mx-2">Upload Marks</h4>

                <div class="mx-2 alert alert-info" role="alert">
                  <strong><i class="fa fa-exclamation-circle"></i> Important Notice:</strong> Please download the template below,
                  enter students’ marks as specified, save it, and thereafter upload it. {{ count(auth()->user()->streamsTeaches) > 1 ? 'If required, please make sure a subject and a stream have been selected before downloading the template.':'' }}
                </div>

              <livewire:marks-upload :classupload="0"
                                     :selectedSubject="$selectedSubject" 
                                     :subjectStatus="$subjectStatus"/>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
      <script src="{{asset('js/jquery.min.js') }}"></script>
</x-layout>