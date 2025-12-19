<div>
    {{-- Step 1: Student Registration Number --}}
    @if ($step === 1)
        <div class="card">
            <div class="card-header">
                <h4>Verify Student</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form wire:submit.prevent="fetchStudentDetails">
                    <div class="form-group">
                        <label for="student_registration_number">Student Registration Number</label>
                        <input type="text" id="student_registration_number" class="form-control"
                               wire:model="studentRegistrationNumber" placeholder="Enter registration number">
                        @error('studentRegistrationNumber')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" wire:loading.attr='disabled'>
                        <span wire:loading.remove='fetchStudentDetails'>
                            Fetch Student Details 
                        </span>
                        <span wire:loading >
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Step 2: Parent Registration Form --}}
    @if ($step === 2)
        <div class="row">

            {{-- Parent Registration Form --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Parent Registration</h4>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="registerParent">
                            @csrf

                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input id="first_name" type="text" class="form-control" wire:model="first_name"
                                       placeholder="Enter first name">
                                @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input id="middle_name" type="text" class="form-control" wire:model="middle_name"
                                       placeholder="Enter middle name">
                            </div>

                            <div class="form-group">
                                <label for="surname">Surname</label>
                                <input id="surname" type="text" class="form-control" wire:model="surname"
                                       placeholder="Enter surname">
                                @error('surname')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input id="phone" type="tel" class="form-control" wire:model="phone"
                                       placeholder="Enter phone number">
                                @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" type="password" class="form-control" wire:model="password"
                                       placeholder="Enter password">
                                @error('password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" type="password" class="form-control"
                                       wire:model="password_confirmation" placeholder="Confirm password">
                                @error('password_confirmation')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" class="form-control" wire:model="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="relationship">Relationship to Student</label>
                                <select id="relationship" class="form-control" wire:model="relationship">
                                    <option value="">Select Relationship</option>
                                    <option value="parent">Parent</option>
                                    <option value="aunt">Aunt</option>
                                    <option value="uncle">Uncle</option>
                                    <option value="sibling">Sibling</option>
                                    <option value="grandmother">Grandmother</option>
                                    <option value="grandfather">Grandfather</option>
                                    <option value="sponsor">Sponsor</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('relationship')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                    <span wire:loading wire:target="registerParent"
                                          class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span>
                                </button>
                                 <a href="{{ url('/') }}" class="btn btn-primary"> Cancel </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- Student Details --}}
            <div class="col-md-6">
                <div class="card text-center"> 
                    <div class="card-header">
                        <h4>Student Information</h4>
                    </div>
                    <div class="py-2">
                        <img src="{{ asset($studentDetails->user->profile ? 'storage/' . $studentDetails->user->profile : 'profile/png-clipart-profile-logo-computer-icons-user-user-blue-heroes-thumbnail.png') }}"
                             alt="Profile Picture" width="100" height="100" class="rounded-circle">
                    </div>
                    <div class="card-body">
                        <h7> {{ $studentDetails->user->name ?? '' }}</h7>
                        <p class="text-muted" style="font-size: 0.86em">{{ $studentDetails->schoolClass->name ?? '' }} <br> {{ $studentDetails->School->name ?? 'N/A'}}</p>
                        {{-- Add more student details here if needed --}}
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>