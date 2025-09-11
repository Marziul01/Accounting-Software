<div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Short Term Liability</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                <form id="addIncomeCategoryForms">
                    @csrf
            
                    <!-- Step 1: Asset Details -->
                    <div id="step1">
                        <div class="row">
                            <h4>Step 1: Liability Details</h4>
            
                            <div class="col-6 mb-3 d-none">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control name-input" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label>Select Person From Contacts </label>
                                <select name="contact_id" class="form-select contact-select" id="contact_id">
                                    <option value=""> Select an User </option>
                                    @if ($users)
                                        @foreach ($users as $user )
                                        <option 
                                            value="{{ $user->id }}" 
                                            data-name="{{ $user->name }}" 
                                            data-mobile="{{ $user->mobile_number }}" 
                                            data-email="{{ $user->email }}"
                                            data-national_id="{{ $user->national_id ?? '' }}"
                                            data-father_name="{{ $user->father_name ?? '' }}"
                                            data-father_mobile="{{ $user->father_mobile ?? '' }}"
                                            data-mother_name="{{ $user->mother_name ?? '' }}"
                                            data-mother_mobile="{{ $user->mother_mobile ?? '' }}"
                                            data-spouse_name="{{ $user->spouse_name ?? '' }}"
                                            data-spouse_mobile="{{ $user->spouse_mobile ?? '' }}"
                                            data-present_address="{{ $user->present_address ?? '' }}"
                                            data-permanent_address="{{ $user->permanent_address ?? '' }}"
                                            data-sms_option="{{ $user->sms_option ?? 0 }}"
                                            data-send_email="{{ $user->send_email ?? 0 }}"
                                            >
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                
                            <div class="col-6 mb-3 d-none">
                                <label>Slug</label>
                                <input type="text" name="slug" class="form-control slug-output" required>
                            </div>
                
                            <div class="col-6 mb-3">
                                <label>Amount</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Entry Date</label>
                                <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            
                            <input type="hidden" value="3" name="category_id">
                
                            <div class="col-6 mb-3">
                                <label for="add_income_category_id" class="form-label">Category</label>
                                <select class="form-select category-select" id="add_income_category_id" name="subcategory_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($liabilityCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-6 mb-3">
                                <label for="add_income_sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-select subcategory-select" id="add_income_sub_category_id" name="subsubcategory_id" required>
                                    <option value="">Select Sub Category</option>
                                </select>
                            </div> --}}

                            <div class="col-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
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
                
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                            </div>
                        </div>
                    </div>
            
                    <!-- Step 2: User Details -->
                    <div id="step2" style="display: none;">
                        <div class="row">
                            <h4>Step 2: User Details</h4>

                            

                            <div class="col-6 mb-3">
                                <label>Photo</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                            @php
                                $fields = [
                                    'user_name','national_id', 'mobile', 'email', 'father_name', 'father_mobile',
                                    'mother_name', 'mother_mobile', 'spouse_name', 'spouse_mobile',
                                    'present_address', 'permanent_address',
                                    
                                ];
                            @endphp
                
                            @foreach($fields as $field)
                                <div class="col-6 mb-3">
                                    <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                    <input type="{{ in_array($field, ['email', 'entry_date']) ? $field == 'entry_date' ? 'date' : 'email' : 'text' }}" 
                                        name="{{ $field }}" class="form-control">
                                </div>
                            @endforeach

                            
                
                            <div class="col-12 row mx-0 my-3">
                    
                                <div class="col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="send_sms" value="1" id="sendSms">
                                    <label class="form-check-label" for="sendSms">SMS Enabled</label>
                                </div>
                    
                                <div class="col-6 form-check">
                                    <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmail">
                                    <label class="form-check-label" for="sendEmail">Email Enabled</label>
                                </div>
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

            $('#fullscreenLoader').fadeIn();

            $.ajax({
                url: "{{ route('liability.store') }}",
                method: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#fullscreenLoader').fadeOut();
                    $('#successMessage').text(response.message); // Set dynamic success message
                    $('#successModal').modal('show');

                    $('#addIncomeCategoryForms')[0].reset();
                    $('#addmodals').modal('hide');

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
                    $('#fullscreenLoader').fadeOut();
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
    document.querySelectorAll('.contact-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const selectedOption = this.options[this.selectedIndex];
    
            const name = selectedOption.dataset.name || '';
            const mobile = selectedOption.dataset.mobile || '';
            const email = selectedOption.dataset.email || '';

            const nationalId = selectedOption.dataset.national_id || '';
            const fatherName = selectedOption.dataset.father_name || '';
            const fatherMobile = selectedOption.dataset.father_mobile || '';
            const motherName = selectedOption.dataset.mother_name || '';
            const motherMobile = selectedOption.dataset.mother_mobile || '';
            const spouseName = selectedOption.dataset.spouse_name || '';
            const spouseMobile = selectedOption.dataset.spouse_mobile || '';
            const presentAddress = selectedOption.dataset.present_address || '';
            const permanentAddress = selectedOption.dataset.permanent_address || '';

    
            const userNameInput = form.querySelector('input[name="user_name"]');
            const mobileInput = form.querySelector('input[name="mobile"]');
            const emailInput = form.querySelector('input[name="email"]');
            const photoInput = form.querySelector('input[name="photo"]');

            const nationalIdInput = form.querySelector('input[name="national_id"]');
            const fatherNameInput = form.querySelector('input[name="father_name"]');
            const fatherMobileInput = form.querySelector('input[name="father_mobile"]');
            const motherNameInput = form.querySelector('input[name="mother_name"]');
            const motherMobileInput = form.querySelector('input[name="mother_mobile"]');
            const spouseNameInput = form.querySelector('input[name="spouse_name"]');
            const spouseMobileInput = form.querySelector('input[name="spouse_mobile"]');
            const presentAddressInput = form.querySelector('input[name="present_address"]');
            const permanentAddressInput = form.querySelector('input[name="permanent_address"]');

            const sendSmsCheckbox = form.querySelector('input[name="send_sms"]');
            const sendEmailCheckbox = form.querySelector('input[name="send_email"]');
            const smsOption = selectedOption.dataset.sms_option;
            const sendEmail = selectedOption.dataset.send_email;
    
            if (this.value && name) {
                userNameInput.value = name;
                userNameInput.readOnly = true;
    
                if(mobileInput){
                    mobileInput.value = mobile;
                    mobileInput.readOnly = true;
                }
                
                if(emailInput){
                    emailInput.value = email;
                    emailInput.readOnly = true;
                }
                
    
                if (photoInput) {
                    photoInput.disabled = true;
                    photoInput.value = ''; // Optional: for browsers that allow resetting
                }

                if(nationalId){
                    nationalIdInput.value = nationalId;
                    nationalIdInput.readOnly = true;
                }
                
                if(fatherName){
                fatherNameInput.value = fatherName;
                fatherNameInput.readOnly = true;
                }

                if(fatherMobile){
                fatherMobileInput.value = fatherMobile;
                fatherMobileInput.readOnly = true;
                }

                if(motherName){
                motherNameInput.value = motherName;
                motherNameInput.readOnly = true;
                }

                if(motherMobile){
                motherMobileInput.value = motherMobile;
                motherMobileInput.readOnly = true;
                }

                if(spouseName){
                spouseNameInput.value = spouseName;
                spouseNameInput.readOnly = true;
                }

                if(spouseMobile){
                spouseMobileInput.value = spouseMobile;
                spouseMobileInput.readOnly = true;
                }

                if(permanentAddress){
                presentAddressInput.value = permanentAddress;
                presentAddressInput.readOnly = true;
                }

                if(permanentAddress){
                permanentAddressInput.value = permanentAddress;
                permanentAddressInput.readOnly = true;
                }

                if (sendSmsCheckbox && typeof smsOption !== 'undefined') {
                    sendSmsCheckbox.checked = smsOption === '1';
                    sendSmsCheckbox.readOnly = smsOption === '1';
                }

                if (sendEmailCheckbox && typeof sendEmail !== 'undefined') {
                    sendEmailCheckbox.checked = sendEmail === '1';
                    sendEmailCheckbox.readOnly = sendEmail === '1';
                }
            } else {
                userNameInput.value = '';
                userNameInput.readOnly = false;
    
                mobileInput.value = '';
                mobileInput.readOnly = false;
    
                emailInput.value = '';
                emailInput.readOnly = false;
    
                if (photoInput) {
                    photoInput.disabled = false;
                }

                nationalIdInput.value = '';
                nationalIdInput.readOnly = false;

                fatherNameInput.value = '';
                fatherNameInput.readOnly = false;

                fatherMobileInput.value = '';
                fatherMobileInput.readOnly = false;

                motherNameInput.value = '';
                motherNameInput.readOnly = false;

                motherMobileInput.value = '';
                motherMobileInput.readOnly = false;

                spouseNameInput.value = '';
                spouseNameInput.readOnly = false;

                spouseMobileInput.value = '';
                spouseMobileInput.readOnly = false;

                presentAddressInput.value = '';
                presentAddressInput.readOnly = false;

                permanentAddressInput.value = '';
                permanentAddressInput.readOnly = false;

                if (sendSmsCheckbox) {
                    sendSmsCheckbox.checked = false;
                    sendSmsCheckbox.readOnly = false;
                }

                if (sendEmailCheckbox) {
                    sendEmailCheckbox.checked = false;
                    sendEmailCheckbox.readOnly = false;
                }
            }
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