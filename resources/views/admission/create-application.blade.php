<x-layout>
    <x-slot:title>
        Application Form
    </x-slot:title>
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />
        <div class="main-content">
            <section class="section">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body">
                        @php
                            $applicationType = 'Form I';
                        @endphp

                        @if(is_array($school->school_type) && in_array('O-Level', $school->school_type) && in_array('A-Level', $school->school_type))
                            <div class="card border-0 shadow-sm mb-4" id="app-type-card" style="border-radius: 16px;">
                                <div class="card-body p-4 p-md-5">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark font-13 mb-2">Choose application type <span class="text-danger">*</span></label>
                                        <div class="d-flex align-items-center mb-4" style="gap: 16px;">
                                            
                                            <!-- Form One -->
                                            <div class="col-md flex-grow-1 p-0">
                                                <label class="card border p-2.5 p-md-3 mb-0 text-left w-100 h-100" style="border-radius: 10px; cursor: pointer;">
                                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                                        <input type="radio" name="application_type" value="form_one" class="app-type-radio" {{ old('application_type') === 'form_one' ? 'checked' : '' }}>
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-0 font-13">Form One</h6>
                                                            <small class="text-muted d-block font-11">Apply for Ordinary Level (Form I)</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- Form Five -->
                                            <div class="col-md flex-grow-1 p-0">
                                                <label class="card border p-2.5 p-md-3 mb-0 text-left w-100 h-100" style="border-radius: 10px; cursor: pointer;">
                                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                                        <input type="radio" name="application_type" value="form_five" class="app-type-radio" {{ old('application_type') === 'form_five' ? 'checked' : '' }}>
                                                        <div>
                                                            <h6 class="font-weight-bold text-dark mb-0 font-13">Form Five</h6>
                                                            <small class="text-muted d-block font-11">Apply for Advanced Level (Form V)</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                        </div>
                                        @error('application_type') <small class="text-danger font-12 d-block mt-1">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- MAIN FORM CONTENT -->
                        <div id="form-content-below" @if(is_array($school->school_type) && in_array('O-Level', $school->school_type) && in_array('A-Level', $school->school_type)) style="display: none;" @endif>
                            
                            <!-- Top Header Navigation / Title -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center" style="gap: 14px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                        style="width: 48px; height: 48px; background-color: #0f766e; border-radius: 12px !important;">
                                        <i class="fas fa-graduation-cap font-20"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold mb-0 text-dark" style="letter-spacing: -0.3px;">{{ $applicationType }} Application</h5>
                                        <small class="text-uppercase font-weight-bold text-muted" style="font-size: 10px; letter-spacing: 0.8px;"> {{ $school->name }}</small>
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
                                            <span class="text-uppercase font-weight-bold" style="font-size: 11px; color: #0f766e; letter-spacing: 1px;">@if($applicationType == 'Form I') {{ date('Y')+1 }} @else {{ date('Y') }} @endif Intake</span>
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
                                                        @error('parents.'.$index.'.relationship') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                                    </div>

                                                    <div class="col-md-6 form-group mb-3">
                                                        <label class="font-weight-bold text-dark font-13">Phone number <span class="text-danger">*</span></label>
                                                        <input type="text" wire:model="parents.{{ $index }}.phone" class="form-control px-3" placeholder="+255 7XX XXX XXX" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                        @error('parents.'.$index.'.phone') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                                    </div>

                                                    <div class="col-md-6 form-group mb-3">
                                                        <label class="font-weight-bold text-dark font-13">First name <span class="text-danger">*</span></label>
                                                        <input type="text" wire:model="parents.{{ $index }}.first_name" class="form-control px-3" placeholder="Enter first name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                        @error('parents.'.$index.'.first_name') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                                    </div>

                                                    <div class="col-md-6 form-group mb-3">
                                                        <label class="font-weight-bold text-dark font-13">Surname <span class="text-danger">*</span></label>
                                                        <input type="text" wire:model="parents.{{ $index }}.surname" class="form-control px-3" placeholder="Enter surname" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                                                        @error('parents.'.$index.'.surname') <small class="text-danger font-12">{{ $message }}</small> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Add Parent Button -->
                                        <button type="button" class="btn btn-outline-primary border-dashed font-weight-bold px-4 py-2" wire:click="addParent" style="border-radius: 8px; border-style: dashed; border-color: #0f766e; color: #0f766e; font-size: 13px;">
                                            <i class="fas fa-plus mr-1"></i> Add Another Parent / Guardian
                                        </button>

                                    </div>
                                </div>

                                <!-- SECTION 04: Academic Background / Schools Attended -->
                                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                                    <div class="card-body p-4 p-md-5">
                                        
                                        <div class="d-flex align-items-center mb-4" style="gap: 16px;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                                style="width: 44px; height: 44px; background-color: #ccfbf1; color: #0f766e; border-radius: 12px !important;">
                                                <i class="fas fa-school font-18"></i>
                                            </div>
                                            <div>
                                                <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">Section 04</span>
                                                <h4 class="font-weight-bold text-dark mb-0">Schools attended</h4>
                                                <p class="text-muted mb-0 font-13">Provide details about primary or secondary schools previously attended.</p>
                                            </div>
                                        </div>

                                        <!-- Loop Through Schools Array -->
                                        @foreach($schools_attended as $sIndex => $schoolItem)
                                            <div class="card border p-4 mb-4" style="border-radius: 12px; border-color: #e2e8f0 !important; background-color: #f8fafc;">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <span class="text-uppercase font-weight-bold" style="font-size: 10px; color: #0f766e; letter-spacing: 0.8px;">School 0{{ $sIndex + 1 }}</span>
                                                        <h6 class="font-weight-bold text-dark mb-0">School details</h6>
                                                    </div>
                                                    @if(count($schools_attended) > 1)
                                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" wire:click="removeSchool({{ $sIndex }})" style="border-radius: 6px;">
                                                            <i class="fas fa-trash-alt mr-1"></i> Remove
                                                        </button>
                                                    @endif
                                                </div>

<div class="row">
                    <!-- School Name -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">School Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.name" class="form-control px-3" placeholder="Enter school name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.name') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Region -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Region <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.region" class="form-control px-3" placeholder="e.g. Dar es Salaam" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.region') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- District -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">District <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.district" class="form-control px-3" placeholder="e.g. Kinondoni" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.district') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Ward -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Ward <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.ward" class="form-control px-3" placeholder="e.g. Morocco" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.ward') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Street (Optional) -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Street / Village <span class="text-muted font-weight-normal">(Optional)</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.street" class="form-control px-3" placeholder="e.g. Mwinyi Juma Road" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.street') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Completion Year -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Completion / Graduation Year <span class="text-danger">*</span></label>
                        <input type="number" wire:model="schools_attended.{{ $sIndex }}.year_completed" class="form-control px-3" placeholder="e.g. 2025" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.year_completed') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Headteacher Name -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Headteacher's Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.headteacher_name" class="form-control px-3" placeholder="Enter headteacher's full name" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.headteacher_name') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>

                    <!-- Headteacher Phone -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold text-dark font-13">Headteacher's Phone Number <span class="text-danger">*</span></label>
                        <input type="text" wire:model="schools_attended.{{ $sIndex }}.headteacher_phone" class="form-control px-3" placeholder="+255 7XX XXX XXX" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
                        @error('schools_attended.'.$sIndex.'.headteacher_phone') <small class="text-danger font-12">{{ $message }}</small> @enderror
                    </div>
                    <!-- Recent Examination Maximum Scores Sub-section -->
                     @if(count($schools_attended) == 0)
<div class="mt-4 pt-3 border-top">
    <h6 class="font-weight-bold text-dark mb-3">
        <i class="fas fa-chart-bar mr-2 text-teal" style="color: #0f766e;"></i> Most Recent Exam — Subject Scores
    </h6>
    <div class="row">
        <!-- Mathematics Max Score -->
        <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark font-13">Mathematics <span class="text-danger">*</span></label>
            <input type="number" wire:model="schools_attended.{{ $sIndex }}.max_score_maths" class="form-control px-3" placeholder="e.g. 100" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
            @error('schools_attended.'.$sIndex.'.max_score_maths') <small class="text-danger font-12">{{ $message }}</small> @enderror
        </div>

        <!-- English Max Score -->
        <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark font-13">English <span class="text-danger">*</span></label>
            <input type="number" wire:model="schools_attended.{{ $sIndex }}.max_score_english" class="form-control px-3" placeholder="e.g. 100" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
            @error('schools_attended.'.$sIndex.'.max_score_english') <small class="text-danger font-12">{{ $message }}</small> @enderror
        </div>

        <!-- Science Max Score -->
        <div class="col-md-4 form-group mb-3">
            <label class="font-weight-bold text-dark font-13">Science <span class="text-danger">*</span></label>
            <input type="number" wire:model="schools_attended.{{ $sIndex }}.max_score_science" class="form-control px-3" placeholder="e.g. 100" style="height: 46px; border-radius: 8px; border-color: #cbd5e1;">
            @error('schools_attended.'.$sIndex.'.max_score_science') <small class="text-danger font-12">{{ $message }}</small> @enderror
        </div>
    </div>
</div>
@endif
                </div>
                                            </div>
                                        @endforeach

                                        <!-- Add School Button -->
                                        <button type="button" class="btn btn-outline-primary border-dashed font-weight-bold px-4 py-2" wire:click="addSchool" style="border-radius: 8px; border-style: dashed; border-color: #0f766e; color: #0f766e; font-size: 13px;">
                                            <i class="fas fa-plus mr-1"></i> Add Another School Attended
                                        </button>

                                    </div>
                                </div>

                                <!-- Submit Button Area -->
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold shadow-sm" style="background-color: #0f766e; border-radius: 10px; font-size: 15px;">
                                        Submit Application <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layout>