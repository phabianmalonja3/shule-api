<div class="card">
    <div class="card-header">
        <h4>Resources for {{ $subject->name }}</h4>
        <div class="card-header-form d-flex justify-content-between align-items-center">
            <!-- Search form -->
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by title" wire:model.live="search">
                <div class="input-group-btn">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <!-- Add New Resource Button -->
            <div class="mx-2 input-group">
            <a href="{{ route('subjects.resources.create', ['subject' => $subject->id]) }}" class="btn btn-success">
                Add New Resource
            </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if ($resources->isEmpty())
            <p>No resources found for this subject.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>{{ \Str::title('Type') }}</th>
                        <th>Upload Date</th>
                        <th>Download / View</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resources as $resource)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $resource->title }}</td>
                            <td>{{ \Str::title($resource->resource_type) }}</td>
                            <td>{{ $resource->created_at->format('d M Y') }}</td>
                            <td>
                                @if ($resource->resource_type === 'link')
                                    <a href="{{ $resource->url }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-external-link-alt"></i> Visit 
                                    </a>
                                @else
                                    @if ($resource->resource_type === 'image')
                                        <img src="{{ asset('storage/' . $resource->file_path) }}" alt="{{ $resource->title }}" width="50"> 
                                    @elseif ($resource->resource_type === 'notes' || $resource->resource_type === 'past_paper')
                                        @if(Str::endsWith($resource->file_path, '.pdf'))
                                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="btn btn-info btn-sm">
                                                <i class="fas fa-file-pdf"></i> PDF Viewer
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/' . $resource->file_path) }}" download class="btn btn-success btn-sm">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        @endif
                                    @elseif ($resource->resource_type === 'video')
                                        <video width="200" height="100" controls>
                                            <source src="{{ asset('storage/' . $resource->file_path) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @elseif ($resource->resource_type === 'audio') 
                                        <audio controls>
                                            <source src="{{ asset('storage/' . $resource->file_path) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @endif
                                @endif
                            </td>
                            <td> 
                                <a href="{{ route('subjects.resources.edit', [$subject->id, $resource->id]) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('subjects.resources.destroy', [$subject->id, $resource->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div x-intersect.full="$wire.loadMore()" class="p-2 text-center">
                <div wire:loading wire:target="loadMore" class="text-center">
                    <div class="lds">
                        <div></div><div></div><div></div><div></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
