<x-layout>
    <x-slot:title>
        Application Success Page
    </x-slot:title>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="text-center card-header">
                        
                    </div>
                    <div class="text-center card-body">
                        <h3 class="text-success">Thank you for registering with us!</h3>
                        <p>Our team will review your application and get back to you within three (3) working days. Your login credentials (i.e. username and password) will be sent to you afterwards through an SMS. </p>
                       

                        {{-- <p class="mt-3">Asante kwa kutuma ombi la kusajili shule yako! Ombi lako limepokelewa. Timu yetu italifanyia kazi na kukupa mrejesho ndani ya siku tatu (3) za kazi. </p> --}}
                       

                        {{-- <img src="{{ asset('02-lottie-tick-01-instant-2.gif') }}" alt="Success" width="300" class="mt-4"> --}}
                       
                    </div>
                    <div class="text-center card-footer">
                        Go back to <a href="/">homepage</a>.
                        {{-- Go back to <a href="/">homepage</a> or <a href="{{ route('login') }}">Login</a> if you have an account. --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
