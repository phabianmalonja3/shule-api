<x-layout>
    <x-slot:title>
        Payment List
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="p-3 card">
                            <div class="mb-3 form-group">
                                <h2 class="mb-4">Subscription Payment</h2>

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

                                {{-- <div class="card"> --}}
                                   <livewire:payment-history />
                                </div>
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
        </section>
    </div>
</x-layout>

