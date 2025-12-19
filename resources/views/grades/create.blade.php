<x-layout>
    <x-slot:title>
        {{ isset($grade) ? 'Edit Grade' : 'Create Grade' }}
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content">
        <div class="container">
            <div class="mt-4 card">
                <div class="text-white card-header ">
                    <h4>{{ isset($grade) ? 'Edit Grade' : 'Create a New Grade' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($grade) ? route('grades.update', $grade->id) : route('grades.store') }}" method="POST">
                        @csrf
                        @if (isset($grade))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="school_type" value="{{ $grade->school_type }}">
                        <div class="form-group">
                            <label for="grade">Grade <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="grade" 
                                   readonly
                                   id="grade" 
                                   class="form-control @error('grade') is-invalid @enderror" 
                                   value="{{ old('grade', $grade->grade ?? '') }}" 
                                   required 
                                  >
                            @error('grade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="min_marks">Minimum Marks <span class="text-danger">*</span></label>
                            <input type="number" 
                                   step="0.01" 
                                   name="min_marks" 
                                   id="min_marks" 
                                   class="form-control @error('min_marks') is-invalid @enderror" 
                                   value="{{ old('min_marks', isset($confirmedGrade)? $confirmedGrade->min_marks : $grade->min_marks ?? '') }}" 
                                   required 
                                   placeholder="Enter minimum marks">
                            @error('min_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="max_marks">Maximum Marks <span class="text-danger">*</span></label>
                            <input type="number" 
                                   step="0.01" 
                                   name="max_marks" 
                                   id="max_marks" 
                                   class="form-control @error('max_marks') is-invalid @enderror" 
                                   value="{{ old('max_marks', isset($confirmedGrade)? $confirmedGrade->max_marks : $grade->max_marks ?? '') }}" 
                                   required 
                                   placeholder="Enter maximum marks">
                            @error('max_marks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" 
                                      id="remarks" 
                                      rows="3" 
                                      class="form-control @error('remarks') is-invalid @enderror" 
                                      placeholder="Add any remarks or notes (Optional)">{{ old('remarks', isset($confirmedGrade)? $confirmedGrade->remarks : $grade->remarks ?? '') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-right form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($grade) ? 'Update Grade' : 'Save Grade' }}
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
