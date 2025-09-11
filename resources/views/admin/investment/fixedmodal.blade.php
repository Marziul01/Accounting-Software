<div class="modal fade" id="incomeModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Gain from Investment </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="investmentIncomeForms">
                @csrf

                <div class="modal-body">
                    <div class="">
                        <div class="col-12 mb-3">
                            <label>Select Investment</label>
                            <select name="investment_id" id="investmentSelect" class="form-select" data-investments='@json($investments)'>
                                @foreach ($investments as $investment)
                                    <option value="{{ $investment->id }}">{{ $investment->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="category_id" value="13">
                        <input type="hidden" name="subcategory_id" id="subcategory_id_input">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="income_date" class="form-label">Income Date</label>
                        <input type="date" class="form-control" id="date" name="date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="Description" class="form-label">Description</label>
                        <textarea class="form-control" id="Description" name="description" rows="3"></textarea>

                    </div>

                    <div class="mb-3">
                                <label for="edit_bank_account_id" class="form-label">Select Bank Account (Optional)</label>
                                <select class="form-select" id="edit_bank_account_id" name="bank_account_id">
                                    <option value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bank Description (Optional)</label>
                                <textarea class="form-control" id="editBankDescription" name="bank_description" rows="3"></textarea>
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
        const input = modal.querySelector('.name-input');
        const slugInput = modal.querySelector('.slug-output');
        if (input && slugInput) {
            input.addEventListener('input', function () {
                slugInput.value = generateSlug(this.value);
            });

            // Trigger once in case of autofill
            slugInput.value = generateSlug(input.value);
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('investmentSelect');
        const subcategoryInput = document.getElementById('subcategory_id_input');
        const investments = JSON.parse(select.dataset.investments);

        function updateSubcategory(investmentId) {
            const investment = investments.find(inv => inv.id == investmentId);
            if (investment) {
                const categoryId = investment.investment_category_id;
                const subcategoryValue = categoryId == 4 ? 8 : 9;
                subcategoryInput.value = subcategoryValue;
            }
        }

        // Set initial value on page load
        updateSubcategory(select.value);

        // Update on change
        select.addEventListener('change', function () {
            updateSubcategory(this.value);
        });
    });
</script>
<script>
    function attachInvestmentSubcategoryHandler(container = document) {
        const select = container.querySelector('#investmentSelect');
        const subcategoryInput = container.querySelector('#subcategory_id_input');

        if (!select || !subcategoryInput) return;

        let investments;
        try {
            investments = JSON.parse(select.dataset.investments);
        } catch (e) {
            console.error('Could not parse investment data:', e);
            return;
        }

        function updateSubcategory(investmentId) {
            const selected = investments.find(inv => inv.id == investmentId);
            if (selected) {
                const catId = Number(selected.investment_category_id);
                const subcatId = catId === 4 ? 8 : 9;
                subcategoryInput.value = subcatId;
            } else {
                subcategoryInput.value = '';
            }
        }

        // Initial
        updateSubcategory(select.value);

        // On change
        select.addEventListener('change', function () {
            updateSubcategory(this.value);
        });
    }
</script>
<script>
    $(function() {

        // grab Laravel’s CSRF token once
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // delegate submit handler to every form id that starts with investmentIncomeForms
        $(document).on('submit', 'form[id^="investmentIncomeForms"]', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $btn = $form.find('button[type=submit]');
            $btn.prop('disabled', true);

            // build payload
            const data = $form.serialize();

            $.post('{{ route('investment-income.store') }}', data)
                .done(function(res) {

                    // 🔔 Toastr success
                    toastr.success(res.message);

                    // hide the current modal
                    $form.closest('.modal').modal('hide');

                    // show success modal, update its message
                    $('#successMessage').text(res.message);
                    $('#successModal').modal('show');

                    // auto-reload after 2 s
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                })
                .fail(function(xhr) {

                    // 422 = validation; 403/500 handled similarly
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, msgs) {
                            toastr.error(msgs[0]);
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Server error');
                    }
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });
    });
</script>
