<x-layout>
    <x-slot:title>
        Edit Teacher
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="px-3 row">
                    <div class="px-3 col-12">
                        <div class="px-4 card">
                            <h4 class="py-2">Edit Teacher Information</h4>
                            <form action="{{ route('teachers.update', $teacher->id) }}?status={{ $teacher->is_verified }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Name Field -->
                                <div class="row">
                                    @php
                                        $fullname = explode(' ', $teacher->name);

                                        $first_name = $fullname[0]?? '';
                                        $middle_name = $fullname[1]?? '';
                                        $sur_name = $fullname[2]?? '';

                                    @endphp
                                    <!-- First Name -->
                                    <div class="mb-4 col-md-4">
                                        <label for="first_name">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            value="{{ old('first_name', $first_name) }}">
                                        @error('first_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <!-- Middle Name -->
                                    <div class="mb-4 col-md-4">
                                        <label for="middle_name">Middle Name (Optional)</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                                            value="{{ old('middle_name', $middle_name) }}">
                                        @error('middle_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-4 col-md-4">
                                        <label for="sur_name">Surname</label>
                                        <input type="text" class="form-control" id="sur_name" name="sur_name"
                                            value="{{ old('sur_name', $sur_name) }}">
                                        @error('sur_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-4 col-md-4">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', $teacher->phone) }}">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-4 col-md-4">
                                        <label for="email">Email (Optional)</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $teacher->email) }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-4 col-md-4">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" id="gender" name="gender">
                                            <option value="">Select gender</option>
                                            <option value="male"
                                                {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female"
                                                {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>
                                                Female</option>
                                        </select>
                                        @error('gender')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                {{-- @role('academic teacher') --}}
                                @php
                                    Str::contains(strtolower(auth()->user()->school->name),'secondary')? $role = "Assistant Head of School" : $role = "Assistant Headteacher";
                                @endphp
                                @if($teacher->roles->first()->name !="header teacher")
                                <div class="row">
                                    <!-- Role -->
                                    <div class="mb-4 col-md-12">
                                        <label for="role">Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="" disabled >Select Role</option>
                                            <option value="teacher"
                                                {{ old('role', $teacher->roles->first()->name ?? '') == 'teacher' ? 'selected' : '' }}>
                                                Teacher</option>

                                            <option value="academic teacher" {{ old('role', $teacher->roles->first()->name ?? '') == 'academic teacher' ? 'selected' : '' }}
                                                @if($academicTeacherCount >= 2) disabled @endif>Academic Teacher</option>

                                            <option value="assistant headteacher" {{ old('role', $teacher->roles->first()->name ?? '') == 'assistant headteacher' ? 'selected' : '' }}
                                                @if($HeaderTeacherAssistCount >= 2) disabled @endif> {{ $role }}</option>
                                        </select>
                                        @error('role')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                                    
                                {{-- @endrole --}}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                         <i class="fas fa-save"></i> Save</button>
                                     <a href="{{ route('teachers.index') }}" class="btn btn-primary">
                                         <i class="fas fa-times"></i> Cancel </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
