<div>
    <div class="row">
        <div class="col-12">
            <div class="px-4 card">
                <h2 class="pt-3">{{ isset($announcement) ? 'Edit Announcement' : 'Add New Announcement' }}</h2>
                <div class="card-header">
                </div>
    
                <form 
    wire:submit.prevent="{{ isset($announcement) ? " editAnnouncement($announcement->id)" :'saveAnnouncement' }}" 
    enctype="multipart/form-data" 
    class="py-1"
>
    <!-- Loading Spinner -->
    <div wire:loading wire:target="saveAnnouncement" class="mb-3 text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Submitting...</span>
        </div>
        <p>Submitting, please wait...</p>
    </div>

    <!-- Title -->
    <div class="mb-4 form-group">
        <label for="title">Title</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror"
            id="title" wire:model="title" required>
        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <!-- Content -->
    <div class="mb-4 form-group">
        <label for="content">Content</label>
        <textarea class="form-control @error('content') is-invalid @enderror" 
                  id="content" wire:model="content" rows="4" required></textarea>
        @error('content') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <!-- Start Date -->
    <div class="mb-4 form-group">
        <label for="start_date">Start Date</label>
        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
            id="start_date"  wire:model="start_date" required>
        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <!-- End Date -->
    <div class="mb-4 form-group">
        <label for="end_date">End Date</label>
        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
            id="end_date" wire:model="end_date" required>
        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <!-- Image -->
    <div class="mb-4 form-group">
        <label for="image">Image (Optional)</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror"
            id="image" wire:model="image" accept="image/*">
        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
        <small class="form-text text-muted">
            Accepted formats: JPEG, PNG, JPG. Maximum file size: 2MB.
        </small>

        <div wire:loading wire:target="image" class="mt-2 text-center">
            <div class="spinner-border text-primary spinner-border-lg" role="status">
                <span class="visually-hidden">Loading preview...</span>
            </div>
            <p class="mt-2">Loading preview...</p>
        </div>
        
        @if ($image)
       
            <div wire:loading.remove wire:target="image" class="mt-2">
                <img src="{{ $image->temporaryUrl() }}" alt="Preview" width="100">
            </div>
       
        @elseif(isset($announcement) && $announcement->image)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $announcement->image) }}" alt="Current Image" width="100">
            </div>
        @endif
    </div>


    <!-- Announcement Type -->
    @if (auth()->user()->hasRole('header teacher') || auth()->user()->hasRole('academic teacher'))
    <!-- Show select box for header teacher & academic teacher -->
    <div class="mb-4 form-group">
        <label for="type">Announcement Type</label>
        <select class="form-control @error('type') is-invalid @enderror" 
                id="type" wire:model="type" required>
            <option value="">-- Select Type --</option>
            <option value="internal">Internal (Teachers Only)</option>
            <option value="external">External (Parents/Guardians/Sponsors)</option>
            <option value="both">Both (Teachers & Parents/Guardians/Sponsors)</option>
        </select>
        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
@elseif (auth()->user()->hasRole('class teacher'))
    <!-- Hidden input for class teachers, forcing "internal" -->
    <input type="hidden" wire:model="type" value="external">
@endif


    <!-- Submit Button -->
    <div class="mb-4 form-group">
        <button type="submit" class="btn btn-primary position-relative" wire:loading.attr="disabled">
            <span wire:loading.remove>
                {{ isset($announcement) ? 'Update Announcement' : 'Add Announcement' }}
            </span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Submitting...
            </span>
        </button>
    </div>
    
</form>

            </div>
        </div>
    </div>
</div>