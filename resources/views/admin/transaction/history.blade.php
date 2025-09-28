@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 mb-0 pb-0">
                <h5 class="mb-0">All Transactions History</h5>
            </div>
            <div class="card-header d-flex flex-column justify-content-between align-items-start border-bottom-1 gap-2 ">
                <!-- Date Range -->
                <div class="d-flex align-items-start justify-content-between gap-2 flex-column flex-md-row align-items-md-end mobile-reports-filter w-100">
                    <div class="d-flex gap-2 align-items-md-end mobile-reports-filter ">
                        <div class="form-group mobile-reports-filter-group">
                            <label class="form-label" for="start_date">Select Start Date:</label>
                            <input type="date" id="startDate" class="form-control form-control-sm myDate">
                        </div>

                        <div class="form-group mobile-reports-filter-group">
                            <label class="form-label" for="end_date">Select End Date:</label>
                            <input type="date" id="endDate" class="form-control form-control-sm myDate">
                        </div>
                    </div>
                    
                    <div class="mobile-reports-filter-btns">
                        <button class="btn btn-outline-success" id="dateFilterBtn">Filter by Date</button>
                        <button class="btn btn-outline-secondary quick-filter" data-period="today">Today</button>
                        <button class="btn btn-outline-secondary quick-filter" data-period="month">This Month</button>
                        <button class="btn btn-outline-secondary quick-filter" data-period="year">This Year</button>
                    </div>                    
                </div>

                <!-- Quick Filters -->
                {{-- <div class="d-flex mt-2 gap-2 align-items-md-start justify-content-md-start form-group mobile-reports-filter-btns">
                    <button class="btn btn-outline-success" id="dateFilterBtn">Filter by Date</button>
                    <button class="btn btn-outline-secondary quick-filter" data-period="today">Today</button>
                    <button class="btn btn-outline-secondary quick-filter" data-period="month">This Month</button>
                    <button class="btn btn-outline-secondary quick-filter" data-period="year">This Year</button>
                </div> --}}

                <div id="transactionTypeFilters" class="d-flex mt-2 gap-2 align-items-md-start justify-content-md-between form-group mobile-reports-filter-btns">
                    <button class="btn btn-primary filter-btn" data-type="">All</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="Investment">Investment Transaction</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="Income">Income Transaction</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="Expense">Expense Transaction</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="Asset">Asset Transaction</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="Liability">Liability Transaction</button>
                    <button class="btn btn-outline-primary filter-btn" data-type="BankTransaction">Bank Transaction</button>
                </div>
            </div>
        </div>
        
        <div class="card ">
            <div class="card-header d-flex flex-column justify-content-between align-items-center border-bottom-1">
            </div>
            <div class="card-body  text-nowrap">
                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Transaction of</th>
                                <th>Name</th>
                                <th>TRNS. TYPE</th>
                                
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center flex-column align-items-center auth-success-modal">
                    <img src="{{ asset('admin-assets/img/double-check.gif') }}" width="25%" alt="">
                    <h5 class="modal-title text-center" id="successModalLabel">Success</h5>
                    <p id="successMessage" class="text-center">Login successful!</p>
                </div>
            </div>
        </div>
    </div>

    
    <div id="ModalContainer"></div>
@endsection


@section('scripts')
    @if ($transactions->isNotEmpty())
        <script>
            $(document).ready(function() {
                let selectedType = "";
                let startDate = "";
                let endDate = "";
                let period = "";

                var table = $('#myTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('transaction.history') }}",
                        data: function (d) {
                            d.type = selectedType;
                            d.startDate = startDate;
                            d.endDate = endDate;
                            d.period = period;
                        }
                    },
                    pageLength: 25,
                    lengthMenu: [[25, 50, 100],[25, 50, 100]],
                    dom: 'Blfrtip',
                    buttons: [
                        {
                            extend: 'csv',
                            text: 'Export CSV',
                            className: 'btn btn-sm my-custom-table-btn',
                            exportOptions: { columns: ':not(:last-child)' }
                        },
                        {
                            extend: 'print',
                            text: 'Print Table',
                            className: 'btn btn-sm my-custom-table-btn',
                            exportOptions: { columns: ':not(:last-child)' }
                        }
                    ],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'type', name: 'type' },
                        { data: 'name', name: 'name' },
                        { data: 'transaction_type', name: 'transaction_type' },
                        { data: 'amount', name: 'amount' },
                        { data: 'date', name: 'date' },
                        { data: 'description', name: 'description' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // Transaction Type filter
                $('#transactionTypeFilters').on('click', '.filter-btn', function () {
                    selectedType = $(this).data('type');
                    $('.filter-btn').removeClass('btn-primary').addClass('btn-outline-primary');
                    $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                    table.ajax.reload();
                });

                // Date Range filter
                $('#dateFilterBtn').on('click', function () {
                    startDate = $('#startDate').val();
                    endDate = $('#endDate').val();
                    period = ""; // reset quick filter
                    table.ajax.reload();
                });

                // Quick Period filters
                $('.quick-filter').on('click', function () {
                    period = $(this).data('period'); // today | month | year
                    startDate = "";
                    endDate = "";
                    $('.quick-filter').removeClass('btn-secondary').addClass('btn-outline-secondary');
                    $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');
                    table.ajax.reload();
                });
            });
        </script>
    @endif
    <script>
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modalContainer = document.getElementById("ModalContainer");

    // Delegate event for dynamically created buttons
    document.addEventListener("click", function (e) {
        if (e.target.closest("button[data-url]")) {
            const button = e.target.closest("button[data-url]");
            const url = button.dataset.url;

            // Step 1: Remove old scripts
            const oldScripts = modalContainer.querySelectorAll("script");
            oldScripts.forEach(script => script.remove());

            // Step 2: Clear the container
            modalContainer.innerHTML = "";

            // Step 3: Fetch the modal content
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    modalContainer.innerHTML = html;
                    
                    // Step 4: Run new scripts inside modal
                    const newScripts = modalContainer.querySelectorAll("script");
                    newScripts.forEach(oldScript => {
                        const newScript = document.createElement("script");
                        newScript.type = oldScript.type || "text/javascript";

                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                        } else {
                            newScript.textContent = oldScript.textContent;
                        }

                        modalContainer.appendChild(newScript);
                        oldScript.remove();
                    });

                    // Step 5: Show the modal
                    const modal = modalContainer.querySelector(".modal");
                    if (modal) {
                        const bsModal = new bootstrap.Modal(modal);
                        bsModal.show();

                        attachSlugListener(modal);
                        if (modal.querySelector('#investmentSelect')) {
                            attachInvestmentSubcategoryHandler(modal);
                        }
                    }
                })
                .catch(error => {
                    console.error("Error loading modal:", error);
                    modalContainer.innerHTML = `<div class="text-danger">Failed to load content.</div>`;
                });
        }
    });
});
</script>

@endsection
