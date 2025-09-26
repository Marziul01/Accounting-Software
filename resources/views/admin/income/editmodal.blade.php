<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editExpenseForm" method="POST" action="{{ route('income.update', $income->id ) }}" enctype="multipart/form-data">
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
                            <input type="text" class="form-control name-input" id="editName" name="name"
                                required value="{{ $income->name }}">
                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="editslug" name="slug"
                                readonly value="{{ $income->slug }}">
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select category-select" id="editCategory1" name="income_category_id" required>
                                <option value="">Select Category</option>
                                @foreach ($incomeCategories as $category)
                                    <option value="{{ $category->id }}" {{ $income->income_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editSubCategory" class="form-label">Sub Category</label>
                            <select class="form-select subcategory-select" id="editSubCategory"
                                name="income_sub_category_id" required data-selected="{{ $income->income_sub_category_id }}">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editAmount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="editAmount" name="amount" required value="{{ $income->amount }}">
                        </div>
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Income Date</label>
                            <input type="date" class="form-control myDate" id="editDate" name="date" required value="{{ $income->date }}">
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3">{{ $income->description }}</textarea>
                        </div>

                        <div class="mb-3">
                                <label for="edit_bank_account_id" class="form-label">Select Bank Account (Optional)</label>
                                <select class="form-select" id="edit_bank_account_id" name="bank_account_id">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" {{ $bankIncomeTransactions && $bankIncomeTransactions->bank_account_id && $bankIncomeTransactions->bank_account_id == $bank->id ? 'selected' : '' }}>{{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bank Description (Optional)</label>
                                <textarea class="form-control" id="editBankDescription" name="bank_description" rows="3">{{ $bankIncomeTransactions && $bankIncomeTransactions->description ? $bankIncomeTransactions->description : '' }}</textarea>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

    </script>

    <script>
        $(document).ready(function() {
            $('#editExpenseForm').on('submit', function(e) {
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
                    success: function(response) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
                        $('#editExpenseModal').modal('hide');
                        form.reset();

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
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
            // When any modal is shown
            $('.modal').on('shown.bs.modal', function() {
                let modal = $(this);
                let categorySelect = modal.find('.category-select');
                let subCategorySelect = modal.find('.subcategory-select');
                let selectedCategoryId = categorySelect.val();
                let selectedSubCategoryId = subCategorySelect.data('selected');

                if (selectedCategoryId) {
                    let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id',
                        selectedCategoryId);
                    subCategorySelect.html('<option value="">Loading...</option>');

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            let options = '<option value="">Select Sub Category</option>';
                            data.forEach(function(subCategory) {
                                let selected = (subCategory.id ==
                                    selectedSubCategoryId) ? 'selected' : '';
                                options +=
                                    `<option value="${subCategory.id}" ${selected}>${subCategory.name}</option>`;
                            });
                            subCategorySelect.html(options);
                        },
                        error: function() {
                            subCategorySelect.html(
                                '<option value="">Error loading subcategories</option>');
                        }
                    });
                }
            });

            // On change of any category dropdown, load subcategories
            $(document).on('change', '.category-select', function() {
                let categoryId = $(this).val();
                let subCategorySelect = $(this).closest('.mb-3').next().find('.subcategory-select');
                let url = "{{ route('get.incomesubcategories', ':id') }}".replace(':id', categoryId);

                subCategorySelect.html('<option value="">Loading...</option>');

                if (categoryId) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            let options = '<option value="">Select Sub Category</option>';
                            data.forEach(function(subCategory) {
                                options +=
                                    `<option value="${subCategory.id}">${subCategory.name}</option>`;
                            });
                            subCategorySelect.html(options);
                        },
                        error: function() {
                            subCategorySelect.html(
                                '<option value="">Error loading subcategories</option>');
                        }
                    });
                } else {
                    subCategorySelect.html('<option value="">Select Sub Category</option>');
                }
            });
        });
    </script>



    <script>
        $(document).on('change', '.subcategory-select', function() {
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