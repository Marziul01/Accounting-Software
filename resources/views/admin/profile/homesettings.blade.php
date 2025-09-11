@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card card-body">
            <div class="row m-0 p-0">
                <form id="siteSettingForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-center">Landing Page Setting</h5>
                            <hr>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $home->name }}">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $home->email }}">
                            <span class="text-danger error-text email_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ $home->phone }}">
                            <span class="text-danger error-text phone_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Address</label>
                            <input type="text" class="form-control" name="address" value="{{ $home->address }}">
                            <span class="text-danger error-text address_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>WhatsApp</label>
                            <input type="text" class="form-control" name="whatsapp" value="{{ $home->whatsapp }}">
                            <span class="text-danger error-text whatsapp_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Facebook</label>
                            <input type="text" class="form-control" name="facebook" value="{{ $home->facebook }}">
                            <span class="text-danger error-text facebook_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Telegram</label>
                            <input type="text" class="form-control" name="telegram" value="{{ $home->telegram }}">
                            <span class="text-danger error-text telegram_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label>Instagram</label>
                            <input type="text" class="form-control" name="insta" value="{{ $home->insta }}">
                            <span class="text-danger error-text insta_error"></span>
                        </div>
                        <div class="form-group col-md-12 mb-3">
                            <label>Description</label>
                            {{-- <input type="text" class="form-control" name="desc" value=""> --}}
                            <textarea name="desc" id="" cols="30" class="form-control" rows="10">{{ $home->desc }}</textarea>
                            <span class="text-danger error-text desc_error"></span>
                        </div>
                        <div class="form-group col-md-6 mb-3 ">
                            {{-- <label>1st Image</label>
                            <input type="file" class="form-control" name="image">
                            @if ($home->image)
                                <p class="my-2">Previous Image :</p>
                                <img src="{{ asset($home->image) }}" alt="Image" class="mt-2" width="120">
                            @endif
                            <span class="text-danger error-text image_error"></span> --}}

                            <div class="border rounded p-3 ">
                                <label class="form-label">1st Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($home->image) ? asset($home->image) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($home->image) ? '' : 'style=display:none;' }}>Crop Existing
                                        Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image">
                            </div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{-- <label>2nd Image</label>
                            <input type="file" class="form-control" name="image2">
                            @if ($home->image2)
                                <p class="my-2">Previous Image :</p>
                                <img src="{{ asset($home->image2) }}" alt="Image" class="mt-2" width="120">
                            @endif
                            <span class="text-danger error-text image_error"></span> --}}

                            <div class="border rounded p-3 ">
                                <label class="form-label">2nd Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($home->image2) ? asset($home->image2) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($home->image2) ? '' : 'style=display:none;' }}>Crop Existing
                                        Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image2">
                            </div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{-- <label>3rd Image</label>
                            <input type="file" class="form-control" name="image3">
                            @if ($home->image3)
                                <p class="my-2">Previous Image :</p>
                                <img src="{{ asset($home->image3) }}" alt="Image" class="mt-2" width="120">
                            @endif
                            <span class="text-danger error-text image_error"></span> --}}

                            <div class="border rounded p-3 ">
                                <label class="form-label">3rd Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($home->image3) ? asset($home->image3) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($home->image3) ? '' : 'style=display:none;' }}>Crop Existing
                                        Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image3">
                            </div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{-- <label>4th Image</label>
                            <input type="file" class="form-control" name="image4">
                            @if ($home->image4)
                                <p class="my-2">Previous Image :</p>
                                <img src="{{ asset($home->image4) }}" alt="Image" class="mt-2" width="120">
                            @endif
                            <span class="text-danger error-text image_error"></span> --}}

                            <div class="border rounded p-3 ">
                                <label class="form-label">4th Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($home->image4) ? asset($home->image4) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($home->image4) ? '' : 'style=display:none;' }}>Crop Existing
                                        Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image4">
                            </div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{-- <label>5th Image</label>
                            <input type="file" class="form-control" name="image5">
                            @if ($home->image5)
                                <p class="my-2">Previous Image :</p>
                                <img src="{{ asset($home->image5) }}" alt="Image" class="mt-2" width="120">
                            @endif
                            <span class="text-danger error-text image_error"></span> --}}

                            <div class="border rounded p-3 ">
                                <label class="form-label">5th Image</label>
                                <!-- Image preview -->
                                <div class="mb-2">
                                    <img class="preview-img"
                                        src="{{ isset($home->image5) ? asset($home->image5) : '' }}"
                                        style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;" />
                                    <button type="button" class="btn btn-sm btn-secondary crop-existing-btn mt-2"
                                        {{ isset($home->image5) ? '' : 'style=display:none;' }}>Crop Existing
                                        Image</button>
                                </div>
                                <input type="file" accept="image/*" class="form-control image-input" name="image5">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                </form>
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
    <script>
        document.getElementById('siteSettingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            // Clear previous errors
            document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

            fetch("{{ route('admin.home-settings.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        if (data.errors) {
                            for (const [key, messages] of Object.entries(data.errors)) {
                                const errorSpan = document.querySelector(`.${key}_error`);
                                if (errorSpan) errorSpan.innerText = messages[0];
                                messages.forEach(msg => toastr.error(msg));
                            }
                        } else {
                            toastr.error('Something went wrong.');
                        }
                    } else {
                        toastr.success(data.message);
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(() => toastr.error('Something went wrong.'));
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

@endsection
