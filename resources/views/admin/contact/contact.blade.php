@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div
                class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">Contact</h5>
                </div>
                <div class=" d-flex gap-3 align-items-start flex-column flex-md-row justify-content-md-end">
                    <div class="">
                        <input type="text" class="form-control" id="contactSearch"
                            placeholder="Search by name, email, or number...">
                    </div>
                    <button type="button"
                        class="btn btn-primary {{ Auth::user()->access->contact == 1 ? 'disabled' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#addmodals">Add New Contact</button>
                </div>

            </div>
            <div class="card-body m-0">
                <div class="row g-2">
                    @if ($contacts->isNotEmpty())
                        @foreach ($contacts as $contact)
                            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                                <div class="card contact-card h-100" data-name="{{ strtolower($contact->name) }}"
                                    data-email="{{ strtolower($contact->email) }}"
                                    data-number="{{ strtolower($contact->mobile_number) }}">
                                    <div class="card-header d-flex justify-content-between">
                                        <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->contact == 1 ? 'disabled' : '' }} "
                                            href="" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $contact->id }}"><i
                                                class="bx bx-edit-alt me-1"></i>
                                            Edit</a>
                                        <form action="{{ route('contact.destroy', $contact->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->contact == 1 ? 'disabled' : '' }}"><i
                                                    class="bx bx-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center position-relative">
                                                <img src="{{ $contact->image ? asset($contact->image) : asset('admin-assets/img/nophoto.jpg') }}"
                                                    width="150px" height="150px"
                                                    style="object-fit: cover; border-radius: 50%" alt=""
                                                    class="my-2">
                                                <h3>{{ $contact->name }}</h3>
                                            </div>
                                            <p class="position-absolute badge bg-success" style="top: -10px; left: -5px;">
                                                {{ $loop->iteration }}</p>

                                            <p>Mobile Number : {{ $contact->mobile_number }} </p>
                                            <p>Email : {{ $contact->email ?? 'N/A' }} </p>
                                            <p>Date of Birth : {{ $contact->date_of_birth ?? 'N/A' }} </p>
                                            <p>Marrige Date : {{ $contact->marriage_date ?? 'N/A' }} </p>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control name-input" id="name" name="name"
                                    required>

                            </div>
                            <div class="mb-3 col-6 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="slug" name="slug"
                                    readonly>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="amount" class="form-label">Mobile Number</label>
                                <input type="number" class="form-control" id="amount" name="mobile_number" required>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="income_date" class="form-label">Email</label>
                                <input type="email" class="form-control" id="income_date" name="email" required>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="income_date" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="income_date" name="date_of_birth" required>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="income_date" class="form-label">Marrige Date</label>
                                <input type="date" class="form-control" id="income_date" name="marriage_date" required>
                            </div>

                            {{-- <div class="mb-3">
                            <label for="income_date" class="form-label">Image</label>
                            <input type="file" accept="image/*" class="form-control" id="income_date" name="image"
                                required>
                        </div> --}}
                            @php
                                $fields = [
                                    'national_id',
                                    'father_name',
                                    'father_mobile',
                                    'mother_name',
                                    'mother_mobile',
                                    'spouse_name',
                                    'spouse_mobile',
                                    'present_address',
                                    'permanent_address',
                                ];
                            @endphp
                            @foreach ($fields as $field)
                                <div class="col-6 mb-3">
                                    <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                    <input
                                        type="{{ in_array($field, ['email', 'entry_date']) ? ($field == 'entry_date' ? 'date' : 'email') : 'text' }}"
                                        name="{{ $field }}" class="form-control">
                                </div>
                            @endforeach
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($data->image) ? asset('storage/' . $data->image) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($data->image) ? '' : 'style=display:none;' }}>Crop Existing Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image">
                            </div>

                            <div class="col-12 row mx-0 mb-3">
                                <div class="mb-3 col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="sms_option" value="1"
                                        id="sms_option">
                                    <label class="form-check-label" for="sms_option">SMS Option</label>
                                </div>
                                <div class="col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                        id="sendEmail1">
                                    <label class="form-check-label" for="sendEmail1">Email Enabled</label>
                                </div>
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
                                <div class="row">
                                    <div class="mb-3 col-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control name-input" id="name" name="name"
                                            value="{{ $contact->name }}" required>

                                    </div>
                                    <div class="mb-3 col-6 d-none">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control slug-output" id="slug" name="slug"
                                            value="{{ $contact->slug }}" readonly>
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="amount" class="form-label">Mobile Number</label>
                                        <input type="number" class="form-control" id="amount" name="mobile_number"
                                            required value="{{ $contact->mobile_number }}">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="income_date" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="income_date" name="email" required
                                            value="{{ $contact->email }}">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="income_date" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="income_date" name="date_of_birth"
                                            required
                                            value="{{ $contact->date_of_birth ? \Carbon\Carbon::parse($contact->date_of_birth)->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="income_date" class="form-label">Marrige Date</label>
                                        <input type="date" class="form-control" id="income_date" name="marriage_date"
                                            required
                                            value="{{ $contact->marriage_date ? \Carbon\Carbon::parse($contact->marriage_date)->format('Y-m-d') : '' }}">
                                    </div>
                                    @php
                                        $fields = [
                                            'national_id',
                                            'father_name',
                                            'father_mobile',
                                            'mother_name',
                                            'mother_mobile',
                                            'spouse_name',
                                            'spouse_mobile',
                                            'present_address',
                                            'permanent_address',
                                        ];
                                    @endphp
                                    @foreach ($fields as $field)
                                        <div class="col-6 mb-3">
                                            <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                            <input
                                                type="{{ in_array($field, ['email', 'entry_date']) ? ($field == 'entry_date' ? 'date' : 'email') : 'text' }}"
                                                name="{{ $field }}" class="form-control" value=" {{ $contact->$field }} ">
                                        </div>
                                    @endforeach
                                    <div class="col-12 row mx-0 mb-3">
                                        <div class="mb-3 form-check">
                                            <input class="form-check-input" type="checkbox" name="sms_option" value="1"
                                                id="sms_option" {{ $contact->sms_option == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sms_option">SMS Option</label>
                                        </div>
                                        <div class="col-6 form-check">
                                            <input class="form-check-input" type="checkbox" name="send_email" value="1"
                                                id="sendEmail1" {{ $contact->send_email == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sendEmail1">Email Enabled</label>
                                        </div>
                                    </div>
                                    
                                    {{-- <div class="mb-3">
                                        <label for="income_date" class="form-label">Image</label>
                                        <input type="file" accept="image/*" class="form-control" id="income_date"
                                            name="image" required>
                                        <p class="my-2">Previous Image</p>
                                        <img src="{{ asset($contact->image) }}" width="100px" height="100px"
                                            style="object-fit: fill" alt="">
                                    </div> --}}
                                    <div class="mb-3">
                                        <label class="form-label">Image</label>
                                        <!-- Image preview -->
                                        <div class="mb-2">
                                            <img class="preview-img"
                                                src="{{ isset($contact->image) ? asset($contact->image) : '' }}"
                                                style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                            <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                                {{ isset($contact->image) ? '' : 'style=display:none;' }}>Crop Existing
                                                Image</button>
                                        </div>
                                        <input type="file" accept="image/*" class="form-control image-input"
                                            name="image">
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

    <script>
        $(document).ready(function() {
            let cropper;
            let activeInput = null;
            let activePreview = null;
            let currentImageURL = ''; // Used for existing image crop

            // Show cropper when uploading new image
            $(document).on('change', '.image-input', function(e) {
                const file = e.target.files[0];
                if (!file || !file.type.startsWith('image/')) return;

                activeInput = this;
                activePreview = $(this).closest('.mb-3').find('.preview-img')[0];

                const reader = new FileReader();
                reader.onload = function(event) {
                    $('#cropper-image').attr('src', event.target.result);
                    $('#imageCropModal').modal('show');
                };
                reader.readAsDataURL(file);
            });

            // Crop button for existing saved image
            $(document).on('click', '.crop-existing-btn', function() {
                activeInput = $(this).closest('.mb-3').find('.image-input')[0];
                activePreview = $(this).closest('.mb-3').find('.preview-img')[0];
                currentImageURL = $(activePreview).attr('src');

                $('#cropper-image').attr('src', currentImageURL);
                $('#imageCropModal').modal('show');
            });

            // Init cropper on modal open
            $('#imageCropModal').on('shown.bs.modal', function() {
                cropper = new Cropper(document.getElementById('cropper-image'), {
                    aspectRatio: 1,
                    viewMode: 1,
                    background: false,
                    ready() {
                        $('.cropper-view-box, .cropper-face').css({
                            borderRadius: '50%'
                        });
                    }
                });
            }).on('hidden.bs.modal', function() {
                cropper?.destroy();
                cropper = null;
            });

            // Crop and use (with circle masking)
            $('#cropAndSave').click(function() {
                const croppedCanvas = cropper.getCroppedCanvas();

                // Create a second canvas for circular mask
                const size = Math.min(croppedCanvas.width, croppedCanvas.height);
                const circleCanvas = document.createElement('canvas');
                circleCanvas.width = size;
                circleCanvas.height = size;

                const ctx = circleCanvas.getContext('2d');

                // Draw circle clipping path
                ctx.beginPath();
                ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();

                // Draw the square cropped image inside the circular path
                ctx.drawImage(croppedCanvas, 0, 0, size, size);

                // Export as PNG (preserves transparent corners)
                circleCanvas.toBlob(function(blob) {
                    const file = new File([blob], "cropped.png", {
                        type: 'image/png'
                    });

                    // Replace file input
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    activeInput.files = dt.files;

                    // Show cropped preview
                    if (activePreview) {
                        activePreview.src = URL.createObjectURL(file);
                    }

                    $('#imageCropModal').modal('hide');
                }, 'image/png');
            });
        });
    </script>

    <script>
        document.getElementById('contactSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            document.querySelectorAll('.contact-card').forEach(function(card) {
                const name = card.getAttribute('data-name');
                const email = card.getAttribute('data-email');
                const number = card.getAttribute('data-number');

                const isVisible = name.includes(query) || email.includes(query) || number.includes(query);
                card.style.display = isVisible ? 'block' : 'none';
            });
        });
    </script>
@endsection
