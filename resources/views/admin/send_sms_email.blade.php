@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div
                class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">SMS Templates</h5>
                </div>
            </div>
            <div class="card-body m-0">
                <div class="row g-2">
                    <div class="col-12 col-lg-6 mb-2">
                        <div class="card contact-card h-100">
                            <div class="card-header">
                                <h6><i class="fa-solid fa-comment-sms"></i> Send SMS</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.send.sms') }}" method="POST" id="sendSmsForm">
                                    @csrf
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Select Contacts</label>
                                        <select id="contact_ids" name="contact_ids[]" class="form-select select2" multiple required>
                                            @foreach($contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->mobile_number }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="select-all-contacts">Select All</button>
                                    </div>
                                    <div class="mb-3">
                                        <label for="smsMessage" class="form-label">Message</label>
                                        <textarea name="message" id="smsMessage" class="form-control" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send SMS</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-2">
                        <div class="card contact-card h-100">
                            <div class="card-header">
                                <h6><i class="fa-solid fa-envelope"></i> Send Email</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.send.email') }}" method="POST" id="sendEmailForm">
                                    @csrf
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Select Contacts</label>
                                        <select id="contact_ids1" name="contact_ids[]" class="form-select select2" multiple required>
                                            @foreach($contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->email }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="select-all-contacts1">Select All</button>
                                    </div>
                                    <div class="mb-3">
                                        <label for="emailSubject" class="form-label">Subject</label>
                                        <input type="text" name="subject" id="emailSubject" class="form-control"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="emailMessage" class="form-label">Message</label>
                                        <textarea name="message" id="emailMessage" class="form-control" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Email</button>
                                </form>
                            </div>
                        </div>
                    </div>
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

    <div id="fullscreenLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="display:flex; justify-content:center; align-items:center; width:100%; height:100%;">
            <div class="loader-custom"></div>
        </div>
    </div>
@endsection


@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 with dropdownParent to fix modal clipping issue
            $('#contact_ids').select2({
                dropdownParent: $('#sendSmsForm')
            });
            $('#select-all-contacts').click(function () {
                $('#contact_ids option').prop('selected', true);
                $('#contact_ids').trigger('change');
            });
        });
        
</script>
<script>
        $(document).ready(function () {
            // Initialize Select2 with dropdownParent to fix modal clipping issue
            $('#contact_ids1').select2({
                dropdownParent: $('#sendSmsForm')
            });
            $('#select-all-contacts1').click(function () {
                $('#contact_ids1 option').prop('selected', true);
                $('#contact_ids1').trigger('change');
            });
        });
</script>
    
    <script>
    $(document).ready(function () {

        // ✅ Handle SMS Form Submit
        $('#sendSmsForm button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            toastr.clear();

            let form = $('#sendSmsForm')[0];
            let formData = new FormData(form);
            $('#fullscreenLoader').fadeIn();
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
                    $('#fullscreenLoader').fadeOut();
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');

                    form.reset();
                    $('#contact_ids').val(null).trigger('change');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
                    $('#fullscreenLoader').fadeOut();
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

        // ✅ Handle Email Form Submit
        $('#sendEmailForm button[type="submit"]').on('click', function (e) {
            e.preventDefault();

            toastr.clear();

            let form = $('#sendEmailForm')[0];
            let formData = new FormData(form);
            $('#fullscreenLoader').fadeIn();
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
                    $('#fullscreenLoader').fadeOut();
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');

                    form.reset();
                    $('#contact_ids1').val(null).trigger('change');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
                    $('#fullscreenLoader').fadeOut();
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

@endsection
