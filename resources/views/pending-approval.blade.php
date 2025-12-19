<x-layout>
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <!-- Logo Section -->
        <div class="mb-4">
            <img src="{{ asset('/logo/skuliApp-logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 150px;">
        </div>

        <!-- Message Section -->
        <div class="p-4 text-center shadow-lg card">
            <h2 class="mb-4">Application Submitted!</h2>
            <p class="lead">
                Thank you for registering your school with us. Your application has been submitted successfully and is currently pending approval from our admin team.
            </p>
            <p>Please check your email for updates. You will be notified once your account is approved. In case of any query please contact us through 0760400200</p>
            
           

            <!-- Optional Button to Go Back to Homepage -->
            <a href="{{ route('home') }}" class="mt-4 text-center btn btn-primary">Return to </a>
        </div>
    </div>
</x-layout>
