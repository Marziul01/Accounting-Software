@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Investment</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                    data-bs-target="#addmodals">Add New Investment</button>
            </div>
            <div class="card-body  text-nowrap">
                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Investment Category</th>
                                
                                <th>Name</th>
                                <th>Description</th>
                                <th>Initial Investment</th>
                                <th>Investment Condition</th>
                                <th>Investment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if($investments->isNotEmpty()) 
                            @foreach ($investments as $investment )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $investment->investmentCategory->name ?? 'Investment Category Not Assigned' }} - ( {{ $investment->investmentSubCategory->name ?? 'Investment Sub Category Not Assigned' }} ) </td>
                                
                                <td>{{ $investment->name }}</td>
                                <td>{{ $investment->description ?? 'N/A' }}</td>
                                @php
                                    
                                    $transactions = $investmentTransactions->where('investment_id', $investment->id);

                                    $totalDeposits = $transactions->where('transaction_type', 'Deposit')->sum('amount');
                                    $totalWithdrawals = $transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                    $investIncome = $investment->investIncome->sum('amount');
                                    $investExpense = $investment->investExpense->sum('amount');

                                    $initialAmount = $transactions->first()->amount ?? 0;
                                    $currentAmount = $totalDeposits - $totalWithdrawals - $investExpense;
                                @endphp

                                <td>{{ number_format($initialAmount, 2) }} Tk</td>
                                <td>
                                    {{ number_format($currentAmount, 2) }} Tk

                                </td>



                                <td>{{ \Carbon\Carbon::parse($investment->date)->format('d M, Y') ?? 'N/A' }}</td> <!-- ✅ Income Date -->
                                <td>
                                    <div class="d-flex align-items-center gap-1 cursor-pointer">
                                        
                                    </div>

                                    <div class="dropdown" data-bs-boundary="viewport">  <!-- 👈  only this line added -->
                                        <button class="btn p-0 btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" style=" padding: 0px 5px !important ;">
                                            Actions <i class="bx bx-dots-vertical-rounded" style="font-size: 20px !important;"></i> 
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cardOpt6">
                                            <div class="d-flex flex-column gap-1 cursor-pointer">
                                                <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                data-bs-target="#updateModal{{ $investment->id }}"><i
                                                        class="bx bx-wallet me-1"></i> Add New Transaction</a>
                                                        <a class=" btn btn-sm btn-outline-primary" href="{{ route('seeInvestmentTrans' ,$investment->slug ) }}" ><i
                                                        class="bx bx-wallet me-1"></i> See All Transactions</a>
                                                <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" href="#" data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $investment->id }}"><i class="bx bx-edit-alt me-1"></i> Edit</a>

                                                    <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                data-bs-target="#incomeModal{{ $investment->id }}"><i
                                                        class="bx bx-wallet me-1"></i> Gain from Investment</a>
                                                        <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                data-bs-target="#expenseModal{{ $investment->id }}"><i
                                                        class="bx bx-wallet me-1"></i> Loss from Investment</a>
                                                        <a class=" btn btn-sm btn-outline-primary" href="{{ route('seeInvestmentsinex' ,$investment->slug ) }}" ><i
                                                        class="bx bx-wallet me-1"></i> See All Gain/Loss</a>

                                                <form action="{{ route('investment.destroy', $investment->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div> 
                                </td>
                            </tr>
                            
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7" class="text-center">No Investment found.</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Investment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addIncomeCategoryForms">
                    @csrf
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control name-input" id="name" name="name"  required>
                           
                        </div>
                        <div class="mb-3 d-none" >
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control slug-output" id="slug" name="slug"  readonly>
                        </div>
                        <div class="mb-3">
                            <label for="add_income_category_id" class="form-label">Category</label>
                            <select class="form-select category-select" id="add_income_category_id" name="investment_category_id" required>
                                <option value="">Select Category</option>
                                @foreach ($investmentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_income_sub_category_id" class="form-label">Sub Category</label>
                            <select class="form-select subcategory-select" id="add_income_sub_category_id" name="investment_sub_category_id" required>
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="income_date" class="form-label">investment Date</label>
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
      

    @if($investments->isNotEmpty())
        @foreach ($investments as $investment )

        <div class="modal fade" id="editModal{{ $investment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Investment </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $investment->id }}" action="{{ route('investment.update', $investment->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control name-input" id="name" name="name" value="{{ $investment->name }}" required>
                               
                            </div>
                            <div class="mb-3 d-none">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control slug-output" id="slug" name="slug" value="{{ $investment->slug }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="edit_income_category_id" class="form-label">Category</label>
                                <select class="form-select category-select" id="edit_income_category_id{{ $investment->id }}" name="investment_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($investmentCategories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ $category->id == $investment->investment_category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                
                            </div>
                            <div class="mb-3">
                                <label for="edit_income_sub_category_id" class="form-label">Sub Category</label>
                                <select class="form-select subcategory-select" 
                                        id="edit_income_sub_category_id{{ $investment->id }}" 
                                        name="investment_sub_category_id" 
                                        data-selected="{{ $investment->investment_sub_category_id }}" 
                                        required>
                                    <option value="">Select Sub Category</option>
                                </select>

                            </div>
                            
                            <div class="mb-3">
                                <label for="income_date" class="form-label">investment Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ $investment->date }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="Description" class="form-label">Description</label>
                                <textarea class="form-control" id="Description" name="description" rows="3">{{ $investment->description }}</textarea>
                                
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

    @if($investments->isNotEmpty())
        @foreach ($investments as $investment )

        <div class="modal fade" id="updateModal{{ $investment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Investment Transaction </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                        <div class="modal-body">
                            <form id="assetForms{{ $investment->id }}">
                                @csrf
                                <div class="row">
                                        <input type="hidden" name="investment_id" value="{{ $investment->id }}">
                                        <div class="col-12 mb-3">
                                            <label>Transation Type</label>
                                            <select name="transaction_type" id="" class="form-select">
                                                <option value="Deposit">জমা </option>
                                                <option value="Withdraw">উত্তোলন</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label>Amount</label>
                                            <input type="number" name="amount" class="form-control" required >
                                        </div>
                                    
                                        <div class="col-12 mb-3">
                                            <label>Transaction Date</label>
                                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required >
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"> </textarea>
                                        </div>
    
                                        <div class="col-12 mb-3">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                </div>
                                
                            </form>
                        </div>
                </div>  
            </div>
        </div>
        @endforeach
    @endif


    @if($investments->isNotEmpty())
        @foreach ($investments as $investment )

        <div class="modal fade" id="seeModal{{ $investment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> See All Investment Transactions </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Transaction Type</th>
                                            <th>Amount</th>
                                            <th>Transaction Date </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @if($investmentTransactions->where('investment_id', $investment->id )->isNotEmpty())
                                        @foreach ($investmentTransactions->where('investment_id', $investment->id ) as $investmentTransaction )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $investmentTransaction->transaction_type }}</td>
                                            <td>{{ $investmentTransaction->amount }}</td>
                                            <td>{{ $investmentTransaction->transaction_date }} </td>
                                            
                                            
                                            <td>
                                                <div class="d-flex align-items-center gap-1 cursor-pointer">
                                                        <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                        data-bs-target="#edittranModal{{ $investmentTransaction->id }}"><i
                                                                class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <form action="{{ route('investmenttransaction.destroy', $investmentTransaction->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" ><i
                                                                class="bx bx-trash me-1"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr> 
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4" class="text-center">No investment Transaction found.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>  
            </div>
        </div>
        @endforeach
    @endif

    @if($investmentTransactions->isNotEmpty())
        @foreach ($investmentTransactions as $investmentTransaction )

        <div class="modal fade" id="edittranModal{{ $investmentTransaction->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Investment Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editTransCategoryForms{{ $investmentTransaction->id }}" action="{{ route('investmenttransaction.update', $investmentTransaction->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="investment_id" value="{{ $investmentTransaction->investment_id }}">
                                <div class="col-12 mb-3">
                                    <label>Transation Type</label>
                                    <select name="transaction_type" id="" class="form-select">
                                        <option value="Deposit" {{ $investmentTransaction->transaction_type == 'Deposit' ? 'Selected' : '' }} >জমা </option>
                                        <option value="Withdraw" {{ $investmentTransaction->transaction_type == 'Withdraw' ? 'Selected' : '' }}>উত্তোলন</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label>Amount</label>
                                    <input type="number" name="amount" class="form-control" required value="{{ $investmentTransaction->amount }}">
                                </div>
                            
                                <div class="col-12 mb-3">
                                    <label>Transaction Date</label>
                                    <input type="date" name="transaction_date" class="form-control" required value="{{ $investmentTransaction->transaction_date }}" >
                                </div>

                                <div class="col-12 mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"> {{ $investmentTransaction->description }} </textarea>
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
        @endforeach
    @endif



    @if($investments->isNotEmpty())
        @foreach ($investments as $investment )

        <div class="modal fade" id="incomeModal{{ $investment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Gain from Investment </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="investmentIncomeForms{{ $investment->id }}">
                        @csrf
                        
                        <div class="modal-body">
                            <input type="hidden" name="investment_id" value="{{ $investment->id }}">
                            <input type="hidden" name="category_id" value="13">
                            <input type="hidden" name="subcategory_id" value="{{ $investment->investment_category_id == 4 ? '8' : '9' }}">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_date" class="form-label">Income Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
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
        @endforeach
    @endif


    @if($investments->isNotEmpty())
        @foreach ($investments as $investment )

        <div class="modal fade" id="expenseModal{{ $investment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Loss from Investment </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="investmentexpenseForms{{ $investment->id }}">
                        @csrf
                        
                        <div class="modal-body">
                            <input type="hidden" name="investment_id" value="{{ $investment->id }}">
                            <input type="hidden" name="category_id" value="7">
                            <input type="hidden" name="subcategory_id" value="{{ $investment->investment_category_id == 4 ? '14' : '15' }}">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="income_date" class="form-label">Expense Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
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
        @endforeach
    @endif




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

@if ($investments->isNotEmpty())
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
    @foreach ($investmentCategories as $investmentcategory)
        attachSlugListener('editModal{{ $investmentcategory->id }}');
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
                url: "{{ route('investment.store') }}",
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
                let url = "{{ route('get.investmentsubcategories', ':id') }}".replace(':id', selectedCategoryId);
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
            let url = "{{ route('get.investmentsubcategories', ':id') }}".replace(':id', categoryId);
    
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
    $(document).ready(function () {
        $('form[id^="editTransCategoryForms"] button[type="submit"]').on('click', function (e) {
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
                    $('#edittranModal' + response.id).modal('hide');

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

        
            $('form[id^="assetForms"] button[type="submit"]').on('click', function (e) {
            e.preventDefault();
           

            toastr.clear();

            let form = $(this).closest('form')[0]; // ✅ get the actual form element
            let formData = new FormData(form);         // ✅ pass the form here

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            $.ajax({
                url: "{{ route('investmenttransaction.store') }}",
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

                    form.reset();
                    $('#updateModal' + response.id).modal('hide');

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
        $(function () {

            // grab Laravel’s CSRF token once
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // delegate submit handler to every form id that starts with investmentIncomeForms
            $(document).on('submit', 'form[id^="investmentIncomeForms"]', function (e) {
                e.preventDefault();

                const $form = $(this);
                const $btn  = $form.find('button[type=submit]');
                $btn.prop('disabled', true);

                // build payload
                const data = $form.serialize();

                $.post('{{ route('investment-income.store') }}', data)
                    .done(function (res) {

                        // 🔔 Toastr success
                        toastr.success(res.message);

                        // hide the current modal
                        $form.closest('.modal').modal('hide');

                        // show success modal, update its message
                        $('#successMessage').text(res.message);
                        $('#successModal').modal('show');

                        // auto-reload after 2 s
                        setTimeout(function () { location.reload(); }, 2000);
                    })
                    .fail(function (xhr) {

                        // 422 = validation; 403/500 handled similarly
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, msgs) {
                                toastr.error(msgs[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Server error');
                        }
                    })
                    .always(function () {
                        $btn.prop('disabled', false);
                    });
            });
        });
</script>

    
    
<script>
$(function () {

    // grab Laravel’s CSRF token once
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // delegate submit handler to every form id that starts with investmentIncomeForms
    $(document).on('submit', 'form[id^="investmentexpenseForms"]', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $btn  = $form.find('button[type=submit]');
        $btn.prop('disabled', true);

        // build payload
        const data = $form.serialize();

        $.post('{{ route('investment-expense.store') }}', data)
            .done(function (res) {

                // 🔔 Toastr success
                toastr.success(res.message);

                // hide the current modal
                $form.closest('.modal').modal('hide');

                // show success modal, update its message
                $('#successMessage').text(res.message);
                $('#successModal').modal('show');

                // auto-reload after 2 s
                setTimeout(function () { location.reload(); }, 2000);
            })
            .fail(function (xhr) {

                // 422 = validation; 403/500 handled similarly
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, msgs) {
                        toastr.error(msgs[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Server error');
                }
            })
            .always(function () {
                $btn.prop('disabled', false);
            });
    });
});
</script>

@endsection
