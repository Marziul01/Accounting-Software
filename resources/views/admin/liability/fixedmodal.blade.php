<div class="modal fade" id="addmodals">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Long Term Liability</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                <form id="addIncomeCategoryForms">
                    @csrf
            
                    <!-- Step 1: Asset Details -->
                    <div>
                        <div class="row">
                            <h4>Liability Details</h4>
            
                            <div class="col-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control name-input" required>
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
                                <input type="date" name="entry_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>

                            
                            <input type="hidden" value="4" name="category_id">
                
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
                
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
            
                    <!-- Step 2: User Details -->
                    {{-- <div id="step2" style="display: none;">
                        <div class="row">
                            <h4>Step 2: User Details</h4>

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
                                            >
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

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
                                
                            </div>
                        </div>
                    </div> --}}
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
                url: "{{ route('liability.store') }}",
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

