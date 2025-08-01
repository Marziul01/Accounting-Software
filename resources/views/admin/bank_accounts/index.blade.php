@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1"> 
                <h5 class="mb-0">BankBooks</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}" data-bs-toggle="modal" data-bs-target="#addmodals">Add New
                    BankBook</button>
            </div>
            <div class="card-body m-0">
                <div class="row g-2">
                @if ($bankbooks->isNotEmpty())
                    @foreach ($bankbooks as $bankbook)
                    <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                        <div class="card contact-card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $bankbook->id }}"><i class="bx bx-edit-alt me-1"></i>
                                    Edit</a>
                                <form action="{{ route('bankbook.destroy', $bankbook->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}"><i
                                            class="bx bx-trash me-1"></i> Delete</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <p class="position-absolute badge bg-success" style="top: -10px; left: -5px;">{{ $loop->iteration }}</p>
                                <div>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <i class="bx bx-wallet bx-lg text-info bank_card_icon"></i>
                                        <div class="bank_card_info">
                                            <h4> {{ $bankbook->bank_name }}</h4>
                                            <p class="mb-2"> Branch : {{ $bankbook->branch_name }} </p>
                                            <p class="bank_type float-right"> {{ $bankbook->account_type }} </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between gap-2 mb-3">
                                        <div>
                                            <h3 class="account_number">{{ $bankbook->account_number }}</h3>
                                            <p class="holder_name">{{ $bankbook->account_holder_name }} </p>
                                            <p class="nominee">Nominee Name: {{ $bankbook->nominee_name }}</p>
                                            <div class="bank_card_info">
                                                <p class="bank_type font-xx">Balance: {{ number_format($bankbook->balance, 2) }} </p>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add BankBook</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Account Holder Name</label>
                            <input type="text" class="form-control name-input" id="name" name="account_holder_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Account Number</label>
                            <input type="number" class="form-control" id="amount" name="account_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control name-input" id="name" name="bank_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control name-input" id="name" name="branch_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nominee Name</label>
                            <input type="text" class="form-control name-input" id="name" name="nominee_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Account Type</label>
                            <select name="account_type" id="" class="form-select" required>
                                <option value="">হিসাবের ধরন নির্বাচন করুন</option>
                                <option value="সঞ্চয় হিসাব">সঞ্চয় হিসাব</option>
                                <option value="চলতি হিসাব">চলতি হিসাব</option>
                                <option value="মাসিক ডিপিএস হিসাব">মাসিক ডিপিএস হিসাব</option>
                                <option value="মেয়াদী আমানত হিসাব">মেয়াদী আমানত হিসাব</option>
                                <option value="সঞ্চয়পত্র হিসাব">সঞ্চয়পত্র হিসাব</option>
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

    <!-- / Modal -->


    @if ($bankbooks->isNotEmpty())
        @foreach ($bankbooks as $bankbook)
            <div class="modal fade" id="editModal{{ $bankbook->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit BankBook </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="editIncomeCategoryForms{{ $bankbook->id }}"
                            action="{{ route('bankbook.update', $bankbook->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control name-input" id="name" name="account_holder_name"
                                        value="{{ $bankbook->account_holder_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Account Number</label>
                                    <input type="number" class="form-control" id="amount" name="account_number"
                                        value="{{ $bankbook->account_number }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control name-input" id="name" name="bank_name"
                                        value="{{ $bankbook->bank_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Branch Name</label>
                                    <input type="text" class="form-control name-input" id="name" name="branch_name"
                                        value="{{ $bankbook->branch_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nominee Name</label>
                                    <input type="text" class="form-control name-input" id="name" name="nominee_name"
                                        value="{{ $bankbook->nominee_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Account Type</label>
                                    <select name="account_type" id="" class="form-select" required>
                                        <option value="">হিসাবের ধরন নির্বাচন করুন</option>
                                        <option value="সঞ্চয় হিসাব" {{ $bankbook->account_type == 'সঞ্চয় হিসাব' ? 'selected' : '' }}>সঞ্চয় হিসাব</option>
                                        <option value="চলতি হিসাব" {{ $bankbook->account_type == 'চলতি হিসাব' ? 'selected' : '' }}>চলতি হিসাব</option>
                                        <option value="মাসিক ডিপিএস হিসাব" {{ $bankbook->account_type == 'মাসিক ডিপিএস হিসাব' ? 'selected' : '' }}>মাসিক ডিপিএস হিসাব</option>
                                        <option value="মেয়াদী আমানত হিসাব" {{ $bankbook->account_type == 'মেয়াদী আমানত হিসাব' ? 'selected' : '' }}>মেয়াদী আমানত হিসাব</option>
                                        <option value="সঞ্চয়পত্র হিসাব" {{ $bankbook->account_type == 'সঞ্চয়পত্র হিসাব' ? 'selected' : '' }}>সঞ্চয়পত্র হিসাব</option>
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
        @endforeach
    @endif
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
    @if ($bankbooks->isNotEmpty())
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

        // Attach for all Edit Modals
        @foreach ($bankbooks as $expensecategory)
            attachSlugListener('editModal{{ $expensecategory->id }}');
        @endforeach
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
                    url: "{{ route('bankbook.store') }}",
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
