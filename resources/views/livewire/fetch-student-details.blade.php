<div>
    <!-- Input Field for Registration Number -->
    <div class="form-group">
        <label for="registrationNumber">Enter Registration Number</label>
        <input type="text" id="registrationNumber" class="form-control" wire:model.defer="registrationNumber" placeholder="e.g., 0005/898/2345">
        @error('registrationNumber')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Fetch Student Button -->
    <div class="form-group">
        <button type="button" class="btn btn-primary" wire:click="fetchStudent" wire:loading.attr="disabled">

            @if(count($students) >0 )
            
                Add student
                @else
                Fetch Student Details
            @endif
        </button>
        <div wire:loading wire:target="fetchStudent">
            <i class="fa fa-circle-notch fa-spin"></i> Loading...
        </div>
    </div>

    <!-- Error Message -->
    @if ($errorMessage)
        <div class="alert alert-danger">
            {{ $errorMessage }}
        </div>
    @endif

    <!-- Add Student Button -->


    <!-- Dynamic Form Repeater -->
    <form wire:submit.prevent="subscribe">
        @csrf

        <!-- List of Students -->
        <div class="row">
            @foreach ($students as $index => $student)
                <div class="mt-3 col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Inputs in a single row -->
                            <div class="row align-items-center">
                                <!-- School Name -->
                                <div class="col-md-5">
                                    <label>School Name</label>
                                    <input type="text" class="form-control" value="{{ $student['school'] }}" readonly>
                                </div>
                                
                                <!-- Student Name -->
                                <div class="col-md-5">
                                    <label>Student Name</label>
                                    <input type="text" class="form-control" value="{{ $student['name'] }}" readonly>
                                </div>
                                
                                <!-- Delete Button -->
                                <div class="text-center col-md-2">
                                    <button type="button" class="mt-4 btn btn-danger btn-sm" wire:click="removeStudent({{ $index }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Add Phone Number -->
        @if (count($students) > 0)
            <div class="mt-4 form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" class="form-control" wire:model.defer="phone" placeholder="e.g., 07XXXXXXX">
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Subscription Summary -->
            <div class="mt-4 card">
                <div class="card-body">
                    <h5>Subscription Summary</h5>
                    <p>Number of Students: <strong>{{ count($students) }}</strong></p>
                    <p>Subscription Fee per Student: <strong>10,000 TZS</strong></p>
                    <p>Total Subscription Fee: <strong>{{ number_format(10000 * count($students)) }} TZS</strong></p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="mt-3 btn btn-success btn-block" wire:loading.attr="disabled">
                <span wire:loading.remove>Proceed to Pay</span>
                <span wire:loading>
                    <i class="fa fa-circle-notch fa-spin"></i> Processing Payment...
                </span>
            </button>
        @endif
    </form>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mt-3 alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-3 alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
</div>
