<!-- resources/views/teacher/assignment/edit.blade.php -->
<x-layout>
    <x-slot:title>
        Stream Assignment Edit
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Edit Stream  Assignment</h4>
                            </div>
                            <div class="card-body">
                                <!-- Success message -->
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <!-- Edit Form -->
                                <form action="{{ route('streams.update',$stream->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Stream Selection -->
                                    <div class="form-group">
                                        <label for="stream_name">Stream</label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            class="form-control" 
                                            value="{{ $stream->name }}" 
                                            
                                        >
                                       
                                    </div>

                                    <!-- Subject Selection -->
                                    
                                  

                                    <!-- Teacher Selection -->
                                    <div class="form-group">
                                        <label for="teacher_id">Teacher</label>
                                        <select name="teacher_id" class="form-control" required>
                                            <option value="">Select Teacher</option>
                                                {{ $teachers }}
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" 
                                                        {{ $teacher->id == $stream->teacher_id ? 'selected' : '' }}>
                                                        {{ $teacher->name }}
                                                @endforeach
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @section('scripts')
    <script>
        // Toggle the streams checkbox section based on the selection
        document.getElementById('all_streams').addEventListener('change', function() {
            const specificStreamsDiv = document.getElementById('specific-streams');
            if (this.value === 'no') {
                specificStreamsDiv.classList.remove('d-none');
            } else {
                specificStreamsDiv.classList.add('d-none');
            }
        });
    </script>
    @endsection
</x-layout>
