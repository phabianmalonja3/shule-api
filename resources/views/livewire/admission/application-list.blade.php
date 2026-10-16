<div> <!-- Root component container (MUST wrap everything) -->

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body">
            <!-- Search, Filters, and Table content... -->
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $applications->links() }}
    </div>

    <!-- Modal Container (Now safely inside the root <div>) -->
    @if($isOpen)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
                    
                    <!-- Modal Header -->
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title font-weight-bold text-dark">Add New Application</h5>
                        <button type="button" class="close" wire:click="$set('isOpen', false)" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body / Form -->
                    <div class="modal-body">
                        <form wire:submit.prevent="storeApplication">
                            
                            <!-- First Name -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">First Name</label>
                                <input type="text" wire:model="first_name" class="form-control" style="border-radius: 8px;" placeholder="Enter first name">
                                @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Middle Name -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">Middle Name</label>
                                <input type="text" wire:model="middlename" class="form-control" style="border-radius: 8px;" placeholder="Enter middle name">
                                @error('middlename') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Surname -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">Surname</label>
                                <input type="text" wire:model="surname" class="form-control" style="border-radius: 8px;" placeholder="Enter surname">
                                @error('surname') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Exam Center Dropdown -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">Exam Center</label>
                                <select wire:model="center_id" class="form-control" style="border-radius: 8px;">
                                    <option value="">Select Exam Center</option>
                                    @foreach($examCenters as $center)
                                        <option value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                                @error('center_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Exam Date -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">Exam Date</label>
                                <input type="date" wire:model="exam_date" class="form-control" style="border-radius: 8px;">
                                @error('exam_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Exam Language -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted font-13">Exam Language</label>
                                <input type="text" wire:model="exam_language" class="form-control" style="border-radius: 8px;" placeholder="e.g. English">
                                @error('exam_language') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Form Status -->
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted font-13">Status</label>
                                <select wire:model="form_status" class="form-control" style="border-radius: 8px;">
                                    <option value="in progress">In Progress</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="completed">Completed</option>
                                </select>
                                @error('form_status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Modal Footer Buttons -->
                            <div class="d-flex justify-content-end" style="gap: 10px;">
                                <button type="button" class="btn btn-light px-4" wire:click="$set('isOpen', false)" style="border-radius: 8px;">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px;">
                                    Save Application
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div> <!-- End of root component container -->