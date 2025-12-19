<x-layout>
    <x-slot:title>
        {{ isset($class) ? 'Edit Class' : 'Add New Class' }}
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
                                <h4>{{ isset($class) ? 'Edit Class' : 'Add New Class' }}</h4>
                            </div>
                            <div class="card-body">
                                <!-- Form for Creating or Editing -->
                                <form
                                    action="{{ isset($class) ? route('classes.update', $class->id) : route('classes.store') }}"
                                    method="POST">
                                    @csrf
                                    @if(isset($class))
                                    @method('PUT')
                                    @endif

                                    <div class="form-group">
                                        <label for="name">Class Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            placeholder="Enter class name" value="{{ old('name', $class->name ?? '') }}"
                                            {{ isset($class) && $class->created_by_system ? 'readonly' : '' }} >
                                    </div>
                                    @error('name')
                                    <div class="text-danger">
                                        {{$message}}
                                    </div>
                                    @enderror

                                    <div class="form-group">
                                        <label for="teacher">Select Class Teacher(Optional)</label>
                                        <select name="teacher_class_id" id="teacher" class="form-control select2">
                                            <option value="" disabled selected>Select Teacher</option>
                                            @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_class_id', $class->teacher_class_id ?? '') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                            @endforeach
                                        </select>

                                        <input type="hidden" name="old_teacher_class_id" value="{{ old('teacher_class_id', $class->teacher_class_id ?? '') }}">
                                    </div>

                                    @error('teacher_class_id')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                    <button type="submit" class="btn btn-primary">{{ isset($class) ? 'Update Class' :
                                        'Create Class' }}</button>
                                    <a href="{{ route('classes.index') }}" class="btn btn-secondary">Cancel</a>
                                </form>

                                <!-- Form for Deleting -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Select2 JS and CSS -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const teacherSelect = document.getElementById('teacher');
        
            if (teacherSelect) {
                // Initialize Select2
                new Select2(teacherSelect, {
                    placeholder: 'Select a Teacher',
                    allowClear: true
                });
            }
        });
    </script>
</x-layout>