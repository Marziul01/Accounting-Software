@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Current Asset</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                    data-bs-target="#addmodals">Add New Current Asset </button>
            </div>
            <div class="card-body position-relative ">
                <div class="table-responsive text-nowrap">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>

                                @if($categorysettings->asset_category_table == 2)
                                <th>Asset Category</th>
                                @endif

                                @if($categorysettings->asset_name_table == 2)
                                <th>Asset Name</th>
                                @endif

                                <th>Assets Issued Name </th>
                                <th>Amount</th>
                                <th>Description</th>
                                
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if($assets->isNotEmpty())
                            @foreach ($assets as $asset )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                
                                @if($categorysettings->asset_category_table == 2)
                                <td>{{ $asset->category->name ?? 'Asset Category Not Assigned' }} - ( {{ $asset->subcategory->name ?? 'Asset Category Not Assigned' }} ) </td>
                                @endif
                                
                                @if($categorysettings->asset_name_table == 2)
                                <td>{{ $asset->name }}</td>
                                @endif

                                <td>{{ $asset->user_name }} </td>
                                @php

                                    $totalDeposits = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                    $totalWithdrawals = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                    $initialAmount = $asset->transactions->first()->amount ?? 0;
                                    $currentAmount = $totalDeposits - $totalWithdrawals;
                                @endphp
                                <td>
                                     {{ number_format($currentAmount, 2) }} Tk
                               
                                </td>
                                <td>{{ $asset->description ?? 'N/A' }}</td>
                                
                                <td>
                                    <div class="dropdown" data-bs-boundary="viewport">  <!-- 👈  only this line added -->
                                        <button class="btn p-0 btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false" style=" padding: 0px 5px !important ;">
                                            Actions <i class="bx bx-dots-vertical-rounded" style="font-size: 20px !important;"></i> 
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cardOpt6">
                                            <div class="d-flex flex-column gap-1 cursor-pointer">
                                                <a class=" btn btn-sm btn-primary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                    data-bs-target="#updateModal{{ $asset->id }}"><i
                                                    class="bx bx-wallet me-1"></i> Update Asset Transaction
                                                </a>
                                                <a class=" btn btn-sm btn-outline-primary d-block" href="{{ route('seeAssetTrans', $asset->slug) }}" ><i
                                                    class="bx bx-wallet me-1"></i> See All Asset Transactions
                                                </a>
                                                <a class=" btn btn-sm btn-outline-secondary d-block" href="" data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $asset->id }}"><i
                                                    class="bx bx-show me-1"></i> See Details
                                                </a>
                                                <a class=" btn btn-sm btn-outline-secondary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $asset->id }}"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form action="{{ route('asset.destroy', $asset->id) }}" method="POST" class="w-100">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 delete-confirm {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" ><i
                                                        class="bx bx-trash me-1"></i> Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>   
                                </td>
                            </tr> 
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8" class="text-center">No asset found.</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add New Current Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                <form id="addIncomeCategoryForms">
                    @csrf
            
                    <!-- Step 1: Asset Details -->
                    <div id="step1">
                        <div class="row">
                            <h4>Step 1: Asset Details</h4>
            
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
                                            data-permanent_address="{{ $user->permanent_address ?? '' }}">
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

                            
                            <input type="hidden" value="4" name="category_id">
                
                            <div class="col-6 mb-3">
                                <label for="add_income_category_id" class="form-label">Category</label>
                                <select class="form-select category-select" id="add_income_category_id" name="subcategory_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($assetCategories as $category)
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

    <!-- / Modal -->
      

    @if($assets->isNotEmpty())
        @foreach ($assets as $asset )

        <div class="modal fade" id="editModal{{ $asset->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Asset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editIncomeCategoryForms{{ $asset->id }}" action="{{ route('asset.update', $asset->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Step 1: Asset Details -->
                                <div id="step11{{ $asset->id }}">
                                    <div class="row">
                                        <h4>Step 1: Asset Details</h4>
                        
                                        <div class="col-6 mb-3 d-none">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control name-input" required value="{{ $asset->name }}">
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
                                                        {{ $asset->contact_id == $user->id ? 'selected' : '' }} >
                                                        {{ $user->name }}
                                                    </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                            
                                        <div class="col-6 mb-3 d-none">
                                            <label>Slug</label>
                                            <input type="text" name="slug" class="form-control slug-output" required value="{{ $asset->slug }}" >
                                        </div>
                            

                                        <div class="col-6 mb-3">
                                            <label>Entry Date</label>
                                            <input type="date" name="entry_date" class="form-control" required value="{{ $asset->entry_date ? \Carbon\Carbon::parse($asset->entry_date)->format('Y-m-d') : '' }}"
                                            >
                                        </div>
                            
                                        
                                        <input type="hidden" value="{{ $asset->category_id }}" name="category_id">
                            
                                        <div class="col-12 mb-3">
                                            <label for="add_income_category_id" class="form-label">Category</label>
                                            <select class="form-select category-select" id="edit_income_category_id{{ $asset->id }}" name="subcategory_id" required>
                                                <option value="">Select Category</option>
                                                @foreach ($assetCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ $category->id == $asset->subcategory_id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="col-6 mb-3">
                                            <label for="add_income_sub_category_id" class="form-label">Sub Category</label>
                                            <select class="form-select subcategory-select" 
                                                    id="edit_income_sub_category_id{{ $asset->id }}" 
                                                    name="subsubcategory_id" 
                                                    data-selected="{{ $asset->subsubcategory_id }}" 
                                                    required>
                                                <option value="">Select Sub Category</option>
                                            </select>
                                        </div> --}}

                                        <div class="col-12 mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"> {{ $asset->description }} </textarea>
                                        </div>
                            
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary" onclick="nextStep1({{ $asset->id }})">Next</button>
                                        </div>
                                    </div>
                                </div>
                        
                                <!-- Step 2: User Details -->
                                <div id="step21{{ $asset->id }}" style="display: none;">
                                    <div class="row">
                                        <h4>Step 2: User Details</h4>

                                        
            
                                        <div class="col-6 mb-3">
                                            <label>Photo</label>
                                            <input type="file" name="photo" class="form-control">
                                            <p class="my-2">Previous Image</p>
                                            <img @if($asset->photo) src="{{ asset($asset->photo) }}" @else src="{{ asset('admin-assets/img/nophoto.jpg') }}"  @endif width="100px" height="100px" style="object-fit: fill" alt="">
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
                                                    name="{{ $field }}" class="form-control" value=" {{ $asset->$field }} ">
                                            </div>
                                        @endforeach




                                        {{-- <div class="col-12 mb-3">
                                            <label>Photo</label>
                                            <input type="file" name="photo" class="form-control">
                                            <p class="my-2">Previous Image</p>
                                            <img @if($asset->photo) src="{{ asset($asset->photo) }}" @else src="{{ asset('admin-assets/img/nophoto.jpg') }}"  @endif width="100px" height="100px" style="object-fit: fill" alt="">
                                        </div>

                                        @php
                                            $fields = [
                                                'national_id', 'mobile', 'email', 'father_name', 'father_mobile',
                                                'mother_name', 'mother_mobile', 'spouse_name', 'spouse_mobile',
                                                'present_address', 'permanent_address', 'user_name', 'entry_date',
                                                'contact_id'
                                            ];
                                        @endphp
                            
                                        @foreach($fields as $field)
                                            <div class="col-6 mb-3">
                                                <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                <input type="{{ in_array($field, ['email', 'entry_date']) ? $field == 'entry_date' ? 'date' : 'email' : 'text' }}" 
                                                    name="{{ $field }}" class="form-control" value=" {{ $asset->$field }} " >
                                            </div>
                                        @endforeach --}}

                                        
                            
                                        <div class="col-12 row mx-0 mb-3">
                                
                                            <div class="col-6 form-check">
                                                <input class="form-check-input" type="checkbox" name="send_sms" value="1" id="sendSms1"
                                                    {{ $asset->send_sms == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sendSms1">SMS Enabled</label>
                                            </div>
                                            
                                            <div class="col-6 form-check">
                                                <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmail1"
                                                    {{ $asset->send_email == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sendEmail1">Email Enabled</label>
                                            </div>                                            
                                        </div>

                                        <div class="col-12 mb-3">
                                            <button type="button" class="btn btn-secondary" onclick="prevStep1({{ $asset->id }})">Back</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>


                        </div>
                        {{-- <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div> --}}
                        
                    </form>
                    
                </div>  
            </div>
        </div>
        @endforeach
    @endif
    <!-- / Modal -->

    @if($assets->isNotEmpty())
        @foreach ($assets as $asset )

        <div class="modal fade" id="viewModal{{ $asset->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">See Asset Assinged User Details </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                        <div class="modal-body">
                                    <div class="row">
                                        
                                        <div class="col-12 mb-3">
                                            <img @if($asset->photo) src="{{ asset($asset->photo) }}" @else src="{{ asset('admin-assets/img/nophoto.jpg') }}"  @endif width="100px" height="100px" style="object-fit: fill ;  border-radius: 50%;" alt="">
                                        </div>

                                        @php
                                            $fields = [
                                                'user_name','national_id', 'mobile', 'email', 'father_name', 'father_mobile',
                                                'mother_name', 'mother_mobile', 'spouse_name', 'spouse_mobile',
                                                'present_address', 'permanent_address'
                                            ];
                                        @endphp
                            
                                        @foreach($fields as $field)
                                            <div class="col-6 mb-3">
                                                <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                <input type="{{ in_array($field, ['email', 'entry_date']) ? $field == 'entry_date' ? 'date' : 'email' : 'text' }}" 
                                                    name="{{ $field }}" class="form-control" value=" {{ $asset->$field }} " readonly >
                                            </div>
                                        @endforeach

                                    </div>
                                </div>


                        </div>
                        
                    
                </div>  
            </div>
        </div>
        @endforeach
    @endif

    @if($assets->isNotEmpty())
        @foreach ($assets as $asset )

        <div class="modal fade" id="updateModal{{ $asset->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Update Your Asset Transaction </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                        <div class="modal-body">
                            <form id="assetForms{{ $asset->id }}">
                                @csrf
                                <div class="row">
                                        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
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
                                            <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}">
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


    {{-- @if($assets->isNotEmpty())
        @foreach ($assets as $asset )

        <div class="modal fade" id="seeModal{{ $asset->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> See All Asset Transactions </h5>
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
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @if($assetTransactions->where('asset_id', $asset->id )->isNotEmpty())
                                        @foreach ($assetTransactions->where('asset_id', $asset->id ) as $assetTransaction )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $assetTransaction->transaction_type }}</td>
                                            <td>{{ $assetTransaction->amount }}</td>
                                            <td>{{ $assetTransaction->transaction_date }} </td>
                                            <td>{{ $assetTransaction->description }} </td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center gap-1 cursor-pointer">
                                                        <a class=" btn btn-sm btn-outline-secondary {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" href="" data-bs-toggle="modal"
                                                        data-bs-target="#edittranModal{{ $assetTransaction->id }}"><i
                                                                class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <form action="{{ route('assettransaction.destroy', $assetTransaction->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" ><i
                                                                class="bx bx-trash me-1"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr> 
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4" class="text-center">No Asset Transaction found.</td>
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
    @endif --}}

    {{-- @if($assetTransactions->isNotEmpty())
        @foreach ($assetTransactions as $assetTransaction )

        <div class="modal fade" id="edittranModal{{ $assetTransaction->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Asset Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editTransCategoryForms{{ $assetTransaction->id }}" action="{{ route('assettransaction.update', $assetTransaction->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="asset_id" value="{{ $assetTransaction->asset_id }}">
                                <div class="col-12 mb-3">
                                    <label>Transation Type</label>
                                    <select name="transaction_type" id="" class="form-select">
                                        <option value="Deposit" {{ $assetTransaction->transaction_type == 'Deposit' ? 'Selected' : '' }} >জমা </option>
                                        <option value="Withdraw" {{ $assetTransaction->transaction_type == 'Withdraw' ? 'Selected' : '' }}>উত্তোলন</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label>Amount</label>
                                    <input type="number" name="amount" class="form-control" required value="{{ $assetTransaction->amount }}">
                                </div>
                            
                                <div class="col-12 mb-3">
                                    <label>Transaction Date</label>
                                    <input type="date" name="transaction_date" class="form-control" required value="{{ $assetTransaction->transaction_date }}" >
                                </div>

                                <div class="col-12 mb-3">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"> {{ $assetTransaction->description }} </textarea>
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
    @endif --}}

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

@if ($assets->isNotEmpty())
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
    @foreach ($assets as $assetsubsubcategory)
        attachSlugListener('editModal{{ $assetsubsubcategory->id }}');
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
                url: "{{ route('asset.store') }}",
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
                let url = "{{ route('get.currentassetsubcategories', ':id') }}".replace(':id', selectedCategoryId);
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
            let url = "{{ route('get.currentassetsubcategories', ':id') }}".replace(':id', categoryId);
    
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
                url: "{{ route('assettransaction.store') }}",
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
    
            if (this.value && name ) {
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

@endsection
