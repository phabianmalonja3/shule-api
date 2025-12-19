<x-layout title="Subscription Status">
    <x-slot:title>
        Subscription Status
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar :school="$school" />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">{{ __('Parent Dashboard') }}</div>
                                <div class="container">
                                    <h1>Your Subscriptions</h1>

                                    @if ($subscriptions->isEmpty())
                                        <div class="alert alert-warning">
                                            You have no active subscriptions. <a
                                                href="{{ route('payment.page') }}">Subscribe now</a>.
                                        </div>
                                    @else
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Transaction Id</th>
                                                    <th>Subscription Start</th>
                                                    <th>Subscription End</th>
                                                    <th>Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Students</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($subscriptions as $key=>$subscription)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $subscription->transaction_id }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($subscription->subscription_start)->format('d-m-Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($subscription->subscription_end)->format('d-m-Y') }}</td>
                                                        <td>Tsh {{ number_format($subscription->amount, 2) }}</td>
                                                        <td>{{ ucfirst($subscription->method) }}</td>
                                                       
                                                        <td>
                                                            @foreach ($subscription->students as $student)
                                                                <span class="badge bg-info">{{ $student->user->name }}</span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @if ($subscription->status == 'Paid')
                                                                <span class="badge bg-success">Paid</span>
                                                            @elseif($subscription->status == 'Pending')
                                                                <span class="badge bg-warning">Pending</span>
                                                            @else
                                                                <span class="badge bg-danger">Failed</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($subscription->status == 'Pending' || $subscription->status == 'Failed')
                                                                <a href="{{ route('payment.page') }}" class="btn btn-primary">Renew Subscription</a>
                                                            @else
                                                                <span class="badge bg-secondary">Active</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
