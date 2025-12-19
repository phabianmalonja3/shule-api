<x-layout>
    <x-slot:title>
        Password Forgot Page
    </x-slot:title>
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Forgot Password</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">We will send a link to reset your password</p>

                            <!-- Display Success or Error Messages -->
                            @if(session('status'))
                            <div class="text-success">
                                {{ session('status') }}
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="text-danger">
                                {{ $errors->first('email') }}
                            </div>
                            @endif

                            <!-- Forgot Password Form -->
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" class="form-control" name="email" required autofocus
                                        value="">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        Send Password Reset Link
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>