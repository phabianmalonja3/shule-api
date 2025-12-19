<div wire:poll.5s>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 col-4">Payment History</h5>

        <!-- Dropdown for Sorting -->
        <div class="dropdown col-4">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                <span>Sort by</span>
            </button>
            <div class="dropdown-menu">
               
                <a href="#" wire:click.prevent="sortBy('created_at', 'asc')" class="dropdown-item {{ $sortField === 'created_at' && $sortDirection === 'asc' ? 'active' : '' }}">
                    Created At (Older)
                </a>
                <a href="#" wire:click.prevent="sortBy('created_at', 'desc')" class="dropdown-item {{ $sortField === 'created_at' && $sortDirection === 'desc' ? 'active' : '' }}">
                    Created At (Newer)
                </a>
            </div>
        </div>


    
        <!-- Search Input -->
        <div class="card-header-action col-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search" wire:model.live="search">
                <div class="input-group-append">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Body -->
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Transaction Id</th>
                    <th>Parent</th>
                    <th>Student(s)</th>
                    <th>Amount</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $payment->transaction_id }}</td>
                        <td>{{ $payment->parent }}</td>
                        <td>
                            @foreach($payment->students as $student)
                                {{ $student->user->name }}@if (!$loop->last), @endif
                            @endforeach
                        </td>
                        <td>TZS{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->subscription_start }}</td>
                        <td>{{ $payment->subscription_end }}</td>
                        <td>
                            <span class="badge 
                                @if($payment->status === 'Paid') bg-success 
                                @elseif($payment->status === 'Pending') bg-warning 
                                @else bg-danger 
                                @endif">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                            <a href="#" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Receipt</a>

                                <a href="#" class="btn-sm sync-icon">
                                    <i class="fas fa-sync-alt  @if($isPolling) rotate @endif"></i> 
                                  </a>
                            
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No payment history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>
