<x-layout >
    <x-slot:title>
        Application Form
    </x-slot:title>
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />
        <div class="main-content">
            <section class="section">
                <div class="py-2" style="background-color: #f4f8f7; min-height: 100vh; color: #1e293b;">
                    <div class="container" style="max-width: 2000px;">

                        <!-- Top Header Navigation / Title -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center" style="gap: 14px;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                    style="width: 48px; height: 48px; background-color: #0f766e; border-radius: 12px !important;">
                                    <i class="fas fa-graduation-cap font-20"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold mb-0 text-dark" style="letter-spacing: -0.3px;">Student Application</h5>
                                    <small class="text-uppercase font-weight-bold text-muted" style="font-size: 10px; letter-spacing: 0.8px;">Education Opportunity Programme</small>
                                </div>
                            </div>

                            <a href="{{ route('admissions.create') }}" target="_blank" class="btn btn-primary font-weight-bold shadow-sm px-3" style="background-color: #0f766e; border-radius: 8px; font-size: 13px; text-decoration: none;">
                                        <i class="fas fa-plus mr-1"></i> Upload applications
                                    </a>
                        </div>

                        <!-- Start Your Application Banner Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background-color: #ffffff;">
                            <div class="card-body p-4 p-md-5">
                                <div class="row align-items-center">
                                    <div class="col-md-7 mb-4 mb-md-0">
                                        <span class="text-uppercase font-weight-bold" style="font-size: 11px; color: #0f766e; letter-spacing: 1px;">2025 Student Intake</span>
                                        <h1 class="font-weight-bold text-dark mt-1 mb-2" style="font-size: 36px; letter-spacing: -0.5px;">Start your application</h1>
                                        <p class="text-muted mb-0 font-14" style="line-height: 1.6;">
                                            Tell us about yourself and your education journey. Fields marked with an asterisk (<span class="text-danger">*</span>) are required.
                                        </p>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="card border-0 p-3" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-weight-bold font-13 text-dark">Application progress</span>
                                                <span class="font-weight-bold font-13 text-dark">4 sections</span>
                                            </div>
                                            <div class="progress mb-2" style="height: 6px; background-color: #e2e8f0;">
                                                <div class="progress-bar rounded-pill" role="progressbar" style="width: 100%; background-color: #0f766e;"></div>
                                            </div>
                                            <small class="text-muted font-12">Complete all sections before submitting</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Submission Form -->
                        <form wire:submit.prevent="store">

                            <!-- SECTION 01: Examination Preferences -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                <div class="card-body p-4 p-md-5">
                                    
                                    <div class="d-flex align-items-center mb-4" style="gap: 16px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 44px; height: 44px; background-color: #ccfbf1; color: #0f766e; border-radius: 12px !important;">
                                            <i class="far fa-calendar-alt font-18"></i>
                                        </div>
                                        <div>
                                            <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">Section 01</span>
                                            <h4 class="font-weight-bold text-dark mb-0">Examination preferences</h4>
                                            <p class="text-muted mb-0 font-13">Choose a convenient examination center, date and preferred language.</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Exam Center Dropdown -->
                                        <div class="col-md-6 form-group mb-4">
                                            <label class="font-weight-bold text-dark font-13">Exam center <span class="text-danger">*</span></label>
                                            <select wire:model="center_id" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                <option value="">Choose an exam center</option>
                                                @foreach($examCenters as $center)
                                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('center_id') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Preferred Exam Date -->
                                        <div class="col-md-6 form-group mb-4">
                                            <label class="font-weight-bold text-dark font-13">Preferred exam date <span class="text-danger">*</span></label>
                                            <input type="date" wire:model="exam_date" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            @error('exam_date') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>
                                    </div>

                                    <!-- Language Preference Radio Cards -->
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark font-13 mb-3">Language preference <span class="text-danger">*</span></label>
                                        <div class="row" style="gap: 12px; margin: 0;">
                                            
                                            <!-- English -->
                                            <div class="col-md flex-grow-1 p-0">
                                                <label class="card border p-3 mb-0 text-left w-100 h-100" 
                                                    style="border-radius: 12px; cursor: pointer; border-color: {{ $exam_language === 'english' ? '#0f766e' : '#e2e8f0' }} !important; background-color: {{ $exam_language === 'english' ? '#f0fdf4' : '#ffffff' }}; transition: all 0.2s;">
                                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                                        <input type="radio" wire:model.live="exam_language" value="english" class="mt-0">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #ccfbf1; color: #0f766e;">
                                                            <i class="fas fa-globe font-14"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-0 font-14">English</h6>
                                                            <small class="text-muted d-block font-12" style="line-height: 1.3;">I prefer to receive exam instructions in English</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- Kiswahili -->
                                            <div class="col-md flex-grow-1 p-0">
                                                <label class="card border p-3 mb-0 text-left w-100 h-100" 
                                                    style="border-radius: 12px; cursor: pointer; border-color: {{ $exam_language === 'kiswahili' ? '#0f766e' : '#e2e8f0' }} !important; background-color: {{ $exam_language === 'kiswahili' ? '#f0fdf4' : '#ffffff' }}; transition: all 0.2s;">
                                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                                        <input type="radio" wire:model.live="exam_language" value="kiswahili" class="mt-0">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #ccfbf1; color: #0f766e;">
                                                            <i class="fas fa-globe font-14"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-0 font-14">Kiswahili</h6>
                                                            <small class="text-muted d-block font-12" style="line-height: 1.3;">Napendelea maelekezo ya mtihani kwa Kiswahili</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- No Preference -->
                                            <div class="col-md flex-grow-1 p-0">
                                                <label class="card border p-3 mb-0 text-left w-100 h-100" 
                                                    style="border-radius: 12px; cursor: pointer; border-color: {{ $exam_language === 'no_preference' ? '#0f766e' : '#e2e8f0' }} !important; background-color: {{ $exam_language === 'no_preference' ? '#f0fdf4' : '#ffffff' }}; transition: all 0.2s;">
                                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                                        <input type="radio" wire:model.live="exam_language" value="no_preference" class="mt-0">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: #ccfbf1; color: #0f766e;">
                                                            <i class="fas fa-globe font-14"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-0 font-14">No preference</h6>
                                                            <small class="text-muted d-block font-12" style="line-height: 1.3;">I am comfortable with either language</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                        </div>
                                        @error('exam_language') <small class="text-danger font-12 d-block mt-2">{{ $message }}</small> @enderror
                                    </div>

                                </div>
                            </div>

                            <!-- SECTION 02: Applicant Details -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                <div class="card-body p-4 p-md-5">
                                    
                                    <div class="d-flex align-items-center mb-4" style="gap: 16px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 44px; height: 44px; background-color: #ccfbf1; color: #0f766e; border-radius: 12px !important;">
                                            <i class="far fa-user font-18"></i>
                                        </div>
                                        <div>
                                            <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">Section 02</span>
                                            <h4 class="font-weight-bold text-dark mb-0">Applicant details</h4>
                                            <p class="text-muted mb-0 font-13">Basic personal and contact information about the applicant.</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- First Name -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">First name <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="first_name" class="form-control px-3" placeholder="Enter first name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            @error('first_name') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Middle Name -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">Middle name</label>
                                            <input type="text" wire:model="middlename" class="form-control px-3" placeholder="Enter middle name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            @error('middlename') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Surname -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">Surname <span class="text-danger">*</span></label>
                                            <input type="text" wire:model="surname" class="form-control px-3" placeholder="Enter surname" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            @error('surname') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Gender -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">Gender <span class="text-danger">*</span></label>
                                            <select wire:model="gender" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                <option value="">Select an option</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                            @error('gender') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Religion -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">Religion <span class="text-danger">*</span></label>
                                            <select wire:model="religion" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                <option value="">Select an option</option>
                                                <option value="christian">Christian</option>
                                                <option value="islam">Islam</option>
                                                <option value="other">Other</option>
                                            </select>
                                            @error('religion') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Denomination -->
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="font-weight-bold text-dark font-13">Denomination</label>
                                            <input type="text" wire:model="denomination" class="form-control px-3" placeholder="e.g. Catholic, Lutheran" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            @error('denomination') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>

                                        <!-- Current Residence -->
                                        <div class="col-md-6 form-group mb-0">
                                            <label class="font-weight-bold text-dark font-13">Current residence <span class="text-danger">*</span></label>
                                            <select wire:model="current_residence" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                <option value="">Select an option</option>
                                                <option value="stays_with_father">Stays with father</option>
                                                <option value="stays_with_mother">Stays with mother</option>
                                                <option value="stays_with_both">Stays with both parents</option>
                                                <option value="stays_with_guardian">Stays with guardian</option>
                                            </select>
                                            @error('current_residence') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- SECTION 03: Parent & Guardian Details -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                <div class="card-body p-4 p-md-5">
                                    
                                    <div class="d-flex align-items-center mb-4" style="gap: 16px;">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                            style="width: 44px; height: 44px; background-color: #ccfbf1; color: #0f766e; border-radius: 12px !important;">
                                            <i class="fas fa-users font-18"></i>
                                        </div>
                                        <div>
                                            <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">Section 03</span>
                                            <h4 class="font-weight-bold text-dark mb-0">Parent & guardian details</h4>
                                            <p class="text-muted mb-0 font-13">Provide contact and residence details for the applicant's father, mother or guardian.</p>
                                        </div>
                                    </div>

                                    <!-- Loop Through Parents Array -->
                                    @foreach($parents as $index => $parent)
                                        <div class="card border p-4 mb-4" style="border-radius: 12px; border-color: #e2e8f0 !important; background-color: #f8fafc;">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">Contact 0{{ $index + 1 }}</span>
                                                    <h6 class="font-weight-bold text-dark mb-0">Parent or guardian details</h6>
                                                </div>
                                                @if(count($parents) > 1)
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" wire:click="removeParent({{ $index }})" style="border-radius: 6px;">
                                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Relationship to applicant <span class="text-danger">*</span></label>
                                                    <select wire:model="parents.{{ $index }}.relationship" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                        <option value="">Select relationship</option>
                                                        <option value="father">Father</option>
                                                        <option value="mother">Mother</option>
                                                        <option value="guardian">Guardian</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Phone number <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.phone" class="form-control px-3" placeholder="+255 7XX XXX XXX" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">First name <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.first_name" class="form-control px-3" placeholder="Enter first name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Middle name</label>
                                                    <input type="text" wire:model="parents.{{ $index }}.middlename" class="form-control px-3" placeholder="Enter middle name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Surname <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.surname" class="form-control px-3" placeholder="Enter surname" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Occupation <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.occupation" class="form-control px-3" placeholder="e.g. Farmer, Teacher" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Region <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.region" class="form-control px-3" placeholder="e.g. Arusha" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">District <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.district" class="form-control px-3" placeholder="Enter district" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Ward <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.ward" class="form-control px-3" placeholder="Enter ward" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-0">
                                                    <label class="font-weight-bold text-dark font-13">Street / Village <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="parents.{{ $index }}.street" class="form-control px-3" placeholder="Enter street or village" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Add Parent Button -->
                                    <button type="button" class="btn border-dashed font-weight-bold py-2 px-3 d-inline-flex align-items-center" 
                                            wire:click="addParent"
                                            style="border: 1.5px dashed #0f766e; color: #0f766e; background-color: #f0fdf4; border-radius: 8px; gap: 8px; font-size: 13px;">
                                        <i class="fas fa-plus font-12"></i> Add mother, father or guardian
                                    </button>

                                </div>
                            </div>

                            <!-- SECTION 04: School Information -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                <div class="card-body p-4 p-md-5">
                                    
                                    @foreach($schools as $sIndex => $school)
                                        <div class="mb-4">
                                            <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">School 0{{ $sIndex + 1 }}</span>
                                            <h4 class="font-weight-bold text-dark mb-4">School information</h4>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold text-dark font-13">School name <span class="text-danger">*</span></label>
                                                <input type="text" wire:model="schools.{{ $sIndex }}.name" class="form-control px-3" placeholder="Enter the official school name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Region <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.region" class="form-control px-3" placeholder="e.g. Arusha" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">District <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.district" class="form-control px-3" placeholder="e.g. Arusha City" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Ward <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.ward" class="form-control px-3" placeholder="Enter ward" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Street / Village <span class="text-danger">*</span></label>
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.street" class="form-control px-3" placeholder="Enter street or village" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Maximum standard <span class="text-danger">*</span></label>
                                                    <select wire:model="schools.{{ $sIndex }}.max_standard" class="form-control px-3" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                        <option value="">Select level</option>
                                                        <option value="form_4">Form IV</option>
                                                        <option value="form_6">Form VI</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 form-group mb-3">
                                                    <label class="font-weight-bold text-dark font-13">Finalist status <span class="text-danger">*</span></label>
                                                    <div class="d-flex align-items-center p-1 bg-light border rounded" style="height: 46px; border-radius: 8px !important; border-color: #cbd5e1 !important;">
                                                        <button type="button" class="btn btn-sm w-50 font-weight-bold {{ $schools[$sIndex]['is_finalist'] ? 'btn-white shadow-sm text-dark' : 'text-muted' }}" 
                                                                wire:click="$set('schools.{{ $sIndex }}.is_finalist', true)" style="border-radius: 6px;">
                                                            Yes
                                                        </button>
                                                        <button type="button" class="btn btn-sm w-50 font-weight-bold {{ !$schools[$sIndex]['is_finalist'] ? 'btn-white shadow-sm text-dark' : 'text-muted' }}" 
                                                                wire:click="$set('schools.{{ $sIndex }}.is_finalist', false)" style="border-radius: 6px;">
                                                            No
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-4" style="border-top: 1px dashed #e2e8f0;">

                                            <!-- Average Subject Scores -->
                                            <div class="row align-items-center mb-4">
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <h6 class="font-weight-bold text-dark mb-0 font-14">Average subject scores</h6>
                                                    <small class="text-muted font-12">Enter the applicant's most recent average as a percentage.</small>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="font-weight-bold text-dark font-12">Mathematics <span class="text-danger">*</span></label>
                                                            <input type="number" wire:model="schools.{{ $sIndex }}.math_score" class="form-control text-center" placeholder="0–100" min="0" max="100" style="height: 42px; border-radius: 8px;">
                                                        </div>
                                                        <div class="col">
                                                            <label class="font-weight-bold text-dark font-12">English <span class="text-danger">*</span></label>
                                                            <input type="number" wire:model="schools.{{ $sIndex }}.english_score" class="form-control text-center" placeholder="0–100" min="0" max="100" style="height: 42px; border-radius: 8px;">
                                                        </div>
                                                        <div class="col">
                                                            <label class="font-weight-bold text-dark font-12">Science <span class="text-danger">*</span></label>
                                                            <input type="number" wire:model="schools.{{ $sIndex }}.science_score" class="form-control text-center" placeholder="0–100" min="0" max="100" style="height: 42px; border-radius: 8px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-4" style="border-top: 1px dashed #e2e8f0;">

                                            <!-- Headteacher Contact -->
                                            <div class="row align-items-center">
                                                <div class="col-md-4 mb-2 mb-md-0 d-flex align-items-start" style="gap: 8px;">
                                                    <span class="rounded-circle mt-1" style="width: 8px; height: 8px; background-color: #f59e0b; flex-shrink: 0;"></span>
                                                    <div>
                                                        <h6 class="font-weight-bold text-dark mb-0 font-14">Headteacher contact</h6>
                                                        <small class="text-muted font-12">Optional for applicants who are not finalists.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 form-group mb-2 mb-md-0">
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.headteacher_name" class="form-control px-3" placeholder="Full name" style="height: 42px; border-radius: 8px;">
                                                </div>
                                                <div class="col-md-4 form-group mb-0">
                                                    <input type="text" wire:model="schools.{{ $sIndex }}.headteacher_phone" class="form-control px-3" placeholder="+255 7XX XXX XXX" style="height: 42px; border-radius: 8px;">
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                    <!-- Add School Button -->
                                    <button type="button" class="btn border-dashed font-weight-bold py-2 px-3 d-inline-flex align-items-center mt-2" 
                                            wire:click="addSchool"
                                            style="border: 1.5px dashed #0f766e; color: #0f766e; background-color: #f0fdf4; border-radius: 8px; gap: 8px; font-size: 13px;">
                                        <i class="fas fa-plus font-12"></i> Add another school
                                    </button>

                                </div>
                            </div>

                            <!-- Footer Confirmation & Submit Button -->
                            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between my-5" style="gap: 16px;">
                                <span class="text-muted font-14">By submitting, you confirm that the information provided is accurate and complete.</span>
                                <button type="submit" class="btn font-weight-bold text-white px-4 py-3 shadow d-inline-flex align-items-center" 
                                        style="background-color: #0f766e; border-radius: 12px; gap: 10px; font-size: 15px;">
                                    Submit application <i class="fas fa-arrow-right font-13"></i>
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layout>