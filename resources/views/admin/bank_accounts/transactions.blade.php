@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 mb-0 ">
                        <h5 class="mb-0">All Bank Transactions</h5>
                                        <button type="button" class="btn btn-primary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                                            data-bs-target="#addmodals">Add New Transactions</button>
                    </div>
                    <div class="card-body">
                        
                                        <div class="table-responsive">
                                            {{-- <table class="table" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Bank Name</th>
                                                        <th>Transaction Name</th>
                                                        <th>Transaction Id</th>
                                                        <th>Amount</th>
                                                        <th>Transaction Date</th>
                                                        <th>Transaction Type</th>
                                                        <th>Description</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="table-border-bottom-0">
                                                    @if($banktransactions->isNotEmpty())
                                                    @foreach ($banktransactions as $transaction )
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $transaction->bankAccount->bank_name ?? 'Bank has been deleted' }}</td>
                                                        <td>{{ $transaction->name }}</td>
                                                        <td>{{ $transaction->transaction_id ?? 'N/A' }}</td> <!-- ✅ Transaction ID -->
                                                        <td>{{ $transaction->amount ?? 'N/A' }}</td> <!-- ✅ Amount -->
                                                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M, Y') ?? 'N/A' }}</td> <!-- ✅ Income Date -->
                                                        <td>
                                                            @if($transaction->transaction_type == 'credit')
                                                                <span class="badge bg-label-success">জমা</span>
                                                            @elseif($transaction->transaction_type == 'debit')
                                                                <span class="badge bg-label-danger">উত্তোলন</span>
                                                            @else
                                                                <span class="badge bg-label-secondary">N/A</span>
                                                            @endif
                                                        </td> <!-- ✅ Transaction Type -->

                                                        <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                        
                                                        
                                                        <td>
                                                            <div class="d-flex align-items-center gap-1 cursor-pointer">
                                                                <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editModal{{ $transaction->id }}"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                                <form action="{{ route('banktransaction.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}">
                                                                        <i class="bx bx-trash me-1"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    @endforeach
                                                    @else
                                                    <tr>
                                                        <td colspan="7" class="text-center">No transaction found.</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table> --}}
                                            <table class="table" id="myTable">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Bank Name</th>
            <th>Transaction Name</th>
            <th>Transaction Id</th>
            <th>Amount</th>
            <th>Transaction Date</th>
            <th>Transaction Type</th>
            <th>Description</th>
            <th>Actions</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add New  Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="mb-3 d-none">
                            <label for="name" class="form-label">Transaction Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name"  required>
                           
                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug"  readonly>
                        </div>
                        <div class="mb-3">
                                <label for="add_income_category_id" class="form-label">Bank Account</label>
                                <select class="form-select category-select" id="add_income_category_id" name="bank_account_id" required>
                                    <option value="">Select Bank Account</option>
                                    @foreach ($bankaccounts as $bankaccount)
                                        <option value="{{ $bankaccount->id }}" >{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }}) </option>
                                    @endforeach
                                </select>
                            </div>
                        
                        <div class="mb-3">
                            <label for="add_income_sub_category_id" class="form-label">Transaction Type</label>
                            <select class="form-select subcategory-select" id="add_income_sub_category_id" name="transaction_type" required>
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
                            <input type="date" class="form-control" id="income_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="income_date" name="transaction_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control description-input" id="Description" name="description" rows="3"></textarea>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
                
            </div>  
        </div>
    </div>


    <!-- / Modal -->
      

    {{-- @if($banktransactions->isNotEmpty())
        @foreach ($banktransactions as $banktransaction )

        <div class="modal fade" id="editModal{{ $banktransaction->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Bank Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $banktransaction->id }}" action="{{ route('banktransaction.update', $banktransaction->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Transaction Name</label>
                                <input type="text" class="form-control name-input" id="name" name="name" value="{{ $banktransaction->name }}" required>
                               
                            </div>
                            <div class="mb-3 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="slug" name="slug" value="{{ $banktransaction->slug }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="add_income_category_id" class="form-label">Bank Account</label>
                                <select class="form-select category-select" id="add_income_category_id" name="bank_account_id" required>
                                    <option value="">Select Bank Account</option>
                                    @foreach ($bankaccounts as $bankaccount)
                                        <option value="{{ $bankaccount->id }}" {{ $banktransaction->bank_account_id == $bankaccount->id ? 'selected' : '' }}>{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="add_income_sub_category_id" class="form-label">Transaction Type</label>
                                <select class="form-select subcategory-select" id="add_income_sub_category_id" name="transaction_type" required>
                                    <option value="">Select Transaction Type</option>
                                    <option value="credit" {{ $banktransaction->transaction_type == 'credit' ? 'selected' : '' }}>জমা</option>
                                    <option value="debit" {{ $banktransaction->transaction_type == 'debit' ? 'selected' : '' }}>উত্তোলন</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" value="{{ $banktransaction->transaction_id }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ $banktransaction->amount }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_date" class="form-label">Income Date</label>
                                <input type="date" class="form-control" id="income_date" name="transaction_date" value="{{ $banktransaction->transaction_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="Description" class="form-label">Description</label>
                                <textarea class="form-control" id="Description" name="description" rows="3">{{ $banktransaction->description }}</textarea>
                                
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </form>
                    
                </div>  
            </div>
        </div>
        @endforeach
    @endif --}}

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
            <input type="text" class="form-control name-input" name="name" id="editBankName" required>
          </div>

          <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="bankslug" name="slug"  readonly>
                        </div>
          <div class="mb-3">
            <label class="form-label">Bank Account</label>
            <select class="form-select" name="bank_account_id" id="editBankAccount" required>
              <option value="">Select Bank</option>
              @foreach ($bankaccounts as $account)
                <option value="{{ $account->id }}">{{ $account->bank_name }} ({{ $account->account_type }})</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Transaction Type</label>
            <select class="form-select contact-select" name="transaction_type" id="editTransactionType" required>
              <option value="credit">জমা</option>
              <option value="debit">উত্তোলন</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Transaction ID</label>
            <input type="text" class="form-control" name="transaction_id" id="editTransactionID">
          </div>

          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" name="amount" id="editBankAmount" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Transaction Date</label>
            <input type="date" class="form-control" name="transaction_date" id="editBankDate" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control description-input" name="description" id="editBankDescription" rows="3"></textarea>
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
$(document).ready(function () {
    $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('banktransaction.index') }}",
        pageLength: 25,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        dom: 'Blfrtip',
        buttons: [
            {
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
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'bank_name', name: 'bankAccount.bank_name' },
            { data: 'name', name: 'name' },
            { data: 'transaction_id', name: 'transaction_id' },
            { data: 'amount', name: 'amount' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'transaction_type', name: 'transaction_type' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']]
    });
});
</script>
{{-- <script>
    $(document).ready(function () {
        
                $('#myTable}').DataTable({
                    pageLength: 20,
                    dom: 'Bfrtip',
                    buttons: [
                        {
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
                    ]
                });
            
    });
</script> --}}
@endif

<script>
    const banglaToEnglishMap = {
        'অ': 'a', 'আ': 'aa', 'ই': 'i', 'ঈ': 'ii', 'উ': 'u', 'ঊ': 'uu',
        'এ': 'e', 'ঐ': 'oi', 'ও': 'o', 'ঔ': 'ou',
        'ক': 'k', 'খ': 'kh', 'গ': 'g', 'ঘ': 'gh', 'ঙ': 'ng',
        'চ': 'ch', 'ছ': 'chh', 'জ': 'j', 'ঝ': 'jh', 'ঞ': 'n',
        'ট': 't', 'ঠ': 'th', 'ড': 'd', 'ঢ': 'dh', 'ণ': 'n',
        'ত': 't', 'থ': 'th', 'দ': 'd', 'ধ': 'dh', 'ন': 'n',
        'প': 'p', 'ফ': 'ph', 'ব': 'b', 'ভ': 'bh', 'ম': 'm',
        'য': 'j', 'র': 'r', 'ল': 'l', 'শ': 'sh', 'ষ': 'ss',
        'স': 's', 'হ': 'h', 'ড়': 'r', 'ঢ়': 'rh', 'য়': 'y',
        'ৎ': 't', 'ং': 'ng', 'ঃ': '', 'ঁ': ''
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
            .replace(/\s+/g, '_');    // replace spaces with "_"
    }

    // Attach to all modals with description-input class
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.description-input').forEach(function (descInput) {
            descInput.addEventListener('input', function () {
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
    $(document).ready(function () {

        $('form#addIncomeCategoryForms button[type="submit"]').on('click', function (e) {
            e.preventDefault();
           

            toastr.clear();

            let form = $('#addIncomeCategoryForms')[0]; // ✅ get the actual form element
            let formData = new FormData(form);          // ✅ pass the form here

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
                success: function (response) {
                    
                    $('#successMessage').text(response.message); // Set dynamic success message
                    $('#successModal').modal('show');

                    $('#addIncomeCategoryForms')[0].reset();
                    $('#addmodals').modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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


{{-- <script>
    $(document).ready(function () {
        $('form[id^="editIncomeCategoryForms"] button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            toastr.clear();

            let form = $(this).closest('form')[0]; // get the closest form to the clicked button
            let formData = new FormData(form);

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            $.ajax({
                url: form.action,
                method: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');

                    form.reset();
                    $('#editModal' + response.id).modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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

</script> --}}

<script>
$(document).ready(function () {
    // Show Edit Modal with data
    $(document).on('click', '.openBankEditModal', function () {
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

        $('#editBankTransactionModal').modal('show');
    });

    // Optional: AJAX submission (if you want to avoid reload)
    $('#editBankTransactionForm').on('submit', function (e) {
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
            success: function (response) {
                $('#successMessage').text(response.message);
                $('#successModal').modal('show');
                $('#editBankTransactionModal').modal('hide');
                setTimeout(function () {
                        window.location.reload();
                    }, 2000);
            },
            error: function (xhr) {
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
    $(document).on('click', '.delete-confirm', function (e) {
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
    
    
{{-- <script>
    $(document).ready(function () {
        $('form[id^="addIncomeCategoryForms"] button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            toastr.clear();

            let form = $(this).closest('form')[0]; // get the closest form to the clicked button
            let formData = new FormData(form);

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
                success: function (response) {
                    
                    $('#successMessage').text(response.message); // Set dynamic success message
                    $('#successModal').modal('show');

                    form.reset();
                    $('#addmodals' + response.id).modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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

</script> --}}


<script>
    $(document).on('change', '.contact-select', function () {
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
