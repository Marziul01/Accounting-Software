@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Income</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                    data-bs-target="#addmodals">Add New Income</button>
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
                                <th>Amount</th>
                                <th>Income Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        {{-- <tbody class="table-border-bottom-0">
                            @if($incomes->isNotEmpty())
                            @foreach ($incomes->sortByDesc('date') as $income )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $income->incomeCategory->name ?? 'Income Category Not Assigned' }} - ( {{ $income->incomeSubCategory->name ?? 'Income Sub Category Not Assigned' }} ) </td>
                                <td>{{ $income->name }}</td>
                                <td>{{ $income->description ?? 'N/A' }}</td>
                                <td>{{ $income->amount ?? 'N/A' }}</td> <!-- ✅ Amount -->
                                <td>{{ \Carbon\Carbon::parse($income->date)->format('d M, Y') ?? 'N/A' }}</td> <!-- ✅ Income Date -->
                                <td>
                                    @if($income->income_category_id != 13)
                                    <div class="d-flex align-items-center gap-1 cursor-pointer">
                                        <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" href="#" data-bs-toggle="modal"
                                           data-bs-target="#editModal{{ $income->id }}"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        <form action="{{ route('income.destroy', $income->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center">No income found.</td>
                            </tr>
                            @endif
                        </tbody> --}}
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Income</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name"  required>
                           
                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug"  readonly>
                        </div>
                        <div class="mb-3">
                            <label for="add_income_category_id" class="form-label">Category</label>
                            <select class="form-select category-select" id="add_income_category_id" name="income_category_id" required>
                                <option value="">Select Category</option>
                                @foreach ($incomeCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_income_sub_category_id" class="form-label">Sub Category</label>
                            <select class="form-select subcategory-select" id="add_income_sub_category_id" name="income_sub_category_id" required>
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Income Date</label>
                            <input type="date" class="form-control" id="income_date" name="date" value="{{ date('Y-m-d') }}" required>
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
      

    {{-- @if($incomes->isNotEmpty())
        @foreach ($incomes as $income )

        <div class="modal fade" id="editModal{{ $income->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Income</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $income->id }}" action="{{ route('income.update', $income->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control name-input" id="name" name="name" value="{{ $income->name }}" required>
                               
                            </div>
                            <div class="mb-3 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="slug" name="slug" value="{{ $income->slug }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_income_category_id" class="form-label">Category</label>
                                <select class="form-select category-select" id="edit_income_category_id{{ $income->id }}" name="income_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($incomeCategories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ $category->id == $income->income_category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="mb-3">
                                <label for="edit_income_sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-select subcategory-select" 
                                        id="edit_income_sub_category_id{{ $income->id }}" 
                                        name="income_sub_category_id" 
                                        data-selected="{{ $income->income_sub_category_id }}" 
                                        required>
                                    <option value="">Select Sub Category</option>
                                </select>

                            </div>
                            
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ $income->amount }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_date" class="form-label">Income Date</label>
                                <input type="date" class="form-control" id="income_date" name="date" value="{{ $income->date }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="Description" class="form-label">Description</label>
                                <textarea class="form-control" id="Description" name="description" rows="3">{{ $income->description }}</textarea>
                                
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
    @endif --}}
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

    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editExpenseForm" method="POST" action="">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Income</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="income_id" id="editExpenseId">
          <div class="mb-3">
            <label for="editName" class="form-label">Name</label>
            <input type="text" class="form-control name-input" id="editName" name="name" required>
          </div>
          <div class="mb-3 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="editslug" name="slug" readonly>
                            </div>
          <div class="mb-3">
            <label for="editCategory" class="form-label">Category</label>
            <select class="form-select" id="editCategory1" name="income_category_id" required>
              <option value="">Select Category</option>
              @foreach ($incomeCategories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="editSubCategory" class="form-label">Sub Category</label>
            <select class="form-select subcategory-select" 
                                        id="editSubCategory" 
                                        name="income_sub_category_id" 
                                        
                                        required>
                                    <option value="">Select Sub Category</option>
                                </select>
          </div>
          <div class="mb-3">
            <label for="editAmount" class="form-label">Amount</label>
            <input type="number" class="form-control" id="editAmount" name="amount" required>
          </div>
          <div class="mb-3">
            <label for="editDate" class="form-label">Income Date</label>
            <input type="date" class="form-control" id="editDate" name="date" required>
          </div>
          <div class="mb-3">
            <label for="editDescription" class="form-label">Description</label>
            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection


@section('scripts')

@if ($incomes->isNotEmpty())
<script>
$(document).ready(function () {
    $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('income.index') }}",
        pageLength: 25,
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        dom: 'Blfrtip',
        buttons: [
            {
                extend: 'csv',
                text: 'Export CSV',
                className: 'btn btn-sm my-custom-table-btn',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: 'Print Table',
                className: 'btn btn-sm my-custom-table-btn',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'income_category', name: 'incomeCategory.name' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[5, 'desc']]
    });
});
</script>
{{-- <script>
    $('#myTable').DataTable({
        pageLength: 25, // default page length
        lengthMenu: [ [25, 50, 100], [25, 50, 100] ], // dropdown options
        dom: 'Blfrtip', // ✅ include 'l' for length menu
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
</script> --}}

    
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
    @foreach ($incomes as $incomecategory)
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
                url: "{{ route('income.store') }}",
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


{{-- <script>
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

</script> --}}

<script>
    $(document).ready(function () {
        $('#editExpenseForm').on('submit', function (e) {
            e.preventDefault();

            toastr.clear();

            let form = this;
            let formData = new FormData(form);
            let actionUrl = form.getAttribute('action');

            $.ajax({
                url: actionUrl,
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
                    $('#editExpenseModal').modal('hide');
                    form.reset();

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
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
        // On Edit button click
        $(document).on('click', '.openEditModal', function () {
            let button = $(this);

            // Get data from button
            let id = button.data('id');
            let name = button.data('name');
            let slug = button.data('slug');
            let amount = button.data('amount');
            let date = button.data('date');
            let description = button.data('description');
            let categoryId = button.data('category-id');
            let subCategoryId = button.data('sub-category-id');

            // Set form action and field values
            let actionUrl = "{{ route('income.update', ':id') }}".replace(':id', id);
$('#editExpenseForm').attr('action', actionUrl);
            $('#editExpenseId').val(id);
            $('#editName').val(name);
            $('#editslug').val(slug);
            $('#editAmount').val(amount);
            $('#editDate').val(date);
            $('#editDescription').val(description);
            $('#editCategory1').val(categoryId); // Set category directly first

            // Load subcategories for that category
            let subCategorySelect = $('#editSubCategory');
            let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id', categoryId);
            subCategorySelect.html('<option value="">Loading...</option>');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    let options = '<option value="">Select Sub Category</option>';
                    data.forEach(function (subCategory) {
                        let selected = subCategory.id == subCategoryId ? 'selected' : '';
                        options += `<option value="${subCategory.id}" ${selected}>${subCategory.name}</option>`;
                    });
                    subCategorySelect.html(options);
                },
                error: function () {
                    subCategorySelect.html('<option value="">Error loading subcategories</option>');
                }
            });

            // Show modal
            $('#editExpenseModal').modal('show');
        });

        // On category change, load related subcategories
        $(document).on('change', '#editCategory1', function () {
            let categoryId = $(this).val();
            let subCategorySelect = $('#editSubCategory');
            let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id', categoryId);

            subCategorySelect.html('<option value="">Loading...</option>');

            if (categoryId) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let options = '<option value="">Select Sub Category</option>';
                        data.forEach(function (subCategory) {
                            options += `<option value="${subCategory.id}">${subCategory.name}</option>`;
                        });
                        subCategorySelect.html(options);
                    },
                    error: function () {
                        subCategorySelect.html('<option value="">Error loading subcategories</option>');
                    }
                });
            } else {
                subCategorySelect.html('<option value="">Select Sub Category</option>');
            }
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


<script>
    $(document).ready(function () {
        // When any modal is shown
        $('.modal').on('shown.bs.modal', function () {
            let modal = $(this);
            let categorySelect = modal.find('.category-select');
            let subCategorySelect = modal.find('.subcategory-select');
            let selectedCategoryId = categorySelect.val();
            let selectedSubCategoryId = subCategorySelect.data('selected');
    
            if (selectedCategoryId) {
                let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id', selectedCategoryId);
                subCategorySelect.html('<option value="">Loading...</option>');
    
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let options = '<option value="">Select Sub Category</option>';
                        data.forEach(function (subCategory) {
                            let selected = (subCategory.id == selectedSubCategoryId) ? 'selected' : '';
                            options += `<option value="${subCategory.id}" ${selected}>${subCategory.name}</option>`;
                        });
                        subCategorySelect.html(options);
                    },
                    error: function () {
                        subCategorySelect.html('<option value="">Error loading subcategories</option>');
                    }
                });
            }
        });
    
        // On change of any category dropdown, load subcategories
        $(document).on('change', '.category-select', function () {
            let categoryId = $(this).val();
            let subCategorySelect = $(this).closest('.mb-3').next().find('.subcategory-select');
            let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id', categoryId);
    
            subCategorySelect.html('<option value="">Loading...</option>');
    
            if (categoryId) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        let options = '<option value="">Select Sub Category</option>';
                        data.forEach(function (subCategory) {
                            options += `<option value="${subCategory.id}">${subCategory.name}</option>`;
                        });
                        subCategorySelect.html(options);
                    },
                    error: function () {
                        subCategorySelect.html('<option value="">Error loading subcategories</option>');
                    }
                });
            } else {
                subCategorySelect.html('<option value="">Select Sub Category</option>');
            }
        });
    });
</script>
    
    
    
<script>
    $(document).on('change', '.subcategory-select', function () {
        // Get selected subcategory name
        let subcategoryName = $(this).find('option:selected').text();

        // Get closest form
        let $form = $(this).closest('form');

        // Fill in name field
        $form.find('.name-input').val(subcategoryName);

        // If generateSlug function is defined globally, use it
        if (typeof generateSlug === 'function') {
            const slug = generateSlug(subcategoryName);
            $form.find('.slug-output').val(slug);
        }
    });
</script>



    
    
    

@endsection
