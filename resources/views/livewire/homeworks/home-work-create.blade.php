<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ isset($homework) ? 'Edit Homework' : 'Upload New Homework' }}</h4>
                </div>
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Whoops!</strong> There were
                            some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form
                        {{-- action="{{  ? route('homeworks.update', $homework->id) : }}" --}}

                        @if(isset($homework))
                        @else
                             wire:submit.prevent='store'
                        @endif
                        method="POST" enctype="multipart/form-data">

                        @csrf
        

                        <!-- Homework Title -->
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" wire:model="title" id="title" class="form-control"
                                placeholder="Enter Homework Title"
                                value="{{ old('title', $homework->title ?? '') }}" required>
                        </div>

                        <!-- Select Subject -->
                        @if ($subjects->count() > 1)

                            <div class="form-group">
                                <label for="subject_id">Subject</label>
                                <select wire:model="subject_id" id="subject_id" class="form-control" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ old('subject_id', $homework->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        @else
                            <input type="text" wire:model="subject_id" hidden
                                value="{{ $subjects->first()->id }}" />
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


                        <!-- Upload Homework File -->
                        <div class="form-group">
                            <label for="file">Homework File</label>
                            <input type="file" wire:model="file" id="file" class="form-control-file" 
                                accept=".pdf,.doc,.docx" {{ isset($homework) ? '' : 'required' }}>
                                <p class="mb-2 text-muted">
                                    Please upload a valid file in pdf, doc, or docx format. The maximum file size allowed is 5MB.
                                </p>
                            @if (isset($homework->file_path))
                                <p class="mt-2">Current File:
                                    <a href="{{ asset('storage/' . $homework->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i> View
                                    </a>
                                </p>
                            @endif
                        </div>
                        

                        <!-- Due Date -->
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="datetime-local" wire:model="due_date" id="due_date" class="form-control"
                                value="{{ old('due_date', isset($homework) ? $homework->formatted_due_date : '') }}"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary " wire:loading.attr="disabled">
                            <span wire:loading.remove>
                             {{ isset($homework) ? 'Update Homework' : 'Create Homework' }}
                            </span>
                            <span wire:loading>
                                <i class="fa fa-spinner fa-spin"></i> Loading...
                            </span>
                        </button>
                        
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
