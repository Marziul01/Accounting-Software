@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Income {{ $categorysettings->income_category == 2 ? 'Sub' : '' }} Category</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                    data-bs-target="#addmodals">Add Income {{ $categorysettings->income_category == 2 ? 'Sub' : '' }} Category</button>
            </div>
            <div class="card-body  text-nowrap">
                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Income Category</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if($incomeSubCategories->isNotEmpty())
                            @foreach ($incomeSubCategories as $incomecategory )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $incomecategory->incomeCategory->name ?? 'Income Category Not Assigned' }}</td>
                                <td>{{ $incomecategory->name }}</td>
                                <td>{{ $incomecategory->description ?? 'N/A' }}</td>
                                <td>
                                    @if($incomecategory->status == '1')
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 cursor-pointer">
                                            <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $incomecategory->id }}"><i
                                                    class="bx bx-edit-alt me-1"></i> Edit</a>
                                                     @if($incomecategory->id != 8 && $incomecategory->id != 9)
                                        <form action="{{ route('incomesubcategory.destroy', $incomecategory->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" ><i
                                                    class="bx bx-trash me-1"></i> Delete</button>
                                        </form>
                                    @endif
                                    </div>
                                </td>
                            </tr> 
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-center">No income categories found.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Income {{ $categorysettings->income_category == 2 ? 'Sub' : '' }} Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name"  required>
                           
                        </div>
                        <div class="mb-3">
                            <label for="income_category_id" class="form-label">Category</label>
                            <select class="form-select" id="income_category_id" name="income_category_id" required>
                                <option value="">Select Category</option>
                                @foreach ($incomeCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug"  readonly>
                        </div>
                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control" id="Description" name="description" rows="3"></textarea>
                            
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
      

    @if($incomeSubCategories->isNotEmpty())
        @foreach ($incomeSubCategories as $incomecategory )

        <div class="modal fade" id="editModal{{ $incomecategory->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Income {{ $categorysettings->income_category == 2 ? 'Sub' : '' }} Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $incomecategory->id }}" action="{{ route('incomesubcategory.update', $incomecategory->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control name-input" id="name" name="name" value="{{ $incomecategory->name }}" required>
                               
                            </div>
                            <div class="mb-3 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="slug" name="slug" value="{{ $incomecategory->slug }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="income_category_id" class="form-label">Category</label>
                                <select class="form-select" id="income_category_id" name="income_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($incomeCategories as $category)
                                        <option value="{{ $category->id }}" {{ $incomecategory->income_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="Description" class="form-label">Description</label>
                                <textarea class="form-control" id="Description" name="description" rows="3">{{ $incomecategory->description }}</textarea>
                                
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $incomecategory->status == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ $incomecategory->status == '2' ? 'selected' : '' }}>Inactive</option>
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

@if ($incomeSubCategories->isNotEmpty())
<script>
    $('#myTable').DataTable({
        pageLength: 20,
        dom: 'Bfrtip',
        buttons: [
            {
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
        'অ': 'a', 'আ': 'aa', 'ই': 'i', 'ঈ': 'ii', 'উ': 'u', 'ঊ': 'uu',
        'এ': 'e', 'ঐ': 'oi', 'ও': 'o', 'ঔ': 'ou',
        'ক': 'k', 'খ': 'kh', 'গ': 'g', 'ঘ': 'gh', 'ঙ': 'ng',
        'চ': 'ch', 'ছ': 'chh', 'জ': 'j', 'ঝ': 'jh', 'ঞ': 'n',
        'ট': 't', 'ঠ': 'th', 'ড': 'd', 'ঢ': 'dh', 'ণ': 'n',
        'ত': 't', 'থ': 'th', 'দ': 'd', 'ধ': 'dh', 'ন': 'n',
        'প': 'p', 'ফ': 'ph', 'ব': 'b', 'ভ': 'bh', 'ম': 'm',
        'য': 'j', 'র': 'r', 'ল': 'l', 'শ': 'sh', 'ষ': 'ss',
        'স': 's', 'হ': 'h', 'ড়': 'r', 'ঢ়': 'rh', 'য়': 'y',
        'ৎ': 't', 'ং': 'ng', 'ঃ': '', 'ঁ': ''
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
            .replace(/\s+/g, '_');    // replace spaces with "_"
    }

    function attachSlugListener(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.addEventListener('shown.bs.modal', () => {
            const input = modal.querySelector('.name-input');
            const slugInput = modal.querySelector('.slug-output');
            if (input && slugInput) {
                input.addEventListener('input', function () {
                    slugInput.value = generateSlug(this.value);
                });
            }
        });
    }

    // Attach for Add Modal
    attachSlugListener('addmodals');

    // Attach for all Edit Modals
    @foreach ($incomeSubCategories as $incomecategory)
        attachSlugListener('editModal{{ $incomecategory->id }}');
    @endforeach
</script>

<script>
    $(document).ready(function () {

        $('form#addIncomeCategoryForms button[type="submit"]').on('click', function (e) {
            e.preventDefault();
           

            toastr.clear();

            let form = $('#addIncomeCategoryForms')[0]; // ✅ get the actual form element
            let formData = new FormData(form);          // ✅ pass the form here

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            $.ajax({
                url: "{{ route('incomesubcategory.store') }}",
                method: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    
                    $('#successMessage').text(response.message); // Set dynamic success message
                    $('#successModal').modal('show');

                    $('#addIncomeCategoryForms')[0].reset();
                    $('#addmodals').modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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
    $(document).ready(function () {
        $('form[id^="editIncomeCategoryForms"] button[type="submit"]').on('click', function (e) {
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
                success: function (response) {
                    $('#successMessage').text(response.message);
                    $('#successModal').modal('show');

                    form.reset();
                    $('#editModal' + response.id).modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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
    $(document).on('click', '.delete-confirm', function (e) {
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
