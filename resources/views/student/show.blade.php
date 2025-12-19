<x-layout>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <section class="">
            <div class="section-body">
                <div class="row">
                    <div class="container">
                        <div class="card">
                            <h4 class="mt-2 ml-4">Student Details</h4>
                            <div class="text-center card-header">
                                <img
                                    src="{{ $student->profile_picture ? asset('storage/' . $student->profile_picture) : 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="30" r="20" fill="#ccc"/><rect x="30" y="55" width="40" height="30" rx="10" fill="#ccc"/></svg>') }}"
                                    alt="Profile Picture"
                                    class="mx-auto mb-3 border rounded-circle"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Student Information</h6>
                                        <p><strong>Name:</strong> {{ $student->user->name }}</p>
                                        <p><strong>Gender:</strong> {{ ucfirst($student->user->gender) }}</p>
                                        <p><strong>Registration Number:</strong> {{ $student->reg_number }}</p>
                                        <p><strong>Class:</strong> {{ $student->schoolClass->name }}</p>
                                        <p><strong>Stream:</strong> {{ $student->stream->name }}</p>
                                        <p><strong>Enrollment Date:</strong> {{ $student->created_at->format('d M, Y') }}</p>
                                    </div>

                                    <div class="col-md-6">                                        
                                        @if ($student->parents->isNotEmpty())
                                            <h6>Parent/Guardian Information</h6>
                                            @foreach ($student->parents as $parent)
                                                <div class="mb-2 border p-3 rounded">
                                                    <h6>{{ ucfirst($parent->relationship) }}</h6>
                                                    <p><strong>Name:</strong> {{ $parent->first_name.' '.$parent->middle_name.''.$parent->sur_name }}</p>
                                                    <p><strong>Gender:</strong> {{ ucfirst($parent->user->gender) }}</p>
                                                    <p><strong>Email:</strong> {{ $parent->user->email }}</p>
                                                    <p><strong>Phone:</strong> {{ $parent->phone }}</p>
                                                    </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    @if(auth()->user()->hasAnyRole(['academic teacher','header teacher','class teacher']))
                                        <a href="{{ route('students.edit', $student->id) }}" class="mx-2 btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('students.index') }}" class="mx-2 btn btn-primary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>