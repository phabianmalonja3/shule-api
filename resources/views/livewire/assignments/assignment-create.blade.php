<div>
    <div class="card">
        <div class="card-header">
            <h4>{{ isset($assignment) ? 'Edit Assignment' : 'Create Assignment' }}</h4>
        </div>
        <div class="card-body">

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- General Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Whoops!</strong> There were some
                    problems with your input.<br><br>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form wire:submit.prevent="{{ isset($assignment) ? 'updateAssignment' : 'storeAssignment' }}" enctype="multipart/form-data">
                <!-- Assignment Title -->
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" wire:model="title" id="title" class="form-control" placeholder="Enter Assignment Title" required value="{{ $assignment->title ?? '' }}">
                    @error('title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Select Subject -->
               @if($subjects->count() > 1)
               <div class="form-group">
                <label for="subject_id">Subject</label>
                <select wire:model="subject_id" id="subject_id" class="form-control" required>
                    <option value="">-- Select Subject --</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
               @endif

               <div class="form-group">
                {{-- @if ($streams->count() > 1) --}}
                    <label for="all_streams">Apply to all streams?</label>
                    <select wire:model.live="all_streams" class="form-control">
                        <option value="">Select Option </option>
                        <option value="yes">Yes, apply to all streams</option>
                        <option value="no">No, select specific streams</option>
                    </select>
            
            </div>

            @if ($all_streams === 'no')
                <!-- Select Stream -->
             @if($streams->count() > 1)
             <div class="form-group">
                <label for="streams">Stream</label>
                @foreach ($streams as $stream)
                    <div class="form-check">
                        <input type="checkbox" wire:model="streams_ids" value="{{ $stream->id }}" class="form-check-input" id="stream_{{ $stream->id }}">
                        <label class="form-check-label" for="stream_{{ $stream->id }}">
                            Class {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }} {{ $stream->alias }} 
                        </label>
                    </div>
                @endforeach
                @error('streams')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

             @endif
             @endif
                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea wire:model="description" id="description" class="form-control" rows="4" placeholder="Enter Assignment Description"></textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Upload Assignment File -->
                <div class="form-group">
                    <label for="file">Assignment File</label>
                    
                    <input type="file" wire:model="file" id="file" class="form-control-file" accept=".pdf,.doc,.docx" />

                    <span wire:loading wire:target="file" class='py-2'>
                        <i class="fa fa-spinner fa-spin"></i>please waiting while its checking file...
        
                    </span>
<p class="mb-2 text-muted">
    
    Please upload a valid file in .pdf, .doc, or .docx format. The maximum file size allowed is 5MB.
</p>

                    @if (isset($assignment) && $assignment->file_path)
                        <p class="mt-2">Current File:
                            <a href="{{asset('storage/'.$assignment->file_path)}}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-file-pdf"></i> View
                            </a>
                        </p>
                    @endif
                    
                    @error('file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Due Date -->
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="datetime-local" wire:model="due_date" id="due_date" class="form-control" required>
                    @error('due_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button with Loading Indicator -->
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ isset($assignment) ? 'Update Assignment' : 'Add Assignment' }}</span>
                    
                   @if(isset($assignment))
                   <span wire:loading wire:target='updateAssignment'>
                    <i class="fas fa-spinner fa-spin"></i> Processing...
                </span>
                @else
                <span wire:loading wire:target='storeAssignment'>
                    <i class="fas fa-spinner fa-spin"></i> Processing...
                </span>
                   @endif
                    
                  
                   
                </button>
            </form>
        </div>
    </div>
</div>
