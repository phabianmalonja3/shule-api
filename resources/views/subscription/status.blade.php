

<x-layout title="Subscription Status">
    <x-slot:title>
        Subscription Status
    </x-slot:title>

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">{{ __('Subscription status') }}</div>
                                <div class="card-header"><a href="{{ route('parents.index') }}" class="btn btn-success">Back</a></div>

                                <div class="card-body">
                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

        <h4>Your subscription status</h4>
        <p>Hello! your subscription has should be your annual subscription has expired</p>
        <a href="{{ route('subscription.create') }}" class="btn btn-primary">Renew Now</a>
    </div>
                            </div></div></div></div></div></section></div>
</x-layout>
