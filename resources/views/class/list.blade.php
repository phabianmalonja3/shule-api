<x-layout>
    <x-slot:title>
        List of Classes
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @role(['header teacher','headteacher','assistant headteacher','academic teacher'])
                            @if ((count($missing_streams) > 0 || count($missing_teachers) > 0) && count($classes) == count($missing_streams))
                                
                                @php
                                    $message = '';
                                    if (auth()->user()->hasAnyRole(['header teacher', 'assistant headteacher'])){
                                        $message = 'inform or remind your <strong>Academic Teacher</strong> to do so';
                                    }elseif(auth()->user()->hasRole('academic teacher')){
                                        $message = 'do so';
                                    }
                                @endphp

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No settings have been done so far, please {!! $message !!}.
                                </div>

                            @elseif (count($missing_streams) > 0 || count($missing_teachers) > 0 )
                                
                                @php
                                    $message = '';
                                    if (auth()->user()->hasAnyRole(['header teacher', 'assistant headteacher'])){
                                        $message = 'remind your <strong>Academic Teacher</strong> to finalize the settings';
                                    }elseif(auth()->user()->hasRole('academic teacher')){
                                        $message = 'finalize the settings';
                                    }
                                @endphp

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Settings to the classes below are partial or have not been done, please {!! $message !!}.
                                    <ul>
                                       
                                        @if(count($missing_streams) > 0)
                                            @foreach ($missing_streams as $class)
                                                <li><strong></strong> {{ $class }}</li>
                                            @endforeach
                                        @elseif(count($missing_teachers) > 0)
                                            @foreach ($missing_teachers as $class)
                                                <li><strong></strong> {{ $class }}</li>
                                            @endforeach
                                        @endif

                                    </ul>
                                </div>

                            @elseif (count($missing_subject_teachers) > 0)
                                @php
                                    $message = '';
                                    if (auth()->user()->hasAnyRole(['header teacher', 'assistant headteacher'])){
                                        $message = 'remind your <strong>Academic Teacher</strong> to assign subject teachers';
                                    }elseif(auth()->user()->hasRole('academic teacher')){
                                        $message = 'assign subject teachers';
                                    }
                                @endphp

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    The streams below have subjects without assigned teachers, please {!! $message !!}.
                                    <ul>
                                        @foreach ($missing_subject_teachers as $missing)
                                            <li>
                                                <strong>Class:</strong> {{ $missing['class'] }} - 
                                                <strong>Stream:</strong> {{ $missing['stream'] }} - 
                                                <strong>Subject:</strong> {{ $missing['subject'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        @endrole

                        <div class="card">
                        
                            <div class="card-header">
                                <h4>  {{ \Str::title('List of Classes') }}</h4>
                                <div class="card-header-form d-flex justify-content-between align-items-center">
                                    <!-- Button to add a new class -->
                                    @role(['academic teacher','header teacher'])
                                    <a href="{{ route('classes.create') }}" class="mr-3 btn btn-success">
                                        <i class="fas fa-plus"></i> Add New Class
                                    </a>
                                    @endrole
                                    <!-- Search form -->

                                </div>
                            </div>

                            <div class="p-3 card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr >
                                                <th>#</th> <!-- Serial Number Column -->
                                                <th>Class</th>
                                                <th>Streams</th>
                                                <th>Class Teacher(s)</th>  
                                                @if ((count($missing_streams) > 0 || count($missing_teachers) > 0) && count($classes) == count($missing_streams) && !auth()->user()->hasRole('academic teacher')) @else <th class="ml-4">Actions</th> @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($classes as $index => $class)
                                                <tr>
                                                    <td >{{ $index + 1 }} </td>
                                                    <td> {{ $class->name }} </td>
                                                    <td>
                                                        @if ($class->streams->isNotEmpty() && empty($class->teacher_class_id))
                                                            @foreach ($class->streams as $index=> $stream)
                                                                {{ $stream->name }}
                                                                    @if ($index == count($class->streams) - 2) 
                                                                    & 
                                                                @elseif (!$loop->last) 
                                                                    , 
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            @if($class->teacher_class_id != '')
                                                                N/A
                                                            @else
                                                                Not Created
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($class->streams->count() > 0)
                                                            @php
                                                                // Get unique teacher names from streams
                                                                $uniqueStreamTeachers = $class->streams
                                                                    ->filter(fn($stream) => $stream->teacher) // Keep only streams with teachers
                                                                    ->pluck('teacher') // Extract teacher names
                                                                    ->unique(); // Remove duplicates
                                                            @endphp
                                                    
                                                            @if ($uniqueStreamTeachers->isNotEmpty())
                                                                @foreach ($uniqueStreamTeachers as $teacher)
                                                                    @php
                                                                        $names = explode(' ', $teacher->name); // Split the name into parts
                                                                    @endphp

                                                                    <a href="{{ route('teachers.show', $teacher->id) }}" >
                                                                        {{ $names[2] ?? $names[0] }}{{ !$loop->last ? ', ' : '' }} <!-- Safely handle names -->
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                Not Assigned
                                                            @endif
                                                        @else
                                                            @if ($class->teacher)
                                                                <a href="{{ route('teachers.show', $teacher->id) }}" > {{ $class->teacher->name }} </a>
                                                            @else
                                                                Not Assigned
                                                            @endif
                                                        @endif
                                                    </td>
 
                                                    <!-- Action Buttons -->
                                                    @if ((count($missing_streams) > 0 || count($missing_teachers) > 0) && count($classes) == count($missing_streams) && !auth()->user()->hasRole('academic teacher')) 

                                                    @else
                                                    <td>
                                                        <div
                                                            class="flex-row gap-3 d-flex justify-content-left align-items-left">
                                                            <!-- Details Button with Icon -->
                                                            <a href="{{ route('classes.show', ['class' => $class->id]) }}"
                                                                class="mb-2 btn btn-info btn-sm d-flex align-items-center"
                                                                style="  margin-right: 4px; margin-top:7px;">
                                                                <i class="fas fa-info-circle me-2"></i> @if(auth()->user()->hasRole('academic teacher')) Manage Class @else View Details @endif
                                                            </a>

                                                            @if(auth()->user()->hasAnyRole(['academic teacher']))

                                                            <!-- Edit Button with Icon -->
                                                           
                                                                <a href="{{ route('classes.edit', ['class' => $class->id]) }}"
                                                                    class="mb-2 btn btn-warning btn-sm d-flex align-items-center "
                                                                  
                                                                    style="margin-right: 4px; margin-top:7px;
                                                                        @if(count($class->streams) > 0 && $class->teacher_class_id == '')
                                                                           opacity: 0.65;
                                                                        @endif                                                                    
                                                                    " 

                                                                    @if(count($class->streams) > 0 && $class->teacher_class_id == '')
                                                                        onclick="return false" title="Please use the 'Manage Class' button to edit this class."
                                                                    @endif
                                                                    >
                                                                    <i class="fas fa-edit me-2"> </i> Edit
                                                                </a>
                                                                
                                                                

                                                                <form  action="{{ route('classes.destroy', ['class' => $class->id]) }}"
                                                                       method="POST">
                                                                
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="btn btn-danger btn-sm d-flex align-items-center"
                                                                            style="max-width: 200px; margin-top: 8px;" 
                                                                            id="deleteButton"  
                                                                            @if($class->created_by_system) 
                                                                                disabled style="opacity: 0.65;" title="System generated classes cannot be deleted." 
                                                                            @else
                                                                                onclick=" return confirm('Are you sure you want to delete this class?');"
                                                                            @endif>
                                                                                    
                                                                                <i class="fas fa-trash-alt me-2"> </i> Delete
                                                                    </button>
                                                                </form>
                                                            @endif

                                                                {{-- @endrole --}}
                                                        </div>
                                                    </td>
                                                    @endif


                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No classes found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    <div class="px-2">
                                        {{-- {{ $classes->links() }} --}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="formModal"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel">Add New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="classForm" action="{{ route('classes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" id="methodInput" name="_method" value="">
                            <input type="hidden" name="class_id" id="class_id">

                            <div class="form-group">
                                <label for="name">Class Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Enter class name">
                            </div>
                            <div class="text-danger" id="nameError"></div>

                            <div class="form-group">
                                <label for="teacher">Select Class Teacher (Optional)</label>
                                <select name="teacher_class_id" id="teacher" class="form-control">
                                    <option value="" disabled selected>Select Teacher</option>

                                </select>
                            </div>
                            <div class="text-danger" id="teacherError"></div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="classForm" class="btn btn-primary" id="submitButton">Create
                            Class</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editClassModal" tabindex="-1" role="dialog" aria-labelledby="formModal"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewLabel">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form to edit class -->
                        <form id="classForm1" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="class_id" id="class_id"> <!-- Hidden field for class_id -->

                            <div class="form-group">
                                <label for="name">Class Name</label>
                                <input type="text" class="form-control" name="name" id="name1"
                                    placeholder="Enter class name" required>
                            </div>
                            <div class="text-danger" id="nameError"></div>

                            <div class="form-group">
                                <label for="teacher">Select Class Teacher (Optional)</label>
                                <select name="teacher_class_id" id="teacher1" class="form-control">
                                    <option value="" disabled selected>Select Teacher</option>

                                </select>
                            </div>
                            <div class="text-danger" id="teacherError"></div>

                            <!-- Hidden field for school_id -->
                            <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="classForm1" class="btn btn-primary" id="submitButton">Update
                            Class</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layout>
