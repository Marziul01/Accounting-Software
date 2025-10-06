@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 mb-0 ">
                <h5 class="mb-0">All Bank Transactions</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#addmodals">Add New Transactions</button>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th style="width: 150px !important; text-wrap: nowrap;">Bank Name</th>
                                <th style="width: 200px !important; text-wrap: nowrap;">TRANS. Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Amount</th>
                                <th style="text-wrap: nowrap;">TRANS. DATE</th>
                                <th style="text-wrap: nowrap;">TRANS.Type</th>
                                <th style="width: 200px !important;text-wrap: nowrap;">Description &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th style="text-wrap: nowrap;">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3 d-none">
                            <label for="name" class="form-label">Transaction Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name" required>

                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="add_income_category_id" class="form-label">Bank Account</label>
                            <select class="form-select category-select" id="add_income_category_id" name="bank_account_id"
                                required>
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }}
                                        ({{ $bankaccount->account_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="add_income_sub_category_id" class="form-label">Transaction Type</label>
                            <select class="form-select subcategory-select" id="add_income_sub_category_id"
                                name="transaction_type" required>
                                <option value="">Select Transaction Type</option>
                                <option value="credit">জমা</option>
                                <option value="debit">উত্তোলন</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Transaction Date</label>
                            <input type="date" class="form-control myDate" id="income_date" name="transaction_date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="income_date" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="income_date" name="transaction_id" required>
                        </div> --}}
                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control description-input" id="Description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="transfer_from" class="form-label">Transfer From (optional)</label>
                            <select class="form-select" id="transfer_from" name="transfer_from">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="transfer_to" class="form-label">Transfer To (optional)</label>
                            <select class="form-select" id="transfer_to" name="transfer_to">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Edit Bank Transaction Modal -->
    <div class="modal fade" id="editBankTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editBankTransactionForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Bank Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="transaction_id_hidden" id="editTransactionIdHidden">

                        <div class="mb-3 d-none">
                            <label class="form-label">Transaction Name</label>
                            <input type="text" class="form-control name-input" name="name" id="editBankName"
                                required>
                        </div>

                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="bankslug" name="slug"
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bank Account</label>
                            <select class="form-select" name="bank_account_id" id="editBankAccount" required>
                                <option value="">Select Bank</option>
                                @foreach ($bankaccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->bank_name }}
                                        ({{ $account->account_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select contact-select" name="transaction_type" id="editTransactionType"
                                required>
                                <option value="credit">জমা</option>
                                <option value="debit">উত্তোলন</option>
                            </select>
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" id="editTransactionID">
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" id="editBankAmount" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control myDate" name="transaction_date" id="editBankDate"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control description-input" name="description" id="editBankDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_from" class="form-label">Transfer From (optional)</label>
                            <select class="form-select" id="edittransfer_from" name="transfer_from">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_to" class="form-label">Transfer To (optional)</label>
                            <select class="form-select" id="edittransfer_to" name="transfer_to">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- / Modal -->


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
@endsection


@section('scripts')
    @if ($banktransactions->isNotEmpty())
        <script>
            let columnDefsConfig = [];

            // Apply only on desktop (for example, width > 768px)
            if (window.innerWidth > 768) {
                columnDefsConfig.push({
                    targets: 2, // "name" column
                    createdCell: function (td) {
                        $(td).css({
                            "white-space": "normal", // allows wrapping
                            "word-break": "break-word" // ensures long words also break
                        });
                    }
                });
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#myTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('banktransaction.index') }}",
                    pageLength: 25,
                    lengthMenu: [
                        [25, 50, 100],
                        [25, 50, 100]
                    ],
                    dom: 'Blfrtip',
                    buttons: [{
                            extend: 'csv',
                            text: 'Export CSV',
                            className: 'btn btn-sm my-custom-table-btn',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print Table',
                            className: 'btn btn-sm my-custom-table-btn',
                            exportOptions: {
                                columns: ':not(:last-child)'
                            }
                        }
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'bank_name',
                            name: 'bankAccount.bank_name'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'amount',
                            name: 'amount'
                        },
                        {
                            data: 'transaction_date',
                            name: 'transaction_date'
                        },
                        {
                            data: 'transaction_type',
                            name: 'transaction_type'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: columnDefsConfig,
                    language: {
                        processing: '<div class="loader-custom-wrapper"><div class="loader-custom1"></div></div>'
                    }
                });
            });
        </script>
    @endif

    <script>
        const banglaToEnglishMap = {
            'অ': 'a',
            'আ': 'aa',
            'ই': 'i',
            'ঈ': 'ii',
            'উ': 'u',
            'ঊ': 'uu',
            'এ': 'e',
            'ঐ': 'oi',
            'ও': 'o',
            'ঔ': 'ou',
            'ক': 'k',
            'খ': 'kh',
            'গ': 'g',
            'ঘ': 'gh',
            'ঙ': 'ng',
            'চ': 'ch',
            'ছ': 'chh',
            'জ': 'j',
            'ঝ': 'jh',
            'ঞ': 'n',
            'ট': 't',
            'ঠ': 'th',
            'ড': 'd',
            'ঢ': 'dh',
            'ণ': 'n',
            'ত': 't',
            'থ': 'th',
            'দ': 'd',
            'ধ': 'dh',
            'ন': 'n',
            'প': 'p',
            'ফ': 'ph',
            'ব': 'b',
            'ভ': 'bh',
            'ম': 'm',
            'য': 'j',
            'র': 'r',
            'ল': 'l',
            'শ': 'sh',
            'ষ': 'ss',
            'স': 's',
            'হ': 'h',
            'ড়': 'r',
            'ঢ়': 'rh',
            'য়': 'y',
            'ৎ': 't',
            'ং': 'ng',
            'ঃ': '',
            'ঁ': ''
        };

        function transliterate(text) {
            return text.split('').map(char => banglaToEnglishMap[char] || char).join('');
        }

        function generateSlug(text) {
            const englishText = transliterate(text);
            return englishText
                .toLowerCase()
                .replace(/[^\w\s]/gi, '') // remove special characters
                .trim()
                .replace(/\s+/g, '_'); // replace spaces with "_"
        }

        // Attach to all modals with description-input class
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.description-input').forEach(function(descInput) {
                descInput.addEventListener('input', function() {
                    const $form = this.closest('form');
                    const nameInput = $form.querySelector('.name-input');
                    const slugInput = $form.querySelector('.slug-output');

                    const value = this.value.trim();

                    if (nameInput) {
                        nameInput.value = value;
                    }

                    if (slugInput) {
                        slugInput.value = generateSlug(value);
                    }
                });
            });
        });
    </script>



    <script>
        $(document).ready(function() {

            $('form#addIncomeCategoryForms button[type="submit"]').on('click', function(e) {
                e.preventDefault();


                toastr.clear();

                let form = $('#addIncomeCategoryForms')[0]; // ✅ get the actual form element
                let formData = new FormData(form); // ✅ pass the form here

                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                $.ajax({
                    url: "{{ route('banktransaction.store') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        $('#successMessage').text(response
                            .message); // Set dynamic success message
                        $('#successModal').modal('show');

                        $('#addIncomeCategoryForms')[0].reset();
                        $('#addmodals').modal('hide');

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Show Edit Modal with data
            $(document).on('click', '.openBankEditModal', function() {
                $('#fullscreenLoader').fadeIn();
                const button = $(this);
                const id = button.data('id');

                const actionUrl = "{{ route('banktransaction.update', ':id') }}".replace(':id', id);
                $('#editBankTransactionForm').attr('action', actionUrl);
                $('#editTransactionIdHidden').val(id);
                $('#editBankName').val(button.data('name'));
                $('#bankslug').val(button.data('slug'));
                $('#editTransactionID').val(button.data('transaction-id'));
                $('#editBankAmount').val(button.data('amount'));
                $('#editBankDate').val(button.data('date'));
                $('#editBankDescription').val(button.data('description'));
                $('#editBankAccount').val(button.data('bank-id'));
                $('#editTransactionType').val(button.data('type'));
                $('#editTransactionType').val(button.data('type'));
                $('#fullscreenLoader').fadeOut();
                $('#editBankTransactionModal').modal('show');
                $('#edittransfer_from').val(button.data('transfared-from'));
                $('#edittransfer_to').val(button.data('transfared-to'));
            });

            // Optional: AJAX submission (if you want to avoid reload)
            $('#editBankTransactionForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this)[0];
                const formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
                        $('#editBankTransactionModal').modal('hide');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error("Something went wrong.");
                        }
                    }
                });
            });
        });
    </script>


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
        $(document).on('change', '.contact-select', function() {
            // Get selected subcategory name
            let subcategoryName = $(this).find('option:selected').text();

            // Get closest form
            let $form = $(this).closest('form');

            // Fill in name field
            $form.find('.name-input').val(subcategoryName);

            // If generateSlug function is defined globally, use it
            if (typeof generateSlug === 'function') {
                const slug = generateSlug(subcategoryName);
                $form.find('.slug-output').val(slug);
            }
        });
    </script>
@endsection
