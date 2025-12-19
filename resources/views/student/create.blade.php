<x-layout>
    <x-slot:title>
        Add Students
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <!-- Upload Excel File Form -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Adding Students in Bulky (Excel Upload)</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle"></i>
                                    <span style="font-size:.85em"> <strong>Important:</strong> Before uploading students' details, please download the template file below to ensure your Excel sheet matches the required format. </span>
                                </div>

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form action="{{ route('students.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group row"> {{-- Use row for horizontal layout --}}
                                        <div class="col-md-6"> {{-- Excel File input takes half the row --}}
                                            <label for="file">Excel File </label>
                                            <input type="file" name="file" id="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                                             <small class="form-text text-muted">
                                                Formats: .xlsx, .xls, .csv | Maximum size: 2MB
                                            </small>
                                        </div>

                                        @if ($streams->count() > 0)
                                        <div class="col-md-6"> {{-- Stream select takes the other half --}}
                                            <label for="stream_id">Stream</label>
                                            <select name="stream_id" id="stream_id" class="form-control @error('stream_id') is-invalid @enderror" required>
                                                <option value="" disabled selected>-- Select Stream --</option>
                                                @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                                                    {{ $stream->schoolClass->name }}  {{ $stream->alias }} 
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('stream_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @else
                                        <input type="hidden" name="stream_id" value="{{ $streams->first()->id ?? '' }}">
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        
                                        <a href="{{ route('students.download.sample') }}" class="btn btn-success" >
                                            <i class="fas fa-download"></i> Download Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Student Creation Form -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ isset($student) ? 'Edit Student' : 'Adding Students Manually' }}</h4>
                            </div>
                            <div class="card-body">
                               
                                <form action="{{ isset($student) 
                                                    ? route('students.update', ['student' => $student->id,]) 
                                                    : route('students.store') }}" 
                                      method="POST">
                                    @csrf
                                    @if(isset($student))
                                    @method('PATCH')
                                    @endif
                    
                                    <!-- First Name -->
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" id="first_name" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               placeholder="Enter first name" 
                                               value="{{ old('first_name', $student->first_name ?? '') }}" 
                                               required>
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <!-- Middle Name -->
                                    <div class="form-group">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                               class="form-control @error('middle_name') is-invalid @enderror" 
                                               placeholder="Enter middle name" 
                                               value="{{ old('middle_name', $student->middle_name ?? '') }}" required>
                                        @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <!-- Surname -->
                                    <div class="form-group">
                                        <label for="surname">Surname</label>
                                        <input type="text" name="surname" id="surname" 
                                               class="form-control @error('surname') is-invalid @enderror" 
                                               placeholder="Enter surname" 
                                               value="{{ old('surname', $student->surname ?? '') }}" 
                                               required>
                                        @error('surname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" 
                                                class="form-control @error('gender') is-invalid @enderror" 
                                                required>
                                            <option value="" disabled selected>-- Select gender --</option>
                                            <option value="male" {{ old('gender', $student->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <!-- Stream -->
                                    @if ($streams->count() > 0)
                                        <div class="form-group">
                                            <label for="gender">Stream</label>
                                            <select name="stream_id" id="stream_id" 
                                                    class="form-control @error('stream_id') is-invalid @enderror" 
                                                    required>
                                                <option value="" disabled selected>-- Select Stream --</option>
                                                @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}" 
                                                        {{ old('stream_id', $student->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                        {{ $stream->schoolClass->name }}  {{ $stream->alias }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('stream_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                    <input type="hidden" name="stream_id" value="{{ $streams->first()->id ?? '' }}">
                                    @endif

                                    <div class="form-group">
                                        <label for="phone">Parent Phone Number(Optional)</label>
                                        <input type="text" name="phone" id="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               placeholder="Enter phone number" 
                                               value="{{ old('phone', $student->phone ?? '') }}">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-{{ isset($student) ? 'save' : 'plus' }}"></i> 
                                            {{ isset($student) ? 'Update Student' : 'Add Student' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
        </section>
    </div>

    <script>
const downloadExcelButton = document.getElementById('download-excel-button');
const loadingSpinner = document.getElementById('loading-spinner');
const form = document.getElementById('myForm'); // Get the form element

downloadExcelButton.addEventListener('click', async (event) => {
  event.preventDefault(); // Prevent default form submission

  try {
    loadingSpinner.style.display = 'inline-block'; 

    const response = await fetch('/start-excel-download', {
      method: 'POST', // Use POST method for form submission
      // Include any necessary data in the request body 
      // (e.g., form data)
      body: new FormData(form) 
    });

    if (response.ok) {
      await new Promise(resolve => setTimeout(resolve, 2000)); 
      window.location.href = '/download-excel'; 
    } else {
      console.error('Error starting download:', response.status);
      // Handle error (e.g., display an error message to the user)
    }
  } catch (error) {
    console.error('Error:', error);
    // Handle error (e.g., display an error message to the user)
  } finally {
    loadingSpinner.style.display = 'none'; 
  }
});
    </script>
</x-layout>

