<div class="modal fade" id="editBankTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editBankTransactionForm" method="POST" action=" {{ route('banktransaction.update', $banktransactions->id) }} " enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Bank Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="transaction_id_hidden" id="editTransactionIdHidden">

                        <div class="mb-3 d-none">
                            <label class="form-label">Transaction Name</label>
                            <input type="text" class="form-control name-input" name="name" id="editBankName"
                                required value="{{ $banktransactions->name }}">
                        </div>

                        <div class="mb-3 d-none">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="bankslug" name="slug"
                                readonly value="{{ $banktransactions->slug }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bank Account</label>
                            <select class="form-select" name="bank_account_id" id="editBankAccount" required>
                                <option value="">Select Bank</option>
                                @foreach ($bankaccounts as $account)
                                    <option value="{{ $account->id }}" {{ $banktransactions->bank_account_id == $account->id ? 'selected' : '' }}>{{ $account->bank_name }}
                                        ({{ $account->account_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Type</label>
                            <select class="form-select contact-select" name="transaction_type" id="editTransactionType"
                                required>
                                <option value="credit" {{ $banktransactions->transaction_type == 'credit' ? 'selected' : '' }}>জমা</option>
                                <option value="debit" {{ $banktransactions->transaction_type == 'debit' ? 'selected' : '' }}>উত্তোলন</option>
                            </select>
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" class="form-control" name="transaction_id" id="editTransactionID">
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" id="editBankAmount" required value="{{ $banktransactions->amount }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Date</label>
                            <input type="date" class="form-control myDate" name="transaction_date" id="editBankDate"
                                required value="{{ $banktransactions->transaction_date }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control description-input" name="description" id="editBankDescription" rows="3">{{ $banktransactions->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_from" class="form-label">Transfer From (optional)</label>
                            <select class="form-select" id="edittransfer_from" name="transfer_from">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}" {{ $banktransactions->transfer_to && $banktransactions->bank_account_id == $bankaccount->id ? 'selected' : '' }} >{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edittransfer_to" class="form-label">Transfer To (optional)</label>
                            <select class="form-select" id="edittransfer_to" name="transfer_to">
                                <option value="">Select Bank Account</option>
                                @foreach ($bankaccounts as $bankaccount)
                                    <option value="{{ $bankaccount->id }}" {{ $banktransactions->transfer_to && $banktransactions->transfer_to == $bankaccount->id ? 'selected' : '' }} >{{ $bankaccount->bank_name }} ({{ $bankaccount->account_type }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
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
            // Show Edit Modal with data
            $(document).on('click', '.openBankEditModal', function() {
                const button = $(this);
                const id = button.data('id');
            });

            // Optional: AJAX submission (if you want to avoid reload)
            $('#editBankTransactionForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this)[0];
                const formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');
                        $('#editBankTransactionModal').modal('hide');
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
                            toastr.error("Something went wrong.");
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
        $(document).on('change', '.contact-select', function() {
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