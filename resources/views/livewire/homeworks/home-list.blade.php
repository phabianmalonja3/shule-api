<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ \Str::title('List of Homeworks') }}</h4>
                    <a href="{{ route('homeworks.create') }}" class="btn btn-primary">Create New Homework</a>
                </div>
                <div class="card-body">
                    <div class="mb-3 row">
                        <!-- Filter by Stream -->
                        @if ($streams->count() > 1)
                            <div class="col-md-4">
                                <label for="selectedStream">Filter by Stream:</label>
                                <select wire:model.live="selectedStream" id="selectedStream" class="form-control">
                                    <option value="">All Streams</option>
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->id }}"> Class  {{ $stream->schoolClass->name }} {{ $stream->alias }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Filter by Subject -->
                        @if ($subjects->count() > 1)
                            <div class="col-md-4">
                                <label for="selectedSubject">Filter by Subject:</label>
                                <select wire:model.live="selectedSubject" id="selectedSubject" class="form-control">
                                    <option value="">All Subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" 
                                            @if ($loop->last && !$selectedSubject) selected @endif>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Search -->
                        <div class="col-md-4">
                            <label for="searchTerm">Search:</label>
                            <input wire:model.debounce.500ms.live="searchTerm" type="text" id="searchTerm" class="form-control" placeholder="Search by title or description">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Uploaded PDF</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($homeworks as $homework)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $homework->title }}</td>
                                        <td>{{ $homework->subject->name }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $homework->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-pdf"></i> View PDF
                                            </a>
                                            <a href="{{ asset('storage/' . $homework->file_path) }}" download class="ml-2 btn btn-sm btn-success">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </td>
                                        <td>{{ $homework->due_date }}</td>
                                        <td>
                                            <a href="{{ route('homeworks.show', $homework->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                            <a href="{{ route('homeworks.edit', $homework->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('homeworks.destroy', $homework->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this homework?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No homework uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $homeworks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
