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
                    @if ($smsTemplates->isNotEmpty())
                        @foreach ($smsTemplates as $smsTemplate)
                            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                                <div class="card contact-card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        
                                        <a class=" btn btn-sm btn-outline-secondary" href="" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $smsTemplate->id }}" >See Current Template </a>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6 class="text-center">
                                                @if($smsTemplate->id == 1)
                                                New Asset
                                                @elseif ($smsTemplate->id == 2)
                                                Asset Deposit Transaction
                                                @elseif ($smsTemplate->id == 3)
                                                Asset Withdraw Transaction
                                                @elseif ($smsTemplate->id == 4)
                                                New Liability
                                                @elseif ($smsTemplate->id == 5)
                                                Liability Deposit Transaction
                                                @elseif ($smsTemplate->id == 6)
                                                Liability Withdraw Transaction
                                                @elseif ($smsTemplate->id == 7)
                                                Monthly Asset
                                                @elseif ($smsTemplate->id == 8)
                                                Monthly Liability
                                                @endif    
                                                SMS Template
                                            </h6>
                                            <form action="{{ route('smsTemplate.update', $smsTemplate->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <textarea name="body" id="" class="form-control" cols="20" rows="3">{{ $smsTemplate->body }}</textarea>
                                                <button type="submit" class="btn btn-primary mt-3 delete-confirm">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div
                class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">Email Templates</h5>
                </div>
            </div>
            <div class="card-body m-0">
                <div class="row g-2">
                    @if ($emailTemplates->isNotEmpty())
                        @foreach ($emailTemplates as $emailTemplate)
                            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                                <div class="card contact-card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        
                                        <a class=" btn btn-sm btn-outline-secondary" href="" data-bs-toggle="modal"
                                            data-bs-target="#editModal1{{ $emailTemplate->id }}" >See Current Template </a>
                                    </div>
                                    <div class="card-body">
                                        <div>
                                            <h6 class="text-center">
                                                @if($emailTemplate->id == 1)
                                                New Asset
                                                @elseif ($emailTemplate->id == 2)
                                                Asset Deposit Transaction
                                                @elseif ($emailTemplate->id == 3)
                                                Asset Withdraw Transaction
                                                @elseif ($emailTemplate->id == 4)
                                                New Liability
                                                @elseif ($emailTemplate->id == 5)
                                                Liability Deposit Transaction
                                                @elseif ($emailTemplate->id == 6)
                                                Liability Withdraw Transaction
                                                @elseif ($emailTemplate->id == 7)
                                                Monthly Asset
                                                @elseif ($emailTemplate->id == 8)
                                                Monthly Liability
                                                @endif    
                                                Email Template
                                            </h6>
                                            <form action="{{ route('emailTemplate.update', $emailTemplate->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <textarea name="body" id="" class="form-control" cols="20" rows="3">{{ $emailTemplate->body }}</textarea>
                                                <button type="submit" class="btn btn-primary mt-3 delete-confirm">Save Changes</button>
                                            </form>
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


    @if ($smsTemplates->isNotEmpty())
        @foreach ($smsTemplates as $smsTemplate)
            <div class="modal fade" id="editModal{{ $smsTemplate->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                @if($smsTemplate->id == 1)
                                                New Asset
                                                @elseif ($smsTemplate->id == 2)
                                                Asset Deposit Transaction
                                                @elseif ($smsTemplate->id == 3)
                                                Asset Withdraw Transaction
                                                @elseif ($smsTemplate->id == 4)
                                                New Liability
                                                @elseif ($smsTemplate->id == 5)
                                                Liability Deposit Transaction
                                                @elseif ($smsTemplate->id == 6)
                                                Liability Withdraw Transaction
                                                @elseif ($smsTemplate->id == 7)
                                                Monthly Asset
                                                @elseif ($smsTemplate->id == 8)
                                                Monthly Liability
                                                @endif    
                                                SMS Template
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">    
                            <div>
                                            <div>
                                                @if($smsTemplate->id == 1)
                                                প্রিয় { সম্পদের নাম}, <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> ***************** । গৃহীত ঋণের পরিমাণ $amount টাকা ।  
                                                @elseif ($smsTemplate->id == 2)
                                                আসসালামু আলাইকুম,
                                                প্রিয় {$accountName}, 
                                                আপনার নিকট $amount টাকা প্রদান করা হয়েছে । <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> রাসেল এর নিকট থেকে আপনার গৃহীত মোট ঋণের পরিমাণ $totalamountBn টাকা।
                                                @elseif ($smsTemplate->id == 3)
                                                আসসালামু আলাইকুম,
                                                প্রিয় {$accountName}, 
                                                আপনার নিকট থেকে $amount টাকা আদায় হয়েছে । <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> রাসেল এর নিকট থেকে আপনার গৃহীত মোট অবশিষ্ট অপরিশোধিত ঋণের পরিমাণ  $totalamountBn টাকা।
                                                @elseif ($smsTemplate->id == 4)
                                                প্রিয় {$accountName}, <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> {$accountNumber} ।  রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ $amount টাকা।
                                                @elseif ($smsTemplate->id == 5)
                                                আসসালামু আলাইকুম,
                                                প্রিয় {$accountName}, 
                                                আপনার নিকট থেকে $amount টাকা  গ্রহন  করা হয়েছে । <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ $totalamountBn টাকা।";
                                                @elseif ($smsTemplate->id == 6)
                                                আসসালামু আলাইকুম,
                                                প্রিয় {$accountName}, 
                                                আপনার নিকট $amount টাকা পরিশোধ করা হয়েছে । <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> রাসেল এর নিকট আপনার প্রদত্ত মোট অবশিষ্ট পাওনা ঋণের পরিমাণ  $totalamountBn টাকা।";
                                                @elseif ($smsTemplate->id == 7)
                                                প্রিয় {$accountName}, <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> {$amount} টাকা ।
                                                @elseif ($smsTemplate->id == 8)
                                                প্রিয় {$accountName}, <span class="text-danger"> {{ $smsTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> {$amount} টাকা ।
                                                @endif 
                                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if ($emailTemplates->isNotEmpty())
        @foreach ($emailTemplates as $emailTemplate)
            <div class="modal fade" id="editModal1{{ $emailTemplate->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                @if($emailTemplate->id == 1)
                                                New Asset
                                                @elseif ($emailTemplate->id == 2)
                                                Asset Deposit Transaction
                                                @elseif ($emailTemplate->id == 3)
                                                Asset Withdraw Transaction
                                                @elseif ($emailTemplate->id == 4)
                                                New Liability
                                                @elseif ($emailTemplate->id == 5)
                                                Liability Deposit Transaction
                                                @elseif ($emailTemplate->id == 6)
                                                Liability Withdraw Transaction
                                                @elseif ($emailTemplate->id == 7)
                                                Monthly Asset
                                                @elseif ($emailTemplate->id == 8)
                                                Monthly Liability
                                                @endif    
                                                Email Template
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">    
                            <div>
                                            <div>
                                                @if($emailTemplate->id == 1)

                                                প্রিয় { সম্পদের নাম}, <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> ***************** ।
                                                
                                                @elseif ($emailTemplate->id == 2)

                                                প্রিয় {$accountName},
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>

                                                @elseif ($emailTemplate->id == 3)

                                                প্রিয়, {$accountName},
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>

                                                @elseif ($emailTemplate->id == 4)
                                                প্রিয় { দায়ের নাম}, <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span> ***************** ।
                                                @elseif ($emailTemplate->id == 5)
                                                 প্রিয় {$accountName},
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>
                                                @elseif ($emailTemplate->id == 6)
                                                 প্রিয় {$accountName},
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>
                                                @elseif ($emailTemplate->id == 7)
                                                প্রিয় {$accountName}, আপনার নিকট আজ পর্যন্ত রাসেল এর থেকে প্রাপ্ত মোট অপরিশোধিত ঋণের পরিমান $totalAmountBn টাকা ৷
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>
                                                @elseif ($emailTemplate->id == 8)
                                                প্রিয় {$accountName}, আপনার প্রদান করা রাসেল এর নিকট আজ পর্যন্ত মোট ঋণের অবশিষ্ট পাওনা $totalAmountBn টাকা ৷
                                                <span class="text-danger"> {{ $emailTemplate->body ?? 'নতুন লেখা গুলো এখানে যুক্ত হবে !' }} </span>
                                                @endif 
                                            </div>
                            </div>
                        </div>
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
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>

@endsection
