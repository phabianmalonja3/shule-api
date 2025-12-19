<x-layout>
    <x-slot:title>
        Profile Page
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <!-- Profile Update Section -->
                <div class="mb-4 card">
                    <div class="card-header">
                        <h4>Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        <!-- Display Profile Picture -->
                        <div class="mb-4 text-center">
                            @if (auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture" class="img-thumbnail mt-4 shadow-sm rounded-circle" width="150">
                            @else
                                <img src="{{ asset('profile/png-clipart-profile-logo-computer-icons-user-user-blue-heroes-thumbnail.png') }}" alt="Default Avatar" class="img-thumbnail mt-4 shadow-sm rounded-circle" width="150">
                            @endif
                        </div>

                        <!-- Form to Update Profile -->
                        <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @php

                                $nameParts = explode(' ', auth()->user()->name);
                                $firstName = $nameParts[0] ?? '';
                                $middleName = $nameParts[1] ?? '';
                                $surname = $nameParts[2] ?? '';
                                $fullName = ucfirst($firstName) . ' ' . ucfirst($middleName) . ' ' . ucfirst($surname);

                            @endphp
                            <div class="form-group row"> 
                                <div class="col-md-6">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $fullName) }}" class="form-control"  readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="name">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control"  readonly>
                                </div> 
                                <div class="col-md-6">
                                    <label for="name">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value=""  disabled selected >Select Gender</option>
                                        <option value="male" @if(auth()->user()->gender == 'male') selected @endif>Male</option>
                                        <option value="female" @if(auth()->user()->gender == 'female') selected @endif>Female</option>
                                    </select>
                                </div>                           
                                <div class="col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" >
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>

                    </div>
                </div>

                @if (auth()->user()->roles->first()->name !='header teacher')
                    <div class="card">
                        <div class="card-body">
                            <div class="mt-2">
                                <form action="{{ route('profile.phone.update') }}" method="POST" >
                                    @csrf
                                    @method('PUT')
                
                
                                    <div class="form-group">
                                        <label for="name">Current Phone</label>
                                        <input type="tel" name="curret_phone" value="{{ old('curret_phone') }}" class="form-control"  >
                                    </div>
                                    <div class="form-group">
                                        <label for="name">New Number</label>
                                        <input type="tel" name="new_phone" value="{{ old('new_phone') }}" class="form-control"  >
                                    </div>
                                
                                    <button type="submit" class="btn btn-primary">Update phone</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Password Change Section -->
                <div class="card">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.changePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="new_password">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                                @error('new_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirmation">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                                @error('new_password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
