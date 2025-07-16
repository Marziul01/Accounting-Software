@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div
                class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">Occasion</h5>
                </div>
                <div class=" d-flex gap-3 align-items-start flex-column flex-md-row justify-content-md-end">
                    <div class="">
                        <input type="text" class="form-control" id="contactSearch"
                            placeholder="Search by name, email, or number...">
                    </div>
                    <button type="button"
                        class="btn btn-primary {{ Auth::user()->access->sms_and_email == 1 ? 'disabled' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#addmodals">Add New Occasion</button>
                </div>

            </div>
            <div class="card-body m-0">
                <div class="row g-2">
                    @if ($occassions->isNotEmpty())
                        @foreach ($occassions as $occasion)
                            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                                <div class="card contact-card h-100" data-name="{{ strtolower($occasion->occassion) }}">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->sms_and_email == 1 ? 'disabled' : '' }} "
                                            href="" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $occasion->id }}"><i
                                                class="bx bx-edit-alt me-1"></i>
                                            Edit</a>
                                        <form action="{{ route('occassion.destroy', $occasion->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->sms_and_email == 1 ? 'disabled' : '' }}"><i
                                                    class="bx bx-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <div class="d-flex flex-column align-items-center justify-content-center position-relative">
                                                <h3>{{ $occasion->occassion }}</h3>
                                            </div>
                                            <p class="position-absolute badge bg-success" style="top: -10px; left: -5px;">
                                                {{ $loop->iteration }}</p>

                                            <p>Date : {{ $occasion->custom_date ?? 'Auto Selected' }} </p>
                                            @if ($occasion->next_send)
                                                <p class="text-success"> "Message has been sented this year. Next will be sented on {{ $occasion->next_send }} " </p>
                                                @if ($occasion->occassion != 'Birhthday' && $occasion->occassion != 'Anniversary')
                                                    <p class="text-warning"> "Update the date for next year , if not updated yet . Thank You!" </p>
                                                @endif
                                            @endif
                                            <p>Message : {{ $occasion->message ?? 'N/A' }} </p>
                                            <p>Total Contacts: 
                                                @php
                                                    $occasionContactCount = $occasion->contact_ids ? count(explode(',', $occasion->contact_ids)) : 0;
                                                    $allContactsCount = $contacts->count() ?? 0;
                                                @endphp

                                                @if ($occasionContactCount == $allContactsCount)
                                                    All Contacts
                                                @else
                                                    {{ $occasionContactCount }}
                                                @endif
                                            </p>
                                            
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Occasion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            {{-- Contact Selector --}}
                            <div class="mb-3 col-12">
                                <label class="form-label">Select Contacts</label>
                                <select id="contact_ids" name="contact_ids[]" class="form-select select2" multiple required>
                                    @foreach($contacts as $contact)
                                        <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->mobile_number }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="select-all-contacts">Select All</button>
                            </div>

                            {{-- Occasion Type --}}
                            <div class="mb-3 col-6">
                                <label class="form-label">Occasion Type</label>
                                <select name="occasion_type" id="occasion_type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Anniversary">Anniversary</option>
                                    <option value="Eid ul Fitr">Eid ul Fitr</option>
                                    <option value="Eid ul Adha">Eid ul Adha</option>
                                    <option value="Boishakhi">Boishakhi</option>
                                    <option value="Custom">Custom</option>
                                </select>
                            </div>

                            {{-- Custom Occasion Name --}}
                            <div class="mb-3 col-6 d-none" id="custom_occasion_div">
                                <label class="form-label">Custom Occasion Name</label>
                                <input type="text" class="form-control" name="custom_occasion" id="custom_occasion_input">
                            </div>

                            {{-- Date Input --}}
                            <div class="mb-3 col-6">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="custom_date" id="custom_date_input" disabled>
                            </div>

                            {{-- Message --}}
                            <div class="mb-3 col-12">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="3" required></textarea>
                            </div>
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


    @if ($occassions->isNotEmpty())
        @foreach ($occassions as $occasion)
            <div class="modal fade" id="editModal{{ $occasion->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Occasion </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="editIncomeCategoryForms{{ $occasion->id }}"
                            action="{{ route('occassion.update', $occasion->id) }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    {{-- Contact Multi-Selector --}}
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Select Contacts</label>
                                        <select name="contact_ids[]" id="contact_ids_edit_{{ $occasion->id }}" class="form-select category-select select2-edit" multiple required>
                                            @foreach($contacts as $contact)
                                                <option value="{{ $contact->id }}"
                                                    {{ in_array($contact->id, explode(',', $occasion->contact_ids ?? '')) ? 'selected' : '' }}>
                                                    {{ $contact->name }} ({{ $contact->mobile_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 select-all-contacts-edit-btn"
                                            data-target-select="#contact_ids_edit_{{ $occasion->id }}">
                                            Select All
                                        </button>
                                    </div>

                                    {{-- Occasion Type --}}
                                    <div class="mb-3 col-6">
                                        <label class="form-label">Occasion Type</label>
                                        <select name="occasion_type" id="occasion_type_edit_{{ $occasion->id }}" class="form-select category-select occasion-type-edit" required>
                                            @php
                                                $presetTypes = ['Birthday', 'Anniversary', 'Eid ul Fitr', 'Eid ul Adha', 'Boishakhi'];
                                                $isCustom = !in_array($occasion->occassion, $presetTypes);
                                            @endphp
                                            <option value="">-- Select --</option>
                                            @foreach ($presetTypes as $type)
                                                <option value="{{ $type }}" {{ $occasion->occassion == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                            <option value="Custom" {{ $isCustom ? 'selected' : '' }}>Custom</option>
                                        </select>
                                    </div>

                                    {{-- Custom Occasion Name --}}
                                    <div class="mb-3 col-6 {{ $isCustom ? '' : 'd-none' }}" id="custom_occasion_div_edit_{{ $occasion->id }}">
                                        <label class="form-label">Custom Occasion Name</label>
                                        <input type="text" name="custom_occasion" class="form-control" id="custom_occasion_input_edit_{{ $occasion->id }}"
                                            value="{{ $isCustom ? $occasion->occassion : '' }}">
                                    </div>

                                    {{-- Date (Enabled only for Eid or Custom) --}}
                                    <div class="mb-3 col-6">
                                        <label class="form-label">Date</label>
                                        <input type="date" class="form-control" name="custom_date" id="custom_date_input_edit_{{ $occasion->id }}"
                                            value="{{ $occasion->custom_date }}"
                                            {{ in_array($occasion->occassion, ['Eid ul Fitr', 'Eid ul Adha']) || $isCustom ? '' : 'disabled' }}>
                                    </div>

                                    {{-- Message --}}
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" class="form-control" rows="3" required>{{ $occasion->message }}</textarea>
                                    </div>

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

    <div class="modal fade" id="imageCropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div style="max-height: 400px;">
                        <img id="cropper-image" style="max-width: 100%;" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cropAndSave" class="btn btn-primary">Crop & Use</button>
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
                    url: "{{ route('occassion.store') }}",
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

    <script>
        document.getElementById('contactSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll('.contact-card').forEach(function(card) {
                const name = card.getAttribute('data-name');

                const isVisible = name.includes(query);
                card.style.display = isVisible ? 'block' : 'none';
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
    // Initialize Select2 with dropdownParent to fix modal clipping issue
    $('#contact_ids').select2({
        dropdownParent: $('#addmodals')
    });

    $('#select-all-contacts').click(function () {
        $('#contact_ids option').prop('selected', true);
        $('#contact_ids').trigger('change');
    });

    $('#occasion_type').on('change', function () {
        let value = $(this).val();

        if (value === 'Custom') {
            $('#custom_occasion_div').removeClass('d-none');
        } else {
            $('#custom_occasion_div').addClass('d-none');
            $('#custom_occasion_input').val('');
        }

        if (['Boishakhi','Eid ul Fitr', 'Eid ul Adha', 'Custom'].includes(value)) {
            $('#custom_date_input').prop('disabled', false);
        } else {
            $('#custom_date_input').prop('disabled', true).val('');
        }
    });




    @foreach ($occassions as $occasion)
            // Initialize Select2 for the specific occasion's contact select
            $('#contact_ids_edit_{{ $occasion->id }}').select2({
                dropdownParent: $('#editModal{{ $occasion->id }}') // Set dropdownParent to the specific edit modal
            });

            // Handle Select All button for the specific occasion's contact select
            $('#editModal{{ $occasion->id }} .select-all-contacts-edit-btn').on('click', function () {
                let targetSelectId = $(this).data('target-select');
                $(targetSelectId + ' option').prop('selected', true);
                $(targetSelectId).trigger('change');
            });

            // Handle Occasion Type change for the specific occasion's select
            $('#occasion_type_edit_{{ $occasion->id }}').on('change', function () {
                let value = $(this).val();
                let customOccasionDiv = $('#custom_occasion_div_edit_{{ $occasion->id }}');
                let customOccasionInput = $('#custom_occasion_input_edit_{{ $occasion->id }}');
                let customDateInput = $('#custom_date_input_edit_{{ $occasion->id }}');

                if (value === 'Custom') {
                    customOccasionDiv.removeClass('d-none');
                } else {
                    customOccasionDiv.addClass('d-none');
                    customOccasionInput.val('');
                }

                if (['Boishakhi','Eid ul Fitr', 'Eid ul Adha', 'Custom'].includes(value)) {
                    customDateInput.prop('disabled', false);
                } else {
                    customDateInput.prop('disabled', true).val('');
                }
            });
        @endforeach


});

    </script>

@endsection
