
     <section class="section">
        <div class="section-body">
            <div class=" row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Adding Teachers in Bulky (Excel Upload)</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle"></i>
                                <span style="font-size:.85em"> <strong>Important:</strong> Before uploading teachers' details, please download the template file below to ensure your Excel sheet matches the required format. </span>
                            </div>

                            @if ($errors->has('users_excel') || ($errors->any() && !collect($errors->keys())->intersect(['first_name', 'middle_name', 'sur_name', 'gender', 'phone', 'email', 'role'])->count()))
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="mr-2 fas fa-danger-circle"></i>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action=""  wire:submit.prevent='import' enctype="multipart/form-data">
                                @csrf

                                <!-- File Upload Field -->
                                <div class="mb-4 form-group">
                                    <label for="file">Select Excel File</label>
                                    <input type="file" class="form-control" id="file" wire:model="users_excel"  accept=".xlsx,.xls">
                                    <span wire:loading wire:target="users_excel">
                                        <i class="fa fa-spinner fa-spin"></i>Please wait, uploading...
                        
                                    </span>
                                    @error('users_excel')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                    <small class="form-text text-muted">
                                        Formats: .xlsx, .xls, .csv | Maximum size: 2MB
                                    </small>
                                </div>

                                <div class="mb-2 d-flex ">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr='hidden' wire:target="import">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                    <span wire:loading wire:target="import">
                                        <i class="fa fa-spinner fa-spin"></i>Please wait, downloading...
                        
                                    </span>

                                    <a
                                    class="ml-2 text-white btn btn-success" wire:click='downloadSample' wire:loading.attr='hidden' wire:target="downloadSample">
                                    <i class="fas fa-download"></i> <span id="download-text">Download Template</span> 
                                    </a>
                                    
                                    <span wire:loading wire:target="downloadSample">
                                        <i class="fa fa-spinner fa-spin"></i>Downloading...
                        
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Adding Teachers Manually</h4>
                        </div>
                        <div class="card-body">

                            <form action="" method="POST" class="mb-2" wire:submit.prevent="add">
                                @csrf

                                <!-- First Name -->
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" wire:model="first_name"
                                        value="{{ old('first_name', $teacher->first_name ?? '') }}">
                                    @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Middle Name -->
                                <div class="form-group">
                                    <label for="middle_name">Middle Name (Optional)</label>
                                    <input type="text" class="form-control" id="middle_name" wire:model="middle_name"
                                        value="{{ old('middle_name', $teacher->middle_name ?? '') }}">
                                    @error('middle_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Surname -->
                                <div class="form-group">
                                    <label for="sur_name">Surname</label>
                                    <input type="text" class="form-control" id="sur_name" wire:model="sur_name"
                                        value="{{ old('sur_name', $teacher->sur_name ?? '') }}">
                                    @error('sur_name')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" wire:model="gender">
                                        <option value=""  >Select Gender</option>
                                        <option value="male"  >Male</option>
                                        <option value="female"  >Female</option>
                                    
                                    </select>
                                    @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                
                                <!-- Phone -->
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" wire:model="phone"
                                    placeholder="eg, 07XXXXXXXXX"
                                        value="{{ old('phone', $teacher->phone ?? '') }}">
                                    @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                                                        <small class="pt-2 form-text text-info">
                                        <i class="fas fa-exclamation-circle text-info"></i>&nbspThe phone number will be used as to send teacher's username and password.</small>
                                </div>


                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email">Email (Optional)</label>
                                    <input type="email" class="form-control" id="email" wire:model="email"
                                        value="{{ old('email', $teacher->email ?? '') }}">
                                    @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Role -->
                                @php
                                    Str::contains(strtolower(auth()->user()->school->name),'secondary')? $role = "Assistant Head of School" : $role = "Assistant Headteacher";
                                @endphp
                                    
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select class="form-control" id="role" wire:model="role">
                                        <option value="">Select Role</option>
                                        <option value="teacher" >Teacher</option>
                            
                                        @if($academicTeacherCount < 2)
                                            <option value="academic teacher">Academic Teacher</option>
                                        @endif

                                        @if($HeaderTeacherAssistCount < 1)
                                            <option value="assistant headteacher"> {{ $role }}</option>
                                        @endif
                                        
                                    </select>
                                    @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr='hidden'>
                                        <i class="fas fa-plus"></i>
                                        Add Teacher

                                    </button>
                                    <span wire:loading>
                                        <i class="fa fa-spinner fa-spin"></i>

                                    </span>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
