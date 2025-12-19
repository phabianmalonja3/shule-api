<x-layout>
    <x-slot:title>
        School View Page
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <h2 class="section-title">School Details</h2>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (isset($school))
                    <div class="card" >
                        <div class="card-header d-flex justify-content-between align-items-center" style=" border-top: 2px solid {{ $school->color }};">
                            <h4 class="text-center">{{ $school->name }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <!-- School Details Section (Left Side) -->
                                <div class="col-md-8">
                                    <p><strong>Color:</strong> <span style="color: {{ $school->color }};"> <span class="rounded-lg" style="background-color: {{ $school->color }};"> jkj</span></span></p></p>
                            {{--  --}}

                                    <p><strong>School Motto:</strong> {{ $school->motto }}</p>
                                    <p><strong>Contract Number:</strong> {{ $school->contract_number }}</p>
                                    <p><strong>Address:</strong> {{ $school->address }}</p>
                                    <p><strong>Location:</strong> @if(isset($school->region, $school->district, $school->ward))
                                        {{ $school->ward }},
                                        {{ $school->district}},
                                        {{ $school->region }},
                                    @else
                                        <em>No location data</em>
                                    @endif</p>
                                    <p><strong>School Type:</strong>
                                        @if ($school->school_type && is_array($types = json_decode($school->school_type, true)))
                                            {{ implode(', ', $types) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>

                                    <p><strong>Sponsorship Type:</strong> {{ $school->sponsorship_type }}</p>

                                    <p><strong>Email:</strong> {{ $school->headerTeacher->email ?? 'no Email' }}</p>
                                    <!-- Display Header Teacher Details -->
                                    @if ($school->headerTeacher)
                                        <p><strong>Header Teacher:</strong> {{ $school->headerTeacher->name ?? 'No name' }}</p>
                                        <p><strong>Header Teacher Phone:</strong> {{ $school->headerTeacher->phone }}</p>
                                    @else
                                        <p><strong>Header Teacher:</strong> N/A</p>
                                    @endif
                                    <p><strong>Number of Teachers:</strong> {{ $school->teachers->count() ?? 'N/A' }}</p>
                                    <p><strong>Number of Active Students:</strong> {{ $school->num_of_active_students ?? 'N/A' }}</p>
                                    <p><strong>Number of Inactive Students:</strong> {{ $school->num_of_inactive_students ?? 'N/A' }}</p>
                                    <p><strong>Total Number of Students:</strong> {{ $school->total_students ?? 'N/A' }}</p>
                                </div>

                                <!-- School Logo Section (Right Side) -->
                                <div class="text-center col-md-4">
                                    @if ($school->logo)
                                        <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo" class="rounded img-fluid" style="max-height: 150px;">
                                    @else
                                        <img src="{{ asset('images/default-logo.png') }}" alt="Default Logo" class="rounded img-fluid" style="max-height: 150px;">
                                    @endif
                                </div>
                            </div>

                            <p><strong>Status:</strong>
                                <span class="badge {{ $school->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $school->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>

                        <div class="text-center card-footer">
                            <!-- Change Status Button -->
                            <form action="{{ route('school.updateStatus', $school->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary">
                                    {{ $school->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- NECTA Results Section (New Card) -->
                    <div class="mt-4 card">
                        <div class="card-header" style="text-align: center; display: flex; justify-content: center; align-items: center; flex-direction: column; border-top: 2px solid #007bff; ">
                            <!-- NECTA Results Card Logo -->
                            <img src="{{ asset('logo/necta_logo-01.png') }}" alt="NECTA Logo" class="mb-2 rounded img-fluid" style="max-height: 150px;">
                            <h4>NECTA Results</h4>
                        </div>
                        <div class="card-body">
                            <p><strong>Academic Position (Country):</strong> {{ $school->academic_position_country ?? 'N/A' }}</p>
                            <p><strong>Academic Position (Region):</strong> {{ $school->academic_position_region ?? 'N/A' }}</p>
                            <p><strong>Academic Position (District):</strong> {{ $school->academic_position_district ?? 'N/A' }}</p>
                            <p><strong>Academic Position (Ward):</strong> {{ $school->academic_position_ward ?? 'N/A' }}</p>
                            <p><strong>Average Grade:</strong> {{ $school->average_grade ?? 'N/A' }}</p>
                            <p><strong>Number of A Grades:</strong> {{ $school->num_of_A ?? 'N/A' }}</p>
                            <p><strong>Number of B Grades:</strong> {{ $school->num_of_B ?? 'N/A' }}</p>
                            <p><strong>Number of C Grades:</strong> {{ $school->num_of_C ?? 'N/A' }}</p>

                        </div>
                    </div>




                @else
                    <p>No school details found.</p>
                @endif
            </div>
        </section>
    </div>
</x-layout>
