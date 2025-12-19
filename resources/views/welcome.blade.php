<x-layout>
    <!-- Logo Section -->
    <section class="container mt-4 d-flex flex-column flex-md-row justify-content-between">
        <!-- Logo in the Center -->
        <div class="mb-3 text-center mb-md-0">
            <img src="{{ asset('/logo/skuliApp-logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 100px;">
        </div>

        <!-- Support Number and User Manual Button -->
        <div class="text-center text-md-right">
            <p class="mb-1"><strong>Support Number:</strong> +255 (0) 760 400 200</p>
           
            <a href="{{ route('user.manual') }}" class="btn btn-outline-primary btn-sm" 
            >Download User Manual</a>
          
          
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="container">
        <div class="row align-items-center">
            <!-- Left Column: Welcome Message and Buttons -->
            <div class="col-md-6">
                <h1 class="display-6">Welcome to <span style="color:#2185FF">Shule</span>MIS</h1>
                <p class="lead" style="margin-bottom:100px">
                    <span style="color:#2185FF">Shule</span>MIS is a collaborative teaching and learning digital platform, enabling parents and teachers to
                    remotely work together to improve pupils' learning experience. The ultimate goal is to make teaching and
                    learning process more fun and subsequently increase pupils'
                    performance, teachers' productivity, and parents' participation.
                </p>
                <div class="mt-1">

                    @auth
                    @role('administrator')
                    <a href="{{ url()->previous() }}" class="mr-2 btn btn-primary btn-lg">Go Back</a>
                    @endrole
@if(auth()->user()->hasAnyRole(['header teacher','academic teacher']))
<a href="{{ url()->previous() }}" class="mr-2 btn btn-primary btn-lg">Go Back</a>
@endif
@if(auth()->user()->hasAnyRole(['class teacher','teacher']))
<a href="{{ url()->previous() }}" class="mr-2 btn btn-primary btn-lg">Go Back</a>
@endif
@role('parent')
<a href="{{ url()->previous() }}" class="mr-2 btn btn-primary btn-lg">Go Back</a>
@endrole

                    @endauth
                    @guest
                    <div class="container mt-4 text-center" style="padding:0">
                        <div class="gap-2 d-flex flex-column flex-sm-row justify-content-center align-items-center">
                            <a href="{{ route('login') }}" class="mb-2 mr-1 btn btn-primary btn-sm flex-fill" style="background-color:#2185FF">Login</a>
                            <a href="{{ route('register.school') }}" class="mb-2 mr-1 btn btn-outline-primary btn-sm flex-fill">Register a School</a>
                            <a href="{{ route('parents.create') }}" class="mb-2 mr-1 btn btn-outline-success btn-sm flex-fill">Register as a Parent</a>
                            <a href="{{ route('online.application') }}" class="mb-2 btn btn-outline-info btn-sm flex-fill">Online Application</a>
                        </div>
                    </div>
@endguest                    
                </div>
            </div>

            <!-- Right Column: Banner Image -->
            <div class="text-center col-md-6">
                <img src="{{ asset('assets/img/banner/home-B4Z11-Yd.png') }}" alt="School Banner" class="rounded img-fluid" width="80%">
            </div>
        </div>
    </section>
</x-layout>
