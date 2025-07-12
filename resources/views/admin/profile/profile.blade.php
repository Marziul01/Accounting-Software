@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row m-0 p-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Admin Profile</h5>
                </div>
                <div class="card-body">
                    <form id="adminProfileForm">
                        @csrf
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                            <span class="text-danger error-text email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ auth()->user()->mobile }}">
                            <span class="text-danger error-text phone_error"></span>
                        </div>
                        <div class="mb-3">
                            <label>Password <small>(leave blank to keep unchanged)</small></label>
                            <input type="password" name="password" class="form-control">
                            <span class="text-danger error-text password_error"></span>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                            <span class="text-danger error-text password_confirmation_error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>


        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('adminProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        // Clear previous errors
        document.querySelectorAll('.error-text').forEach(el => el.innerText = '');

        fetch("{{ route('admin.profile.update') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': formData.get('_token')
            },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();

            if (!res.ok) {
                if (data.errors) {
                    for (const [key, messages] of Object.entries(data.errors)) {
                        const errorSpan = document.querySelector(`.${key}_error`);
                        if (errorSpan) {
                            errorSpan.innerText = messages[0];
                        }
                        // Show each error in Toastr
                        messages.forEach(msg => toastr.error(msg));
                    }
                } else {
                    toastr.error('Something went wrong.');
                }
            } else {
                toastr.success(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        })
        .catch(() => {
            toastr.error('Something went wrong. Please try again.');
        });
    });
</script>



@endsection
