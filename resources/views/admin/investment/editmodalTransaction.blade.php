<div class="modal fade" id="edittranModal{{ $investmentTransaction->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Investment Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTransCategoryForms{{ $investmentTransaction->id }}"
                action="{{ route('investmenttransaction.update', $investmentTransaction->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="investment_id" value="{{ $investmentTransaction->investment_id }}">
                        <div class="col-12 mb-3">
                            <label>Transation Type</label>
                            <select name="transaction_type" id="" class="form-select">
                                <option value="Deposit"
                                    {{ $investmentTransaction->transaction_type == 'Deposit' ? 'Selected' : '' }}>
                                    জমা </option>
                                <option value="Withdraw"
                                    {{ $investmentTransaction->transaction_type == 'Withdraw' ? 'Selected' : '' }}>
                                    উত্তোলন</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" required
                                value="{{ $investmentTransaction->amount }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label>Transaction Date</label>
                            <input type="date" name="transaction_date" class="form-control myDate" required
                                value="{{ $investmentTransaction->transaction_date }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control"> {{ $investmentTransaction->description }} </textarea>
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_id" class="form-label">Select Bank Account (Optional)</label>
                            <select class="form-select category-select" id="bank_account_id" name="bank_account_id">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}"
                                        {{ $currentTransaction && $currentTransaction->bank_account_id == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->bank_name }}- ({{ $bank->account_type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bank Description (Optional)</label>
                            <textarea class="form-control" name="bank_description" rows="3">{{ $currentTransaction ? $currentTransaction->description : '' }}</textarea>
                        </div>


                        <div class="col-12 mb-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
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
        $('form[id^="editTransCategoryForms"] button[type="submit"]').on('click', function(e) {
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
                    $('#edittranModal' + response.id).modal('hide');

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
