<x-layout>
    <x-slot:title>
        {{ $class->name }} Details
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <h2>{{ $class->name }} Details</h2>

                <!-- Display Streams and Add New Stream Form in Same Row -->
                <div class="mt-4 row">
                    <!-- Stream List -->

                    <!-- Add New Stream Form -->
                    @role('academic teacher')
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Add New Stream</h4>
                                </div>
                                <div class="card-body">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    <form action="{{ route('streams.store', ['school_class_id' => $class->id]) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="school_class_id" value="{{ $class->id }}">

                                        <!-- Stream Name -->
                                        <div class="form-group">
                                            <label for="name">Stream Name</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>

                                        <!-- Teacher Select Box -->
                                        <div class="form-group">
                                            <label for="teacher_id">Assign Stream Teacher (Optional)</label>
                                            <select name="teacher_id" class="form-control">
                                                <option value="" disabled selected>Select a Teacher</option>
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Stream Selection -->
                                        <div class="mb-3 input-group">
                                            <button type="submit" class="btn btn-sm btn-primary">Add Stream</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endrole
                </div>

                @if(count($class->streams) > 0 && empty($class->teacher_class_id))
                <div class="mt-4 row">
                    <!-- Stream List -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Streams for {{ $class->name }}</h4>
                            </div>
                            <div class="card-body">
                                @if ($class->streams->isNotEmpty())
                                    <div class="">
                                        <table class="table table-bordered table-striped ">
                                            <thead class="thead-dark" style="text-align: left">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Alias</th>
                                                    <th>Class Teacher</th>
                                                    @role(['academic teacher'])
                                                        <th>Actions</th>
                                                    @endrole
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($class->streams as $index=>$stream)
                                                    <tr id="stream-row-{{ $stream->id }}">
                                                        <td>
                                                            {{ $index +1 }}
                                                        </td>
                                                        <td>
                                                            <span class="text-white rounded-circle d-flex align-items-center justify-content-center bg-primary"
                                                                style="width: 40px; height: 40px; font-size: 1rem;">
                                                                {{ $stream->name }}
                                                            </span>
                                                        </td>
                                                        <td> {{ $stream->alias }} </td>
                                                        <td>
                                                            {{ $stream->teacher->name ?? 'N/A' }}
                                                            <div class="text-muted" style="font-size: 0.85em;">
                                                                {{ $stream->teacher->phone ?? 'N/A' }}
                                                            </div>
                                                        </td>
                                                        @role(['academic teacher'])
                                                            <td>
                                                                <a href="{{ route('streams.edit', ['stream' => $stream->id]) }}" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                                <form action="{{ route('streams.destroy', $stream->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        @endrole
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center p-3">
                                        <p>No Streams found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <livewire:teacher.assign-teacher :class="$class" />
                {{-- <!-- Timetable Form -->

                @if (count($timetables) == 0)
                    @role('academic teacher')
                        <div class="mt-4 row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Set Timetable for {{ $class->name }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('timetable.store', ['class_id' => $class->id]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf

                                            <!-- Display validation errors -->
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <!-- Display success message -->
                                            @if (session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif

                                            <!-- Excel Upload Form -->
                                            <div id="excelForm">
                                                <div class="form-group">
                                                    <label for="timetable_file">Upload Timetable (Excel)</label>
                                                    <input type="file" name="timetable_file"
                                                        class="form-control @error('timetable_file') is-invalid @enderror"
                                                        accept=".xls,.xlsx">
                                                    @error('timetable_file')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                            <a href="{{ route('timetable.downloadTemplate', ['classId' => $class->id]) }}"
                                                id="excel-download" class="btn btn-sm btn-success">
                                                Download Template
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endrole
                @endif --}}
                @if(count($class->streams) > 0)
                    @if(count($timetables )> 0)
                        <!-- Timetable Display with Static Data -->
                        <div class="mt-4 row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>{{ $class->name }} Timetable</h4>
                                        <form method="">

                                            <button type="button" class="btn btn-danger"
                                                    data-toggle="modal" data-target="#exampleModalCenter"><i class="fas fa-sync"></i> Reset

                                                    {{-- <span id="loading-spinner" class="spinner-border spinner-border-sm" style="display: none;"></span> --}}
                                                </button>
                                                
                                        </form>

                                        

                                    


                                    </div>
                                        <div class="card-body">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-primary">
                                                    <tr>

                                                        <th>#</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Subject</th>
                                                        <th>Teacher</th>
                                                        <th>Stream</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($timetables as $day => $dayTimetables)
                                                        <!-- Display the day name -->
                                                        <tr class="table-info">
                                                            <td colspan="6" class="text-center">
                                                                <strong>{{ $day }}</strong></td> <!-- Day -->
                                                        </tr>

                                                        <!-- Loop through the timetables for each day -->
                                                        @foreach ($dayTimetables as $index => $timetable)
                                                            <tr>
                                                                <!-- Start Time -->
                                                                <td>{{ $index + 1 }}</td> <!-- Start Time -->
                                                                <td>{{ $timetable->start_time }}</td> <!-- Start Time -->
                                                                <td>{{ $timetable->end_time }}</td> <!-- End Time -->
                                                                <td>{{ $timetable->subject['name'] }}</td> <!-- Subject -->
                                                                <td>{{ $timetable->teacher->name }}</td> <!-- Teacher -->
                                                                <td>{{ $timetable->stream->name }}</td> <!-- Stream -->
                                                            </tr>
                                                        @endforeach
                                                    @empty
                                                        <!-- Display a message if no timetables are available -->
                                                        <tr>
                                                            <td colspan="6" class="text-center">No timetable available.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    <!-- Custom styles for better color contrast -->
                                    <style>
                                        .table-primary {
                                            background-color: #cce5ff;
                                            /* Light blue header */
                                        }

                                        .table-info {
                                            background-color: #e7f3fe;
                                            /* Light blue for odd rows */
                                        }

                                        .table-secondary {
                                            background-color: #f8f9fa;
                                            /* Light grey for even rows */
                                        }

                                        /* .table th {
                                            text-align: center;
                                            /* Center align the headers */
                                        
                                        

                                        .table td {
                                            vertical-align: middle;
                                            /* Center align the content */
                                        }
                                    </style>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </div>

   
    
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Action To delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Do You want To delete This Time Table  ?
            </div>

            <form method="POST"  id="resetForm">

                @csrf
             
                <input type="text" class="school-class-id" hidden name="class" value="{{ $class->id }}">
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" class="btn btn-danger" id="reset-btn"> <i class="fa fa-trash" aria-hidden="true" data-id={{ $class->id }}></i> Yes <i id="load-spinner" class=" fa fa-spinner fa-spin" style="display: none;"></i> </button>
                    
                    
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
                
            </form>
              
            </div>
        </div>
    </div>
    </div>
    
   
   
</x-layout>
<script>


    $(document).ready(function() {
        $('#reset-btn').on('click', function() 
        {

            const loadingSpinner = document.getElementById('load-spinner');
            
           
            
            
            
            
            
            
            
            // Get the data from the button's data attributes
            var id = $('.school-class-id').val();
            
            $('#resetForm').attr('action', '/timetable-reset/' + id);
            
            
            
            $('#resetForm').on('submit', function (e) {

                
                // $('#load-spinner').style.display='inline';
                
                e.preventDefault();
                loadingSpinner.style.display = 'inline-block'; 
    
      var form = $(this);
          $.ajax({
        url: form.attr('action'),
            type: 'DELETE',
           data: {
            _token: "{{ csrf_token() }}",
            
           },

           success: function (response) {
            loadingSpinner.style.display = 'inline-none'; 
                    $('#exampleModalCenter').modal('hide'); // Close the modal
                console.log(response)
                
                      location.reload();

                      iziToast.success({
    title: 'Hello, world!',
    message: 'This awesome plugin is made by iziToast',
    position: 'bottomRight'
  });
                          },
                          
                
 error: function (xhr) {
    loadingSpinner.style.display = 'inline-none'; 
 console.log(xhr)
 let errors = xhr.responseJSON.errors;

 console.log(errors)
 let errorMessages = '';
 for (let key in errors) {
 errorMessages += errors[key] + '\n';
 }

 }
 

    
    
    
    })
    })

        })})
    </script>

<script src="{{ asset('assets/bundles/izitoast/js/iziToast.min.js') }}"></script>