<div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New  Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="mb-3 d-none">
                            <label for="name" class="form-label">Transaction Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name"  required>
                           
                        </div>
                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug"  readonly>
                        </div>
                        <div class="mb-3">
                                <label for="add_income_category_id" class="form-label">Bank Account</label>
                                <select class="form-select category-select" id="add_income_category_id" name="bank_account_id" required>
                                    <option value="">Select Bank Account</option>
                                    @foreach ($bankaccounts as $bankaccount)
                                        <option value="{{ $bankaccount->id }}" >{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }}) </option>
                                    @endforeach
                                </select>
                            </div>
                        
                        <div class="mb-3">
                            <label for="add_income_sub_category_id" class="form-label">Transaction Type</label>
                            <select class="form-select subcategory-select" id="add_income_sub_category_id" name="transaction_type" required>
                                <option value="">Select Transaction Type</option>
                                <option value="credit">জমা</option>
                                <option value="debit">উত্তোলন</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Transaction Date</label>
                            <input type="date" class="form-control" id="income_date" name="transaction_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" id="income_date" name="transaction_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="Description" class="form-label">Description</label>
                            <textarea class="form-control description-input" id="Description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_from" class="form-label">Transfer From (optional)</label>
                            <select class="form-select" id="edittransfer_from" name="transfer_from">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_to" class="form-label">Transfer To (optional)</label>
                            <select class="form-select" id="edittransfer_to" name="transfer_to">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}">{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
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
            .replace(/[^\w\s]/gi, '')
            .trim()
            .replace(/\s+/g, '_');
    }

    function attachSlugListener(modal) {
        const nameInput = modal.querySelector('.name-input');
        const slugInput = modal.querySelector('.slug-output');
        const descInput = modal.querySelector('.description-input');

        // Slug updates when typing in name input
        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function () {
                slugInput.value = generateSlug(this.value);
            });

            slugInput.value = generateSlug(nameInput.value); // trigger on load
        }

        // Name & Slug auto-filled when typing in description input
        if (descInput && nameInput && slugInput) {
            descInput.addEventListener('input', function () {
                const value = this.value.trim();
                nameInput.value = value;
                slugInput.value = generateSlug(value);
            });

            // Also trigger once in case description has initial value
            const value = descInput.value.trim();
            if (value) {
                nameInput.value = value;
                slugInput.value = generateSlug(value);
            }
        }
    }
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
                url: "{{ route('banktransaction.store') }}",
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
    $(document).on('change', '.contact-select', function () {
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