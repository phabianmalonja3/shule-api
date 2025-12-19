<x-layout>
    <x-slot:title>
        {{ isset($student) ? 'Edit Student & Guardian Details' : 'Add Students' }}
    </x-slot:title>

    <!-- Navbar and Sidebar Components -->
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="px-3 row">
                    <div class="px-3 col-12">
                        <div class="px-4 card">
                            <h6 class="py-3">Edit Student Details</h6>
                            <form action="{{ route('students.update', ['student' => $student->id, 'school_class_id' => $student->school_class_id]) }}" 
                                    method="POST">
                                @csrf
                            
                                @method('PUT')
                        
                                @php
                                    // Exploding the full name into first name, middle name, and surname
                                    $nameParts = explode(' ', old('full_name', $student->user->name ?? ''));
                                    $first_name = $nameParts[0] ?? '';
                                    $middle_name = $nameParts[1] ?? '';
                                    $surname = $nameParts[2] ?? '';
                                @endphp

                                <div class="row">
                                    <!-- First Name -->
                                    <div class="mb-4 col-md-4">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" id="first_name" 
                                            class="form-control @error('first_name') is-invalid @enderror" 
                                            placeholder="Enter first name" 
                                            value="{!! old('first_name', $first_name ?? '') !!}" 
                                            required>
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <!-- Middle Name -->
                                    <div class="mb-4 col-md-4">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                            class="form-control @error('middle_name') is-invalid @enderror" 
                                            placeholder="Enter middle name" 
                                            value="{{ old('middle_name', $middle_name ?? '') }}">
                                        @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <!-- Surname -->
                                    <div class="mb-4 col-md-4">
                                        <label for="surname">Surname</label>
                                        <input type="text" name="surname" id="surname" 
                                            class="form-control @error('surname') is-invalid @enderror" 
                                            placeholder="Enter surname" 
                                            value="{{ old('surname', $surname ?? '') }}" 
                                            required>
                                        @error('surname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>                                    
                                </div>
                
                                <div class="row">                                   
                                    <!-- Gender -->
                                    <div class="mb-4 col-md-4">
                                        <label for="gender">Select Gender</label>
                                        <select name="gender" id="gender" 
                                                class="form-control @error('gender') is-invalid @enderror" 
                                                required>
                                            <option value="">-- Select gender --</option>
                                            <option value="male" {{ old('gender', $student->user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $student->user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <div class="mb-4 col-md-4">
                                        <label for="combination">Select Combination</label>
                                        <select name="combination" id="combination" 
                                                class="form-control @error('combination') is-invalid @enderror" 
                                                required>
                                            <option value="">-- Select Combination --</option>
                                            @foreach($combinations as $key => $combination)
                                                @if($combination->id > 1) 
                                                    <option value="{{ $combination->id }}" 
                                                            {{ old('combination', $student->combinations->first()->id ?? '') == $combination->id ? 'selected' : '' }}> 
                                                        {{ $combination->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('combination')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Stream -->
                                    @if ($streams->count() > 1)
                                        <div class="mb-4 col-md-4">
                                            <label for="stream_id">Select Stream</label>
                                            <select name="stream_id" id="stream_id" 
                                                    class="form-control @error('stream_id') is-invalid @enderror" 
                                                    required>
                                                <option value="">-- Select Stream --</option>
                                                @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}" 
                                                        {{ old('stream_id', $student->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                        Class {{ $stream->schoolClass->name }} {{ $stream->alias }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('stream_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <input type="hidden" name="stream_id" value="{{ old('stream_id', $student->stream_id ?? '') }}">
                                    @endif
                    
                                    <!-- Phone Number -->
                                    <div class="mb-4 col-md-4">
                                        <label for="phone">Parent Phone Number</label>
                                        <input type="text" name="phone" id="phone" 
                                            class="form-control @error('phone') is-invalid @enderror" 
                                            placeholder="Enter phone number" 
                                            value="{{ old('phone', $student->user->phone ?? '') }}"
                                            @if(count($student->parents) > 0) readonly @endif>
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if(count($student->parents) > 0)
                                    <hr>
                                    <h6 class="py-3">Edit Parent/Guardian Details</h6>

                                    @foreach($student->parents as $parent)
                                        <fieldset style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
                                            <legend style="width: auto; padding: 0 10px; margin-left: 10px; font-size: small">
                                                <strong>{{ ucfirst($parent->relationship ?? 'Parent/Guardian') }}</strong>
                                            </legend>

                                            <input type="hidden" name="parents[{{ $parent->id }}][id]" value="{{ $parent->id }}">
                                            <div class="row">
                                                {{-- Parent First Name --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="parent_first_name_{{ $parent->id }}">First Name</label>
                                                    <input type="text" 
                                                        name="parents[{{ $parent->id }}][first_name]" 
                                                        id="parent_first_name_{{ $parent->id }}" 
                                                        class="form-control @error('parents.'.$parent->id.'.first_name') is-invalid @enderror" 
                                                        placeholder="Enter parent first name" 
                                                        value="{{ old('parents.'.$parent->id.'.first_name', $parent->first_name ?? '') }}" 
                                                        required>
                                                    @error('parents.'.$parent->id.'.first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        
                                                {{-- Parent Middle Name --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="parent_middle_name_{{ $parent->id }}">Middle Name</label>
                                                    <input type="text" 
                                                        name="parents[{{ $parent->id }}][middle_name]" 
                                                        id="parent_middle_name_{{ $parent->id }}" 
                                                        class="form-control @error('parents.'.$parent->id.'.middle_name') is-invalid @enderror" 
                                                        placeholder="Enter parent middle name" 
                                                        value="{{ old('parents.'.$parent->id.'.middle_name', $parent->middle_name ?? '') }}">
                                                    @error('parents.'.$parent->id.'.middle_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        
                                                {{-- Parent Surname --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="parent_surname_{{ $parent->id }}">Surname</label>
                                                    <input type="text" 
                                                        name="parents[{{ $parent->id }}][surname]" 
                                                        id="parent_surname_{{ $parent->id }}" 
                                                        class="form-control @error('parents.'.$parent->id.'.surname') is-invalid @enderror" 
                                                        placeholder="Enter parent surname" 
                                                        value="{{ old('parents.'.$parent->id.'.surname', $parent->sur_name ?? '') }}" 
                                                        required>
                                                    @error('parents.'.$parent->id.'.surname')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div> 
                                            </div>

                                            <div class="row">
                                                {{-- Parent Gender --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="parent_gender_{{ $parent->id }}">Select Gender</label>
                                                    <select 
                                                        name="parents[{{ $parent->id }}][gender]" 
                                                        id="parent_gender_{{ $parent->id }}" 
                                                        class="form-control @error('parents.'.$parent->id.'.gender') is-invalid @enderror" 
                                                        required>
                                                        <option value="" disabled selected>-- Select gender --</option>
                                                        <option value="male" {{ old('parents.'.$parent->id.'.gender', $parent->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                        <option value="female" {{ old('parents.'.$parent->id.'.gender', $parent->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                    </select>
                                                    @error('parents.'.$parent->id.'.gender')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        
                                                {{-- Parent Relationship --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="relationship_{{ $parent->id }}">Relationship</label>
                                                    <select 
                                                        id="relationship_{{ $parent->id }}" 
                                                        name="parents[{{ $parent->id }}][relationship]" 
                                                        class="form-control @error('parents.'.$parent->id.'.relationship') is-invalid @enderror">
                                                        <option value="" disabled {{ old('parents.'.$parent->id.'.relationship') ? '' : 'selected' }}>-- Select Relationship --</option>
                                                        @php
                                                            $relationships = ['parent', 'aunt', 'uncle', 'sibling', 'grandmother', 'grandfather', 'sponsor', 'other'];
                                                        @endphp
                                                        @foreach($relationships as $rel)
                                                            <option value="{{ $rel }}" {{ old('parents.'.$parent->id.'.relationship', $parent->relationship ?? '') == $rel ? 'selected' : '' }}>{{ ucfirst($rel) }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('parents.'.$parent->id.'.relationship')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        
                                                {{-- Parent Phone --}}
                                                <div class="mb-4 col-md-4">
                                                    <label for="parent_phone_{{ $parent->id }}">Phone</label>
                                                    <input type="text" 
                                                        name="parents[{{ $parent->id }}][phone]" 
                                                        id="parent_phone_{{ $parent->id }}" 
                                                        class="form-control @error('parents.'.$parent->id.'.phone') is-invalid @enderror" 
                                                        placeholder="Enter parent phone" 
                                                        value="{{ old('parents.'.$parent->id.'.phone', $parent->phone ?? '') }}">
                                                    @error('parents.'.$parent->id.'.phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </fieldset>
                                    @endforeach
                                @endif

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-{{ isset($student) ? 'save' : 'plus' }}"></i> 
                                        {{ isset($student) ? 'Save' : 'Add Student' }}
                                    </button>
                                    <a href="{{ route('students.index') }}" class="btn btn-primary">
                                        <i class="fas fa-times"></i>  Cancel </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
