<x-layout>
    <x-slot:title>
        {{ isset($resource) ? 'Edit Leanring Materials - ' . $resource->title : 'Create Leanring Materials' }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        {{-- <div class="container"> --}}
            <div class="card">
                <div class="card-header">
                    <h4>{{ isset($resource) ? 'Edit Leanring Materials' : 'Create Leanring Materials' }} for {{$subject->name}}</h4>
                </div>
                @if ($errors->any())
    <div class="alert alert-danger" id="error-container">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                <div class="card-body">
                    <form action="{{ isset($resource) ? route('subjects.resources.update', [$subject->id, $resource->id]) : route('subjects.resources.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($resource))
                            @method('PUT')
                        @endif
                
                        <div class="form-group">
                            <label for="title">Resource Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $resource->title ?? '') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="resource_type">Resource Type</label>
                            <select class="form-control @error('resource_type') is-invalid @enderror" id="resource_type" name="resource_type" required>
                                <option value="notes" {{ old('resource_type', $resource->resource_type ?? '') == 'notes' ? 'selected' : '' }}>Notes</option>
                                <option value="past_paper" {{ old('resource_type', $resource->resource_type ?? '') == 'past_paper' ? 'selected' : '' }}>Past Paper</option>
                                <option value="video" {{ old('resource_type', $resource->resource_type ?? '') == 'video' ? 'selected' : '' }}>Video</option>
                                <option value="audio" {{ old('resource_type', $resource->resource_type ?? '') == 'audio' ? 'selected' : '' }}>Audio</option>
                                <option value="link" {{ old('resource_type', $resource->resource_type ?? '') == 'link' ? 'selected' : '' }}>Link</option>
                            </select>
                            @error('resource_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="form-group" id="file_upload_div">
                            <label for="file">Upload File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept="application/pdf, .doc, .docx, .jpg, .jpeg, .png, .mp4, .mp3" 
                                   @if(old('resource_type', $resource->resource_type ?? '') != 'link')  @endif>
                           
                           
                                   <small class="form-text text-muted" id="file-info">
                                
                            </small>
                            @if ($resource->file_path ?? '' )
                            <p>Current File: <a href="{{  asset('storage/' . $resource->file_path)  }}" target="_blank">View</a></p>
                        @endif
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="form-group" id="link_input_div" style="display: {{ old('resource_type', $resource->resource_type ?? '') == 'link' ? 'block' : 'none' }}">
                            <label for="url">Link</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $resource->url ?? '') }}" 
                                   @if(old('resource_type', $resource->resource_type ?? '') == 'link')  @endif>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter resource  Description">{{ old('description', $resource->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                
                        <button type="submit" class="btn btn-primary">
                            {{ isset($resource) ? 'Update Resource' : 'Create Resource' }}
                        </button>
                    </form>
                </div>
                
            </div>
        {{-- </div> --}}
    </div>
<script src="{{asset('js/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#resource_type').change(function() {
            var resourceType = $(this).val();
            var fileUploadDiv = $('#file_upload_div');
            var linkInputDiv = $('#link_input_div');
            var fileInput = $('#file');
            var urlInput = $('#url');
            var fileInfo = $('#file-info'); // Target the <small> tag

            // Define acceptable file types for each resource type
            var fileTypes = {
                "notes": { accept: "application/pdf,.doc,.docx", text: "Accepted formats: .pdf, .doc, .docx (Max: 10MB)" },
                "past_paper": { accept: "application/pdf,.doc,.docx", text: "Accepted formats: .pdf, .doc, .docx (Max: 10MB)" },
                "video": { accept: "video/mp4", text: "Accepted formats: .mp4 (Max: 100MB)" },
                "audio": { accept: "audio/mpeg", text: "Accepted formats: .mp3 (Max: 10MB)" },
                "link": { accept: "", text: "" }
            };

            if (resourceType === 'link') {
                fileUploadDiv.hide();
                fileInput.removeAttr('required');
                linkInputDiv.show();
                urlInput.attr('required', 'required');
            } else {
                fileUploadDiv.show();
                fileInput.attr('required', 'required');
                fileInput.attr('accept', fileTypes[resourceType].accept || ""); // Update accept attribute
                fileInfo.text(fileTypes[resourceType].text); // Update file info text
                linkInputDiv.hide();
                urlInput.removeAttr('required');
            }
        });

        // Trigger change event on page load to apply logic to pre-selected value
        $('#resource_type').trigger('change'); 
    });
</script>

</x-layout>