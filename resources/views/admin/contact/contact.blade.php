@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Contact</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addmodals">Add New
                    Contact</button>
            </div>
            <div class="card-body row text-nowrap gap-3">
                @if ($contacts->isNotEmpty())
                    @foreach ($contacts as $contact)
                        <div class="card contact-card col-md-3">
                            <div class="card-header d-flex justify-content-between">
                                <a class=" btn btn-sm btn-outline-secondary" href="" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $contact->id }}"><i class="bx bx-edit-alt me-1"></i>
                                    Edit</a>
                                <form action="{{ route('contact.destroy', $contact->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm"><i
                                            class="bx bx-trash me-1"></i> Delete</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div>
                                    <img src="{{ $contact->image ? asset($contact->image) : asset('admin-assets/img/nophoto.jpg') }}"
                                        width="150px" height="150px" style="object-fit: cover" alt="" class="my-2">
                                    <h3>{{ $contact->name }}</h3>
                                    <p>Mobile Number : {{ $contact->mobile_number }} </p>
                                    <p>Email : {{ $contact->email ?? 'N/A' }} </p>
                                    <p>Date of Birth : {{ $contact->date_of_birth ?? 'N/A' }} </p>
                                    <p>Marrige Date : {{ $contact->marriage_date ?? 'N/A' }} </p>
                                </div>
                            </div>

                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name" required>

                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="amount" name="mobile_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Email</label>
                            <input type="email" class="form-control" id="income_date" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="income_date" name="date_of_birth" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Marrige Date</label>
                            <input type="date" class="form-control" id="income_date" name="marriage_date" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="sms_option" value="1"
                                id="sms_option">
                            <label class="form-check-label" for="sms_option">SMS Option</label>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Image</label>
                            <input type="file" accept="image/*" class="form-control" id="income_date" name="image"
                                required>
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


    @if ($contacts->isNotEmpty())
        @foreach ($contacts as $contact)
            <div class="modal fade" id="editModal{{ $contact->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Contact </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="editIncomeCategoryForms{{ $contact->id }}"
                            action="{{ route('contact.update', $contact->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control name-input" id="name" name="name"
                                        value="{{ $contact->name }}" required>

                                </div>
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control slug-output" id="slug" name="slug"
                                        value="{{ $contact->slug }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Mobile Number</label>
                                    <input type="number" class="form-control" id="amount" name="mobile_number"
                                        required value="{{ $contact->mobile_number }}">
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="income_date" name="email" required
                                        value="{{ $contact->email }}">
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="income_date" name="date_of_birth"
                                        required
                                        value="{{ $contact->date_of_birth ? \Carbon\Carbon::parse($contact->date_of_birth)->format('Y-m-d') : '' }}">
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Marrige Date</label>
                                    <input type="date" class="form-control" id="income_date" name="marriage_date"
                                        required
                                        value="{{ $contact->marriage_date ? \Carbon\Carbon::parse($contact->marriage_date)->format('Y-m-d') : '' }}">
                                </div>
                                <div class="mb-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="sms_option" value="1"
                                        id="sms_option" {{ $contact->sms_option == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_option">SMS Option</label>
                                </div>
                                <div class="mb-3">
                                    <label for="income_date" class="form-label">Image</label>
                                    <input type="file" accept="image/*" class="form-control" id="income_date"
                                        name="image" required>
                                    <p class="my-2">Previous Image</p>
                                    <img src="{{ asset($contact->image) }}" width="100px" height="100px"
                                        style="object-fit: fill" alt="">
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
    @if ($contacts->isNotEmpty())
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
        @foreach ($contacts as $expensecategory)
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
                    url: "{{ route('contact.store') }}",
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
