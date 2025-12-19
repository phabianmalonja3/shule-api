<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ \Str::title('List of Students') }}</h4>

                    <div>
                    </div>
                    <div>

                        @php
                            $unassignedCount = count($unassignedStudents); 
                            $showCheckboxes = $unassignedCount > 1; 
                        @endphp
                        
                        @if(auth()->user()->hasAnyRole(['academic teacher','header teacher','class teacher']))
                        
                            <a href="{{ route('students.create', ['classid' => $class[0]->id ?? null]) }}"
                                class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Student
                            </a>

                            @if($unassignedCount > 0)
                                <a href="#" class="btn btn-info" id="assignCombinationBtn">
                                    <i class="fas fa-plus"></i> Assign Combination
                                </a>
                            @endif
                        @endif

                        <div id="combinationAssignmentModal" class="modal" style="display: none;">
                            <div class="modal-content">

                                <form id="assignCombinationForm" method="POST" action="{{ route('students.assign.combination') }}"> 
                                    @csrf

                                    <input type="hidden" id="unassignedStudentCount" value="{{ $unassignedCount }}">

                                    <div class="form-group header-controls">
                                
                                        <div style="flex-grow: 1;" class="col-md-6"> 
                                            <select id="combination_id" name="combination_id" class="form-control" required>
                                                <option value="" selected disabled>-- Choose Combination --</option>
                                                @foreach ($combinations as $combination)
                                                    @if($combination->id != 1)
                                                        <option value="{{ $combination->id }}">
                                                            {{ $combination->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        @if($unassignedCount > 1)
                                            <div class="select-all-group">
                                                <input type="checkbox" id="selectAllStudents">
                                                <label for="selectAllStudents">Select All Students</label>
                                            </div>
                                        @endif
                                    </div>

                                    <hr>

                                    <div class="student-list-container table-responsive">
                                        <h6>List of Students</h6>

                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Student Name</th>
                                                     @if($showCheckboxes) <th>Action</th> @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($unassignedStudents as $student)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td> {{ $student->user->name }} </td>
                                                        @if($showCheckboxes)
                                                            <td> <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox"> </td>
                                                        @else
                                                            <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Assign</button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        @if($students->count() > 0)
                            <button wire:click="exportToExcel"
                                class="btn btn-primary">
                                <i class="fas fa-file-excel"></i> Download List

                                <span wire:loading='exportToExcel' wire:target='exportToExcel'  class="spinner-border spinner-border-sm"></span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-3 row">
                        @if(count($classes) > 1)
                            <div class="col-md-3">
                                <select wire:model.live="classId" class="form-control">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" wire:model.live="classId" value="{{ $classes[0]->id }}">
                        @endif
                        
                        @if(count($streams) > 1)
                            <div class="col-md-3">
                                <select wire:model.live="streamId" class="form-control">
                                    <option value="">Select Stream</option>
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->id }}">{{ $stream->schoolClass->name }} {{ $stream->alias }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" wire:model.live="streamId" value="{{ $streams[0]->id }}">                        
                        @endif
                        <div class="col-md-3">
                            <input wire:model.live="search" type="text" class="form-control"
                                placeholder="Search by name or reg. number">
                        </div>

                        <div class="col-md-3">
                            <select wire:model.live="sort" class="form-control">
                                <option value="asc">Sort by Created Date (Ascending)</option>
                                <option value="desc">Sort by Created Date (Descending)</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Stream</th>
                                    <th>Combination</th>
                                    <th>Parent Phone #</th>                                    
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $teacher_classes = [];
                                    foreach($students as $student){
                                        if(!in_array($student->stream->schoolClass->name, $teacher_classes)){
                                            $teacher_classes[] =  $student->stream->schoolClass->name;
                                        }
                                    }
                                @endphp
                                @forelse ($students as $student)

                                    @php
                                        $nameParts = explode(' ', $student->user->name);
                                        $firstName = $nameParts[0] ?? '';
                                        $middleName = $nameParts[1] ?? '';
                                        $surname = $nameParts[2] ?? '';
                                        $fullName = strtoupper($surname) . ', ' . ucfirst($firstName) . ' ' . ucfirst(substr($middleName, 0, 1)) . '.';

                                        $gender = trim($student->user->gender) === 'female' ? 'F' : 'M';
                                        $parent_phones = $student->parents->pluck('phone')->take(2)->join(', ');

                                        if(empty($parent_phones)){
                                            $parent_phones = $student->user->phone?? 'N/A';
                                        }

                                        $created_by = \App\Models\User::select('name')->where('id',$student->created_by)->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $fullName }} <br>
                                            <span class="text-muted" style="font-size: 0.86em;"> {{ $student->reg_number }} </span>    
                                        </td>
                                        <td>{{ $gender }}</td>
                                        <td>
                                            @if(count($teacher_classes) > 1)
                                                {{ $student->stream->schoolClass->name }} {{ $student->stream->name ?? 'N/A' }}
                                            @else
                                                {{ $student->stream->name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>{{ $student->combinations->first()->name?? 'N/A' }}</td>
                                        <td>{{ $parent_phones }}</td> 
                                        <td>{{ ucfirst($student->payment_status) }}</td>                                        
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle me-2"></i> Details
                                            </a>

                                            @if(auth()->user()->hasAnyRole(['academic teacher','header teacher','assistant headteacher','class teacher']))
                                                <a href="{{ route('students.edit', $student->id) }}"
                                                    class="btn btn-sm btn-warning"
                                                    @if (auth()->user()->id != $student->created_by && !auth()->user()->hasRole('academic teacher')) 
                                                        onclick="return false;" style="opacity: 0.65;" title="Can only be edited by an Academic Officer or {{ $created_by->name?? '' }}" @endif>
                                                        <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                    style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        @if (auth()->user()->id != $student->created_by && !auth()->user()->hasRole('academic teacher')) 
                                                            disabled style="opacity: 0.65;" title="Can only be deleted by an Academic Officer or {{$created_by->name?? ''}}" @endif
                                                            onclick="return confirm('Are you sure you want to delete the student?')">
                                                            <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
