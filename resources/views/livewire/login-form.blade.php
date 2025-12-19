<!-- resources/views/livewire/login-component.blade.php -->

    <div class="card-body">
        <!-- Display Login State -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="phone">Phone Number or Registration Number</label>
                <input id="phone" type="text" class="form-control" wire:model="phone"
                    placeholder="Phone Number or Registration Number" required autofocus>

                @error('phone')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label">Password</label>
                    <div class="float-right">
                        <a href="{{ route('password-forgot') }}" class="text-small">
                            Forgot Password?
                        </a>
                    </div>
                </div>
                <input id="password" type="password" class="form-control" wire:model="password" required>
                @error('password')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" wire:model="remember" class="custom-control-input" id="remember-me">
                    <label class="custom-control-label" for="remember-me">Remember Me</label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        Login
                    </span>
                    <span wire:loading>
                        <i class="fa fa-spinner fa-spin"></i> Loading...
                    </span>
                </button>
            </div>
        </form>
    </div>

