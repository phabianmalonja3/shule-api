<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Details of Homework</h4>
                </div>
                <div class="card-body">
                    <div class="container">
                        <h2>{{ $homework->title }}</h2>
                        <p><strong>Subject:</strong> {{ $homework->subject->name }}</p>
                        <p><strong>Class :</strong> Class
                            @foreach ($homework->streams as $index => $stream)
                            {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }} {{ $stream->alias }}
                                @if ($index == count($homework->streams) - 2) 
                                    & 
                                @elseif (!$loop->last) 
                                    , 
                                @endif
                            @endforeach
                        </p>
                        
                        <p><strong>Teacher:</strong> {{ $homework->teacher->name }}</p>
                        <p><strong>Description:</strong> {{ $homework->description }}</p>
                        <p><strong>Due Date:</strong>
                            {{ \Carbon\Carbon::parse($homework->due_date)->format('d M Y') }}</p>
                        {{-- <p><strong>Completion Rate:</strong> {{ number_format($completionRate, 2) }}%
                        </p> --}}
                        @if ($homework->file_path && Str::endsWith($homework->file_path, '.pdf'))
                        <p><strong>Attached File:</strong> 
                            <a href="{{ asset('storage/' . $homework->file_path) }}" target="_blank" class="btn btn-outline-danger">
                                <i class="bi bi-file-earmark-pdf"></i> Download PDF
                            </a>
                        </p>
                    @endif
                    

                    @if(auth()->user()->hasRole('student'))
                    @if(!$hasSubmitted)
                        <!-- File Upload Form for Students -->
                        <form wire:submit.prevent="uploadFile" class="my-2">
                            <div class="mb-3">
                                <label for="homeworkFile" class="form-label">Upload Your Homework</label>
                                <input type="file" class="form-control" id="homeworkFile" wire:model="file" accept=".pdf,.doc,.docx">
                                @error('file') <span class="text-danger">{{ $message }}</span> @enderror

                                <span wire:loading wire:target="file" class='py-2'>
                                    <i class="fa fa-spinner fa-spin"></i>please waiting while its checking file...
                    
                                </span>
                                <p class="mb-2 text-muted">
                                    Please upload a valid file in pdf, doc, or docx format. The maximum file size allowed is 5MB.
                                </p>
                            </div>
                            
                            <!-- Submit Button with Loading Spinner -->
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target='uploadFile'>Submit</span>
                                <span wire:loading wire:target='uploadFile' class="spinner-border spinner-border-sm"></span>
                            </button>
                        </form>
                    @else
                        <!-- Success Ribbon -->
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle me-2"></i> Homework already submitted!
                        </div>
                    @endif
                    @else
                    <!-- Progress Bar for Teachers/Admins -->
                    <div wire:poll.0.5s="calculateCompletionRate"> 
                        <div class="my-3 progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                style="width: {{ $completionRate }}%;" 
                                aria-valuenow="{{ $completionRate }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ number_format($completionRate, 2) }}%
                            </div>
                        </div>
                    </div>
                
                    <!-- List of students who have submitted the assignment -->
                    <div class="py-2">
                        <h5>Students who have submitted:</h5>
                        <ul class="list-group">
                            @foreach($homework->submissions as $index=>$submission)
                                <li class="list-group-item">
                                    {{$index +1  }} . 
                                    {{ $submission->student->user->name }} <!-- Assuming you have student relationship -->
                                  <!-- Assuming you have student relationship -->
                                  @if($submission->file_path)
                                  <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-white badge bg-info">
                                      <i class="fas fa-file-pdf"></i> <!-- Font Awesome PDF icon -->
                                      View Assignment
                                  </a>
                              @endif
                              
                                    <span class="text-white badge bg-success">Submitted</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                
                
                        <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back 
                           </a>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
