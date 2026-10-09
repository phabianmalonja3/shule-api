<x-layout>
    <x-slot:title>
        Admission Officer's Panel
    </x-slot:title>

    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <x-navbar />
        <x-admin.sidebar />

        <div class="main-content">
            <section class="section">
                


                <!-- Applications Table / Component -->
                <div class="mt-4">
                    <livewire:admission.application-list />
                </div>

            </section>
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal"
            aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    <form class="">
                        <div class="form-group">
                        <label>Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </div>
                            </div>
                            <input type="text" class="form-control" placeholder="Email" name="email">
                        </div>
                        </div>
                        <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </div>
                            </div>
                            <input type="password" class="form-control" placeholder="Password" name="password">
                        </div>
                        </div>
                        <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="remember" class="custom-control-input" id="remember-me">
                            <label class="custom-control-label" for="remember-me">Remember Me</label>
                        </div>
                        </div>
                        <button type="button" class="btn btn-primary m-t-15 waves-effect">LOGIN</button>
                    </form>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    </div>
   {{-- <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> --}} 
</x-layout>