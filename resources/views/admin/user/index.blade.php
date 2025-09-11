@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Users</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->admin_panel == 1 ? 'disabled' : '' }}" data-bs-toggle="modal" data-bs-target="#addmodals">Add New
                    User</button>
            </div>
            <div class="card-body row text-nowrap gap-3">
                @if ($users->isNotEmpty())
                    @foreach ($users as $user)
                        <div class="card contact-card col-md-3">
                            <div class="card-header d-flex justify-content-between">
                                <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->admin_panel == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $user->id }}"><i class="bx bx-edit-alt me-1"></i>
                                    Edit</a>
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    
                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->admin_panel == 1 ? 'disabled' : '' }}"><i
                                            class="bx bx-trash me-1"></i> Delete</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                        <img src="{{ asset('admin-assets/assets/img/illustrations/man.png') }}" height="75" class="scaleX-n1-rtl" alt="View Badge User">
                                    </div>
                                    <h3 class="account_number">{{ $user->name }}</h3>
                                    <p class="holder_name">Email: {{ $user->email }} </p>
                                    <p class="holder_name mb-3">Mobile Number: {{ $user->mobile }}</p>
                                    <div class="mt-3">
                                        @php
                                            $fullAccess = [];
                                            $viewOnly = [];
                                            $hidden = [];

                                            $accessItems = [
                                                'sms_and_email' => 'SMS and Email',
                                                'contact' => 'Contact',
                                                'income' => 'Income',
                                                'expense' => 'Expense',
                                                'investment' => 'Investment',
                                                'asset' => 'Asset',
                                                'liability' => 'Liability',
                                                'bankbook' => 'Bankbook',
                                                'accounts' => 'Accounts',
                                            ];

                                            foreach ($accessItems as $field => $label) {
                                                $mode = $user->access->$field ?? null;
                                                if ($mode == 2) {
                                                    $fullAccess[] = $label;
                                                } elseif ($mode == 1) {
                                                    $viewOnly[] = $label;
                                                } elseif ($mode == 3) {
                                                    $hidden[] = $label;
                                                }
                                            }
                                        @endphp

                                        {{-- Full Access --}}
                                        @if (count($fullAccess))
                                            <p class="d-flex flex-wrap gap-2">
                                                <strong>Full Access:</strong>
                                                @foreach ($fullAccess as $item)
                                                    <span class="bank_type">{{ $item }}</span>
                                                @endforeach
                                            </p>
                                        @endif

                                        {{-- View Only --}}
                                        @if (count($viewOnly))
                                            <p class="d-flex flex-wrap gap-2">
                                                <strong>View Only:</strong>
                                                @foreach ($viewOnly as $item)
                                                    <span class="bank_type">{{ $item }}</span>
                                                @endforeach
                                            </p>
                                        @endif

                                        {{-- Hidden --}}
                                        @if (count($hidden))
                                            <p class="d-flex flex-wrap gap-2">
                                                <strong>Hidden:</strong>
                                                @foreach ($hidden as $item)
                                                    <span class="bank_type">{{ $item }}</span>
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            </div>

                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal -->
    <div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                <form id="addIncomeCategoryForms">
                    @csrf
            
                    <!-- Step 1: Asset Details -->
                    <div id="step1">
                        <div class="row">
                            <h4>Step 1: User Details</h4>
            
                            <div class="col-12 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control name-input" required>
                            </div>
                
                            <div class="col-12 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label>Mobile Number</label>
                                <input type="number" name="mobile" class="form-control" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label>Password</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                            </div>
                        </div>
                    </div>
            
                    <!-- Step 2: User Details -->
                    <div id="step2" style="display: none;">
                        <div class="row">
                            <h4>Step 2: User Access</h4>

                            <div class="col-6 mb-3">
                                <label>SMS and Email</label>
                                <select name="sms_and_email" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Contact</label>
                                <select name="contact" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Income</label>
                                <select name="income" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Expense</label>
                                <select name="expense" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Investment</label>
                                <select name="investment" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Asset</label>
                                <select name="asset" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Liability</label>
                                <select name="liability" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Bankbook</label>
                                <select name="bankbook" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Accounts</label>
                                <select name="accounts" id="" class="form-select">
                                    <option value="1">View Only</option>
                                    <option value="2">Full Access</option>
                                    <option value="3">Hide Menu</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <button type="button" class="btn btn-secondary" onclick="prevStep()">Back</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>  
        </div>
    </div>

    <!-- / Modal -->
      

    @if($users->isNotEmpty())
        @foreach ($users as $user )

        <div class="modal fade" id="editModal{{ $user->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $user->id }}" action="{{ route('user.update', $user->id) }}">
                        @csrf
                        
                        <div class="modal-body">
                            <!-- Step 1: Asset Details -->
                                <div id="step11{{ $user->id }}">
                                    <div class="row">
                                        <h4>Step 1: User Details</h4>
                        
                                        <div class="col-12 mb-3">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control name-input" required value="{{ $user->name }}">
                                        </div>
                            
                                        <div class="col-12 mb-3">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" required value="{{ $user->email }}">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label>Mobile Number</label>
                                            <input type="number" name="mobile" class="form-control" required value="{{ $user->mobile }}">
                                        </div>
                            
                                        <div class="col-12 mb-3">
                                            <label>Password</label>
                                            <input type="text" name="password" class="form-control" required>
                                        </div>

                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary" onclick="nextStep1({{ $user->id }})">Next</button>
                                        </div>
                                    </div>
                                </div>
                        
                                <!-- Step 2: User Details -->
                                <div id="step21{{ $user->id }}" style="display: none;">
                                    <div class="row">
                                        <h4>Step 2: User Access</h4>

                                        <div class="col-6 mb-3">
                                            <label>SMS and Email</label>
                                            <select name="sms_and_email" id="" class="form-select">
                                                <option value="1" {{ $user->access->sms_and_email == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->sms_and_email == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->sms_and_email == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Contact</label>
                                            <select name="contact" id="" class="form-select">
                                                <option value="1" {{ $user->access->contact == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->contact == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->contact == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Income</label>
                                            <select name="income" id="" class="form-select">
                                                <option value="1" {{ $user->access->income == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->income == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->income == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Expense</label>
                                            <select name="expense" id="" class="form-select">
                                                <option value="1" {{ $user->access->expense == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->expense == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->expense == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Investment</label>
                                            <select name="investment" id="" class="form-select">
                                                <option value="1" {{ $user->access->investment == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->investment == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->investment == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Asset</label>
                                            <select name="asset" id="" class="form-select">
                                                <option value="1" {{ $user->access->asset == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->asset == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->asset == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Liability</label>
                                            <select name="liability" id="" class="form-select">
                                                <option value="1" {{ $user->access->liability == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->liability == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->liability == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Bankbook</label>
                                            <select name="bankbook" id="" class="form-select">
                                                <option value="1" {{ $user->access->bankbook == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->bankbook == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->bankbook == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-6 mb-3">
                                            <label>Accounts</label>
                                            <select name="accounts" id="" class="form-select">
                                                <option value="1" {{ $user->access->accounts == 1 ? 'selected' : '' }}>View Only</option>
                                                <option value="2" {{ $user->access->accounts == 2 ? 'selected' : '' }}>Full Access</option>
                                                <option value="3" {{ $user->access->accounts == 3 ? 'selected' : '' }}>Hide Menu</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <button type="button" class="btn btn-secondary" onclick="prevStep1({{ $user->id }})">Back</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>


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
    @if ($users->isNotEmpty())
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
        @foreach ($users as $expensecategory)
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
                    url: "{{ route('user.store') }}",
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
        function nextStep() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        }

        function prevStep() {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
        }
    </script>

    <script>
        function nextStep1(id) {
            document.getElementById('step11' + id).style.display = 'none';
            document.getElementById('step21' + id).style.display = 'block';
        }
        
        function prevStep1(id) {
            document.getElementById('step21' + id).style.display = 'none';
            document.getElementById('step11' + id).style.display = 'block';
        }
    </script>
@endsection
