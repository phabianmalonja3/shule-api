<div>
    <div class="mb-3 row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ \Str::title('List of Assignments') }}</h4>
                    <a href="{{ route('assignments.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Assignment
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-md-4">
                            <select wire:model.debounce.500ms.live="selectedStream" class="form-control">
                                <option value="">All Streams</option>
                                @foreach($streams as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->schoolClass->name  }}{{ $stream->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select wire:model.debounce.500ms.live="selectedSubject" class="form-control">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-flex col-md-3">
                            <input type="text" wire:model.debounce.500ms.live="search" class="form-control" placeholder="Search Assignments">

                            <span wire:loading wire:target='search'>
                                <span id="loading-spinner" class="spinner-border spinner-border-sm" ></span>

                            </span>
                        </div>
                       
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Stream</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $assignment->title }}</td>
                                        <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
                                        <td>
                                            @foreach($assignment->streams as $index=>$stream)

                                           
                                                <span>{{ $stream->name }} @if ($index == count($assignment->streams) - 2) 
                                                    & 
                                                @elseif (!$loop->last) 
                                                    , 
                                                @endif</span>
                                            @endforeach
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}</td>
                                        <td>
                                            <a href="{{ route('assignments.show', $assignment->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-info-circle me-2"></i> View
                                            </a>
                                            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('assignments.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No assignments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
