<div class="mt-4 col-md-6">
    <h4>Latest Resources</h4>
    @if ($resources->isEmpty())
        <p>No resources available.</p>
    @else
    <ul class="list-group">
        @foreach ($resources as $resource)
            @php
                // Get the file extension if file_path exists
                $fileExtension = $resource->file_path ? pathinfo($resource->file_path, PATHINFO_EXTENSION) : null;
    
                // Define icons based on file types
                $icons = [
                    'pdf' => 'bi-file-earmark-pdf text-danger',
                    'doc' => 'bi-file-earmark-word text-primary',
                    'docx' => 'bi-file-earmark-word text-primary',
                    'xls' => 'bi-file-earmark-excel text-success',
                    'xlsx' => 'bi-file-earmark-excel text-success',
                    'ppt' => 'bi-file-earmark-ppt text-warning',
                    'pptx' => 'bi-file-earmark-ppt text-warning',
                    'jpg' => 'bi-file-earmark-image text-info',
                    'jpeg' => 'bi-file-earmark-image text-info',
                    'png' => 'bi-file-earmark-image text-info',
                    'mp4' => 'bi-file-play text-dark',
                    'mp3' => 'bi-file-music text-dark',
                    'txt' => 'bi-file-earmark-text text-secondary',
                    'zip' => 'bi-file-zip text-muted'
                ];
    
                // Default icon
                $iconClass = $icons[$fileExtension] ?? 'bi-file-earmark text-secondary';
            @endphp
    
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi {{ $iconClass }} fs-4"></i>
                    <strong>{{ $resource->title }}</strong>
                    <p>{{ $resource->description }}</p>
                    <small>Type: {{ ucfirst($resource->resource_type) }}</small>
                    <small>Subject: {{ $resource->subject->name }}</small>
                </div>
    
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
                              
            </li>
        @endforeach
    </ul>
    <button class="my-2 btn btn-primary" wire:click='loadMore()'>See More</button>
            <div wire:loading wire:target="loadMore" class="text-center">
                <div class="lds">
                    <div></div><div></div><div></div><div></div>
                </div>
            </div>
    @endif
  </div>
