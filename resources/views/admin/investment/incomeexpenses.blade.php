@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">All {{ $investment->name }} Gain/Loss</h5>

            </div>
            <div class="card-body  text-nowrap">
                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Gain/Loss</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if ($records->isNotEmpty())
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $record->category_id == '13' ? 'Gain' : 'Loss' }}</td>

                                        <td>{{ $record->description }}</td>
                                        <td>{{ $record->amount ?? 'N/A' }}</td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($record->date)->format('d M, Y') ?? 'N/A' }}</td>
                                        <!-- ✅ Income Date -->
                                        </td>
                                        <td>



                                            <div class="d-flex gap-1 cursor-pointer">
                                                @if ($record->category_id == '13')
                                                    <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target="#incomeModal{{ $record->id }}"><i
                                                            class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <form action="{{ route('investment-income.destroy', $record->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf

                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                                                        href="#" data-bs-toggle="modal"
                                                        data-bs-target="#expenseModal{{ $record->id }}"><i
                                                            class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <form action="{{ route('investment-expense.destroy', $record->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf

                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No records found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    @if ($records->isNotEmpty())
        @foreach ($records->where('category_id', '13') as $record)
            <div class="modal fade" id="incomeModal{{ $record->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Investments
                                {{ $record->category_id == '13' ? 'Gain' : 'Loss' }} </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="investmentIncomeUpdateForm{{ $record->id }}">
                            @csrf

                            <div class="modal-body">
                                <input type="hidden" name="investment_id" value="{{ $record->investment_id }}">
                                <input type="hidden" name="category_id" value="{{ $record->category_id }}">
                                <input type="hidden" name="subcategory_id" value="{{ $record->subcategory_id }}">
                                <div class="mb-3">
                                    <label for="" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="" name="amount"
                                        value="{{ $record->amount }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Income Date</label>
                                    <input type="date" class="form-control myDate" id="date" name="date"
                                        value="{{ $record->date }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Description" class="form-label">Description</label>
                                    <textarea class="form-control" id="Description" name="description" rows="3">{{ $record->description }}</textarea>
                                </div>

                                @php
                                    $currentTransaction = $bankTransaction
                                        ->where('from_id', $record->id)
                                        ->first();
                                @endphp


                                <div class="mb-3">
                                    <label for="bank_account_id" class="form-label">Select Bank Account (Optional)</label>
                                    <select class="form-select category-select" id="bank_account_id" name="bank_account_id">
                                        <option value="">Select Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ $currentTransaction && $currentTransaction->bank_account_id == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bank Description (Optional)</label>
                                    <textarea class="form-control" name="bank_description" rows="3">{{ $currentTransaction ? $currentTransaction->description : '' }}</textarea>
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
    @endif

    @if ($records->isNotEmpty())
        @foreach ($records->where('category_id', '7') as $record)
            <div class="modal fade" id="expenseModal{{ $record->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Investments
                                {{ $record->category_id == '13' ? 'Gain' : 'Loss' }} </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="investmentexpenseUpdateForm{{ $record->id }}">
                            @csrf

                            <div class="modal-body">
                                <input type="hidden" name="investment_id" value="{{ $record->investment_id }}">
                                <input type="hidden" name="category_id" value="{{ $record->category_id }}">
                                <input type="hidden" name="subcategory_id" value="{{ $record->subcategory_id }}">
                                <div class="mb-3">
                                    <label for="" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="" name="amount"
                                        value="{{ $record->amount }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Expense Date</label>
                                    <input type="date" class="form-control myDate" id="date" name="date"
                                        value="{{ $record->date }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="Description" class="form-label">Description</label>
                                    <textarea class="form-control" id="Description" name="description" rows="3">{{ $record->description }}</textarea>

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
    @endif


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
    @if ($records->isNotEmpty())
        <script>
            $('#myTable').DataTable({
                pageLength: 20,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        text: 'Export CSV',
                        className: 'btn btn-sm my-custom-table-btn',
                        exportOptions: {
                            columns: ':not(:last-child)' // exclude the last column
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print Table',
                        className: 'btn btn-sm my-custom-table-btn',
                        exportOptions: {
                            columns: ':not(:last-child)' // exclude the last column
                        }
                    }
                ]
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
        $(function() {
            $('form[id^="investmentIncomeUpdateForm"]').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const formId = $form.attr('id');
                const id = formId.replace('investmentIncomeUpdateForm', '');
                const $btn = $form.find('button[type=submit]');
                const formData = $form.serialize();

                $btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('investment-income.update', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        toastr.success(res.message);
                        $form.closest('.modal').modal('hide');

                        $('#successMessage').text(res.message);
                        $('#successModal').modal('show');

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Update failed.');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>


    <script>
        $(function() {
            $('form[id^="investmentexpenseUpdateForm"]').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const formId = $form.attr('id');
                const id = formId.replace('investmentexpenseUpdateForm', '');
                const $btn = $form.find('button[type=submit]');
                const formData = $form.serialize();

                $btn.prop('disabled', true);

                $.ajax({
                    url: '{{ route('investment-expense.update', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        toastr.success(res.message);
                        $form.closest('.modal').modal('hide');

                        $('#successMessage').text(res.message);
                        $('#successModal').modal('show');

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Update failed.');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
