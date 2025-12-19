<x-layout>
    <x-slot:title>
        Teacher Of {{ $student->user->name }}
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            

            <div class="section-body">
                <div class="row">
                  
                 
<div class="container">
    <div class="card ">
    
        <div class="py-2 card-head">
            <h2 class="px-2 mb-1 ">Teachers for {{ $student->user->name }} (Stream: {{ $student->stream->name }})</h2>
    
        </div>
        <div class=" card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th> <!-- Serial Number -->
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Subject Name</th>
                            <th>Role</th>
    
                            @if(auth()->user()->hasAnyRole(['header teacher','academic teacher' ,'assistant headteacher']))
                            
                            <th>Status</th>
                            @endif
                            
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $index => $teacher)
                            <tr>
                                <!-- Serial Number -->
                                <td>{{ $index + 1 }}</td>
    
                                <!-- Teacher Details -->
                                <td>{{ $teacher->teacher->name }}</td>
                                <td>{{ $teacher->teacher->phone }}</td>
                                <td>{{ $teacher->subject->name }}</td>
                                
    
                               
                             
                               
    
                                <!-- Actions -->
                                <td class="">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-info-circle "></i> Details
                                        </a>   
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No teachers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
    
                
            </div>
        </div>
    </div>
              </div>         
                       
                  
                  
                </div>
        </section>
    </div>

    {{-- <script>
const downloadExcelButton = document.getElementById('download-excel-button');
const loadingSpinner = document.getElementById('loading-spinner');
const form = document.getElementById('myForm'); // Get the form element

downloadExcelButton.addEventListener('click', async (event) => {
  event.preventDefault(); // Prevent default form submission

  try {
    loadingSpinner.style.display = 'inline-block'; 

    const response = await fetch('/start-excel-download', {
      method: 'POST', // Use POST method for form submission
      // Include any necessary data in the request body 
      // (e.g., form data)
      body: new FormData(form) 
    });

    if (response.ok) {
      await new Promise(resolve => setTimeout(resolve, 2000)); 
      window.location.href = '/download-excel'; 
    } else {
      console.error('Error starting download:', response.status);
      // Handle error (e.g., display an error message to the user)
    }
  } catch (error) {
    console.error('Error:', error);
    // Handle error (e.g., display an error message to the user)
  } finally {
    loadingSpinner.style.display = 'none'; 
  }
});
    </script> --}}
</x-layout>

