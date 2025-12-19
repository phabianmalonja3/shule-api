<!-- Show Application Details Page -->
<x-layout>
    <x-slot:title>
        Application Show Page
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />
    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">

                <div class="container mt-5">
                    <div class="text-center row">
                        <!-- Application Details Section -->
                        <div class="mx-auto mt-4 col-md-6">
                            <h3>Application Details</h3>

                            <!-- Display Application Details -->
                            <div class="card">

                            </div>
                        </div>
                        <div class="container mt-5">
                            <div class="row">
                                <!-- Left Column for Application Details -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4>{{ $application->school_name }}</h4>

                                            <!-- Registration Number -->
                                            <p><i class="fas fa-id-card"></i> <strong>Registration Number:</strong>
                                                {{ $school->registration_number ?? 'No Registration Number' }}
                                            </p>

                                            <!-- Motto -->
                                            <p><i class="fas fa-quote-left"></i> <strong>Motto:</strong>
                                                {{ $school->motto ?? 'No Motto Available' }}
                                            </p>

                                            <!-- Color -->
                                            <p><i class="fas fa-paint-brush"></i> <strong>Color:</strong>
                                                <span style="color: {{ $school->color ?? '#000000' }};">
                                                    {{ $school->color ?? 'No Color Available' }}
                                                </span>
                                            </p>

                                            <!-- Location Details -->
                                            @if(!empty($school->location))
                                            @php
                                            // Decode the JSON location data
                                            $location = json_decode($school->location, true);
                                            @endphp

                                            <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong></p>
                                            <ul>
                                                <li><strong>Region:</strong> {{ $location['region'] ?? 'N/A' }}</li>
                                                <li><strong>District:</strong> {{ $location['district'] ?? 'N/A' }}</li>
                                                <li><strong>Ward:</strong> {{ $location['ward'] ?? 'N/A' }}</li>
                                            </ul>
                                            @else
                                            <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> No
                                                Location Available</p>
                                            @endif
                                            <a href="{{ route('application.showVerifyForm', ['application' => $application->id]) }}"
                                                class="btn btn-primary">
                                                Verify Application
                                            </a>
                                            <form
                                                action="{{ route('application.schedule', ['application' => $application->id]) }}"
                                                method="POST" style="display:inline;">

                                                @if($application->status === 'pending')
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-primary">Schedule</button>
                                                @else
                                                <button type="submit" disabled
                                                    class="btn btn-primary">Scheduled</button>

                                                @endif
                                            </form>

                                            <!-- Check if the application is verified -->
                                            @if ($application->status === 'complete')
                                            <p class="">
                                                <i class="fa fa-check-circle text-success"></i> This application has
                                                already been verified.
                                            </p>


                                            @endif


                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column for Logo -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Logo</h5>
                                            <!-- Check if logo exists -->
                                            @if ($school && $school->logo)
                                            <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo"
                                                class="img-fluid" style="max-height: 200px; max-width: 100%;">
                                            @else
                                            <!-- Display an SVG placeholder if no logo exists -->
                                            <div class="text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"
                                                    fill="none" viewBox="0 0 100 100">
                                                    <circle cx="50" cy="50" r="45" stroke="gray" stroke-width="5"
                                                        fill="transparent" />
                                                    <text x="50%" y="50%" text-anchor="middle" stroke="gray"
                                                        stroke-width="1" dy=".3em" font-size="20">No Logo</text>
                                                </svg>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </section>
    </div>



</x-layout>