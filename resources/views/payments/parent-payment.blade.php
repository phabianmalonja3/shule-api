<x-layout>
    <x-slot:title>
        Parent Payments
        </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Payments for Subscription</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Transaction ID</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>TXN12345</td>
                                            <td>$50</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="openModal('TXN12345', '$50', 'Completed')">Details</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>TXN67890</td>
                                            <td>$30</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="openModal('TXN67890', '$30', 'Pending')">Details</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Sliding Modal with Backdrop -->
    <div id="modalBackdrop" class="modal-backdrop" onclick="closeModal()"></div>
    
    <div id="detailsModal" class="modal-overlay">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <h4>Payment Details</h4>
            <p><strong>Transaction ID:</strong> <span id="modalTransactionId"></span></p>
            <p><strong>Amount:</strong> <span id="modalAmount"></span></p>
            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        </div>
    </div>

    <style>
        /* Backdrop Overlay */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s;
            z-index: 1000;
        }

        /* Sliding Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            right: -400px;
            width: 300px;
            height: 100%;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            transition: right 0.3s ease-in-out;
            z-index: 1001;
        }

        /* When active */
        .modal-overlay.active {
            right: 0;
        }
        .modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Close Button */
        .close-btn {
            cursor: pointer;
            font-size: 27px;
            position: absolute;
            top: 10px;
            right: 15px;
        }

        /* Modal Content */
        .modal-content {
            margin-top: 40px;
            box-shadow:none;
        }
    </style>

    <script>
        function openModal(transactionId, amount, status) {
            document.getElementById('modalTransactionId').innerText = transactionId;
            document.getElementById('modalAmount').innerText = amount;
            document.getElementById('modalStatus').innerText = status;

            document.getElementById('detailsModal').classList.add('active');
            document.getElementById('modalBackdrop').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('detailsModal').classList.remove('active');
            document.getElementById('modalBackdrop').classList.remove('active');
        }
    </script>
</x-layout>
