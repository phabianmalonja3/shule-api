<x-layout>
    <x-slot:title>
        Class Teacher Panel - Student Management
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content">
        <section class="section">
            <div class="container mt-4">

                <!-- Success and Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Manual Registration Form -->
                <h4>Manual Student Registration</h4>
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" name="fullname" id="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="school_class_id">School Class</label>
                        <select name="school_class_id" id="school_class_id" class="form-control" required>
                            {{-- @foreach($schoolClasses as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stream_id">Stream</label>
                        <select name="stream_id" id="stream_id" class="form-control" required>
                            {{-- @foreach($streams as $stream)
                                <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register Student</button>
                </form>

                <hr>

                <!-- File Import Form -->
                <h4>Import Students via Excel</h4>
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="student_file">Upload Excel File</label>
                        <input type="file" name="student_file" id="student_file" class="form-control" accept=".xls,.xlsx" required>
                    </div>
                    <button type="submit" class="btn btn-success">Upload and Import</button>
                </form>

            </div>
        </section>
    </div>
</x-layout>
