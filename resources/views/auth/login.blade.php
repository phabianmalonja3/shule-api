<x-layout title="Dashboard">

    <x-slot:title>
        Login Page
    </x-slot:title>
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Login</h4>
                        </div>
                        <livewire:login-form />
                    </div>
                    <div class="mt-5 text-center text-muted">
                        Don't have an account? <a href="{{route('register.school')}}">Register School  </a> or as a <a href="{{ route('parents.create') }}"> Parent</a><br />
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>