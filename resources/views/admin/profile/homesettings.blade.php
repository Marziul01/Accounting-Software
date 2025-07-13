@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card card-body">
            <div class="row m-0 p-0">
            <form id="siteSettingForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
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
                    <div class="form-group col-md-6 mb-3">
                        <label>Description</label>
                        <input type="text" class="form-control" name="desc" value="{{ $home->desc }}">
                        <span class="text-danger error-text desc_error"></span>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label>Image</label>
                        <input type="file" class="form-control" name="image">
                        @if($home->image)
                            <p class="my-2">Previous Image :</p>
                            <img src="{{ asset($home->image) }}" alt="Image" class="mt-2" width="120">
                        @endif
                        <span class="text-danger error-text image_error"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
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



@endsection
