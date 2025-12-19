<div>
    <div class="card">
        <div class="card-header">
            <h4>{{ $subject ? 'Edit Subject' : 'Add New Subject' }}</h4>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit.prevent="{{ $subject ? 'update' : 'store' }}">
                <div class="form-group">
                    <label for="name">Subject Name</label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        wire:model="name" 
                        id="name" 
                        placeholder="Enter subject name" 
                        @if($subject && $subject->created_by_system) readonly @endif 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(!$subject || ($subject && !$subject->created_by_system))
                    <button type="submit" class="btn btn-primary" wire:loading.attr="hidden">
                        {{ $subject ? 'Update Subject' : 'Add New Subject' }}
                    </button>
                    <span wire:loading>
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                @endif

                <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
