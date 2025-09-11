@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Schedule Bank Transfer</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#addmodals">Add New Schedule Transfer </button>
            </div>
            <div class="card-body position-relative ">
                <div class="table-responsive text-nowrap">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Transfering From</th>
                                <th>Transfering To</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Star Date</th>
                                <th>End Date/Continue</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if ($schedules->isNotEmpty())
                                @foreach ($schedules as $schedule)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $schedule->bankAccount->bank_name ?? 'N/A' }} -
                                            ({{ $schedule->bankAccount->account_type ?? 'N/A' }})</td>
                                        <td>{{ $schedule->toBankAccount->bank_name ?? 'N/A' }} -
                                            ({{ $schedule->toBankAccount->account_type ?? 'N/A' }})</td>
                                        <td>{{ number_format($schedule->amount, 2) }}</td>
                                        <td>{{ $schedule->description }}</td>
                                        <td>{{ $schedule->start_date }}</td>
                                        <td>{{ $schedule->end_date ?? 'Continue' }}</td>
                                        <td>
                                            <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}"
                                                href="" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $schedule->id }}"><i
                                                    class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            <form action="{{ route('bankschedule.destroy', $schedule->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}"><i
                                                        class="bx bx-trash me-1"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">No Schedule found.</td>
                                </tr>
                            @endif
                        </tbody>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add New Schedule Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bank_account_id" class="form-label">Transfering From</label>
                            <select class="form-select" id="bank_account_id" name="from" required>
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}-
                                        ({{ $bank->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="to_bank_account_id" class="form-label">Transfering To</label>
                            <select class="form-select" id="to_bank_account_id" name="to" required>
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}-
                                        ({{ $bank->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control myDate" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control myDate" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="infinite" name="infinite" value="1">
                            <label class="form-check-label" for="infinite">Continue Indefinitely</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>

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

    @if ($schedules->isNotEmpty())
        @foreach ($schedules as $schedule)
            <div class="modal fade" id="editModal{{ $schedule->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"> Update Schedule Transfer Details </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form id="editIncomeCategoryForms{{ $schedule->id }}" action="{{ route('bankschedule.update', $schedule->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="bank_account_id" class="form-label">Transfering From</label>
                                        <select class="form-select" id="bank_account_id" name="from" required>
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ $schedule->from == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="to_bank_account_id" class="form-label">Transfering To</label>
                                        <select class="form-select" id="to_bank_account_id" name="to" required>
                                            <option value="">Select Bank</option>
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}"
                                                    {{ $schedule->to == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                            value="{{ $schedule->amount }}" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3">{{ $schedule->description }}</textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control myDate" id="start_date"
                                            name="start_date" value="{{ $schedule->start_date }}" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control myDate" id="end_date" name="end_date"
                                            value="{{ $schedule->end_date }}">
                                    </div>
                                    <div class="col-12 mb-3 form-check p-0">
                                        <input type="hidden" name="infinite" value="0">
                                        <input type="checkbox" class="form-check-input me-2" id="editinfinites{{ $schedule->id }}"
                                            name="infinite" value="1"
                                            {{ $schedule->infinite == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="editinfinites{{ $schedule->id }}">Continue Indefinitely</label>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection


@section('scripts')
    @if ($schedules->isNotEmpty())
        <script>
            $('#myTable').DataTable({
                pageLength: 25, // default rows per page
                lengthMenu: [
                    [25, 50, 100],
                    [25, 50, 100]
                ], // options in dropdown
                dom: 'Blfrtip', // added 'l' so the length menu appears
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

        function attachSlugListener(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.addEventListener('shown.bs.modal', () => {
                const input = modal.querySelector('.name-input');
                const slugInput = modal.querySelector('.slug-output');
                if (input && slugInput) {
                    input.addEventListener('input', function() {
                        slugInput.value = generateSlug(this.value);
                    });
                }
            });
        }

        // Attach for Add Modal
        attachSlugListener('addmodals');
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
                    url: "{{ route('bankschedule.store') }}",
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
            $('form[id^="editIncomeCategoryForms"] button[type="submit"]').on('click', function(e) {
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
                    success: function(response) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');

                        form.reset();
                        $('#editModal' + response.id).modal('hide');

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
@endsection
