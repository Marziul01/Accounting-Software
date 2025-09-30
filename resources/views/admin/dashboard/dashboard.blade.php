@extends('admin.master')

@section('content')
    <script>
        const monthlyData = @json($monthlyData); // from Laravel controller
        const revenueData = @json($monthlyComparisonData);
    </script>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-8 mb-4 order-0">
                <div class="card contact-card">
                    <div class="d-flex align-items-start row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3">Hello, Welcome again <span
                                        class="text-uppercase">{{ Auth::user()->name }}</span>! 🎉</h5>
                                <h6 class="text-secondary mb-3">
                                    @php
                                        use Carbon\Carbon;

                                        $now = Carbon::now();
                                        $hour = $now->format('H');

                                        if ($hour >= 3 && $hour < 12) {
                                            $greeting = 'Good Morning';
                                        } elseif ($hour >= 12 && $hour < 18) {
                                            $greeting = 'Good Afternoon';
                                        } elseif ($hour >= 18 && $hour < 20) {
                                            $greeting = 'Good Evening';
                                        } else {
                                            $greeting = 'Good Night';
                                        }
                                    @endphp

                                    <p>{{ $greeting }}. Today {{ $now->format('d F Y, l') }}</p>
                                </h6>
                                {{-- <p class="mb-6">
                                    You have done 72% more sales today.<br />Check your new badge in your profile.
                                </p> --}}

                                <a href="{{ route('profile') }}" class="btn btn-sm btn-outline-primary">View Profile</a>

                                <p class="mt-3">Upcoming Events :</p>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($finalOccasions as $event)
                                        <span class="badge btn-secondary d-flex align-items-center flex-wrap gap-1">
                                            <strong class="me-1">{{ $event['title'] }}</strong> 
                                            - {{ \Carbon\Carbon::parse($event['date'])->format('d F Y') }}
                                            
                                            @if (isset($event['type']))
                                                <span class="ms-1">({{ $event['type'] }})</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left d-none d-md-block">
                            <div class="card-body pb-0 px-0 px-md-6">
                                <img src="{{ asset('admin-assets') }}/assets/img/illustrations/man.png" height="230px"
                                    class="scaleX-n1-rtl" alt="View Badge User" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-body contact-card d-flex flex-wrap flex-row mt-4 ">
                    <h6 class="d-block w-100 mx-2">Quick Posting :</h6>
                    <div class="quickdivs">
                        <button type="button" class="btn btn-primary {{ Auth::user()->access->income != 2 ? 'disabled' : '' }}"
                            id="loadIncomeModal" data-url="{{ route('admin.income.modal') }}">Add New Income</button>
                    </div>
                    <div class="quickdivs">
                        <button type="button" class="btn btn-primary {{ Auth::user()->access->expense != 2 ? 'disabled' : '' }}"
                            id="loadExpenseModal" data-url="{{ route('admin.expense.modal') }}">Add New Expense</button>
                    </div>
                    <div class="quickdivs">
                        <button class="btn btn-primary" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            Asset Options
                        </button>
                        <div class="dropdown-menu dropdown-menu-end bg-menu-theme" aria-labelledby="cardOpt3">
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetModal" data-url="{{ route('admin.currentasset.modal') }}">Add New Current Asset</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetTransactionModal"
                                data-url="{{ route('admin.currentassettransaction.modal') }}">Add New Current Asset Transaction</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                                id="loadfixedAssetModal" data-url="{{ route('admin.fixedasset.modal') }}">Add New Fixed Asset</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                                id="loadfixedAssetTransactionModal"
                                data-url="{{ route('admin.fixedassettransaction.modal') }}">Add New Fixed Asset Transaction</button>
                        </div>
                    </div>
                    <div class="quickdivs">
                        <button class="btn btn-primary" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            Liability Options
                        </button>
                        <div class="dropdown-menu dropdown-menu-end bg-menu-theme" aria-labelledby="cardOpt4">
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetModal" data-url="{{ route('admin.currentliability.modal') }}">Add New Short Term Liability</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetTransactionModal"
                                data-url="{{ route('admin.currentliabilitytransaction.modal') }}">Add New Short Term Liability Transaction</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                                id="loadfixedAssetModal" data-url="{{ route('admin.fixedliability.modal') }}">Add New Long Term Liability</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                                id="loadfixedliabilityTransactionModal"
                                data-url="{{ route('admin.fixedliabilitytransaction.modal') }}">Add New Long Term Liability Transaction</button>
                        </div>
                    </div>
                    <div class="quickdivs">
                        <button class="btn btn-primary" type="button" id="cardOpt5" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            Investment Options
                        </button>
                        <div class="dropdown-menu dropdown-menu-end bg-menu-theme" aria-labelledby="cardOpt5">
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetModal" data-url="{{ route('admin.investment.modal') }}">Add New Investment</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                                id="loadcurrentAssetTransactionModal"
                                data-url="{{ route('admin.investmenttransaction.modal') }}">Add New Investment Transaction</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                                id="loadfixedAssetModal" data-url="{{ route('admin.investmentincome.modal') }}">Add New Investment Gain</button>
                            <button type="button" class=" dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                                id="loadfixedliabilityTransactionModal"
                                data-url="{{ route('admin.investmentloss.modal') }}">Add New Investment Loss</button>
                        </div>
                    </div>
                    <div class="quickdivs">
                        <button type="button" class="btn btn-primary {{ Auth::user()->access->bankbook != 2 ? 'disabled' : '' }}"
                            id="loadExpenseModal" data-url="{{ route('admin.bankbook.modal') }}">Add New Bank Transaction</button>
                    </div>
                    <div id="ModalContainer"></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Income</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['incomes'] }} BDT
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Expenses</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['expenses'] }}
                                    BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Assets</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['assets'] }} BDT
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Liabilities</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['liabilities'] }}
                                    BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Investments</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['investments'] }} BDT
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card h-100 contact-card">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">This Month Bank Amount</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ $currentMonthtotals['bankbooks'] }}
                                    BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-12 mb-3 mt-1">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Incomes</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ number_format($incomes->sum('amount'), 2) }} BDT
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Expenses</p>
                                <h4 class="card-title ttoalsamount mb-0">{{ number_format($expenses->sum('amount'), 2) }}
                                    BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/paypal.png"
                                            alt="paypal" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Assets</p>
                                <h4 class="card-title mb-3 ttoalsamount mb-0">{{ number_format($allassets->sum('amount'), 2) }} BDT
                                </h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/cc-primary.png"
                                            alt="Credit Card" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Liability</p>
                                <h4 class="card-title mb-0 ttoalsamount">
                                    {{ number_format($allliabilities->sum('amount'), 2) }} BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/paypal.png"
                                            alt="paypal" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Investments</p>
                                <h4 class="card-title mb- ttoalsamount">
                                    {{ number_format($alltotalinvestments, 2) }} BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 mb-4">
                        <div class="card contact-card h-100">
                            <div class="card-body p-4">
                                <div class="card-title d-flex align-items-start justify-content-between mb-3">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/cc-primary.png"
                                            alt="Credit Card" class="rounded" />
                                    </div>
                                    
                                </div>
                                <p class="mb-0">Total Bank Amount</p>
                                <h4 class="card-title mb-0 ttoalsamount">{{ number_format($totalbank, 2) }} BDT</h4>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 order-0 mb-6">
                <div class="card contact-card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="mb-1 me-2">Transaction Statistics</h5>

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0 no-colors"> 
                            <tbody>
                                @if ($merged->isNotEmpty())
                                    @foreach ($merged as $trx)
                                        <tr class="p-0">
                                            <td style="width:60%; padding-left: 0px !important ; padding-right: 0px !important " class="p-0">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                                            alt="chart success" class="rounded" />
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $trx->type }}</h6>
                                                        <small>
                                                            {{ $trx->name }}
                                                            {{ $trx->transaction_type ? '- (' . $trx->transaction_type . ')' : '' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td  style="width:40%; padding-left: 0px !important ; padding-right: 0px !important" class="text-end align-middle p-0">
                                                <small class="mb-0">{{ number_format($trx->amount, 2) }} BDT</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 order-1 mb-6">
                <div class="card  contact-card h-100">
                    <div class="card-header nav-align-top">
                        <h5 class="mb-1 me-2 mb-4">Monthly Statistics</h5>
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#income-tab">Income</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#expenses-tab">Expense</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#assets-tab">Asset</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#liabilities-tab">Liability</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="income-tab"> <!-- added show active -->
                                <div id="incomeChart"></div>
                            </div>
                            <div class="tab-pane fade" id="expenses-tab">
                                <div id="expensesChart"></div>
                            </div>
                            <div class="tab-pane fade" id="assets-tab">
                                <div id="assetsChart"></div>
                            </div>
                            <div class="tab-pane fade" id="liabilities-tab">
                                <div id="liabilitiesChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Total Revenue -->
            <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6">
                <div class="card contact-card">
                    <div class="row row-bordered g-0">
                        <div class="col-lg-12">
                            <div class="card-header d-flex align-items-start justify-content-start flex-column">
                                <div class="card-title mb-4">
                                    <h5 class="m-0 me-2">Total Revenue</h5>
                                </div>
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#revenue-income">Income</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#revenue-expense">Expense</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#revenue-asset">Asset</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#revenue-liability">Liability</button>
                                    </li>
                                </ul>
                            </div>
                            

                            <!-- ✅ Single chart container always visible -->
                            <div class="mt-4">
                                <div id="totalRevenueChart"></div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-4 d-flex align-items-center">
                            <div class="card-body px-xl-9">
                                <div class="text-center mb-6">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-primary">
                                            <script>
                                                document.write(new Date().getFullYear() - 1);
                                            </script>
                                        </button>
                                        <button type="button"
                                            class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0);">2021</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);">2020</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);">2019</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div id="growthChart"></div>
                                <div class="text-center fw-medium my-6">62% Company Growth</div>

                                <div class="d-flex gap-3 justify-content-between">
                                    <div class="d-flex">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial rounded-2 bg-label-primary"><i
                                                    class="bx bx-dollar bx-lg text-primary"></i></span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <small>
                                                <script>
                                                    document.write(new Date().getFullYear() - 1);
                                                </script>
                                            </small>
                                            <h6 class="mb-0">$32.5k</h6>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="avatar me-2">
                                            <span class="avatar-initial rounded-2 bg-label-info"><i
                                                    class="bx bx-wallet bx-lg text-info"></i></span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <small>
                                                <script>
                                                    document.write(new Date().getFullYear() - 2);
                                                </script>
                                            </small>
                                            <h6 class="mb-0">$41.2k</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 order-2 mb-6">
                <div class="card contact-card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Bank Transactions</h5>
                        {{-- <div class="dropdown">
                            <button class="btn text-muted p-0" type="button" id="transactionID"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded bx-lg"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                            </div>
                        </div> --}}
                    </div>
                    <div class="card-body pt-4">
                        <table class="table table-borderless mb-0 no-colors">
                            <tbody>
                                @if ($latestBankTransactions->isNotEmpty())
                                    @foreach ($latestBankTransactions as $latestBankTransaction)
                                        <tr class="p-0">
                                            <td style="width:60%; padding-left: 0px !important ; padding-right: 0px !important" class="p-0">
                                                <div class="d-flex align-items-center gap-1">
                                                    <div class="avatar flex-shrink-0 me-1">
                                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet.png"
                                                            alt="User" class="rounded" />
                                                    </div>
                                                    <div>
                                                        <small class="d-block">
                                                            {{ $latestBankTransaction->bankAccount->bank_name }} -
                                                            {{ $latestBankTransaction->bankAccount->account_type }}
                                                        </small>
                                                        <small class="fw-normal mb-0">{{ $latestBankTransaction->transaction_type }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width:40%; padding-left: 0px !important ; padding-right: 0px !important" class="text-end align-middle p-0">
                                                <div class="user-progress d-flex align-items-center gap-1 justify-content-end">
                                                    <small class="fw-normal mb-0">{{ $latestBankTransaction->amount }}</small>
                                                    <span class="text-muted">BDT</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <div id="fullscreenLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="display:flex; justify-content:center; align-items:center; width:100%; height:100%;">
            <div class="loader-custom"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalContainer = document.getElementById("ModalContainer");

        document.querySelectorAll("button[data-url]").forEach(button => {
            button.addEventListener("click", function () {
                const url = this.dataset.url;

                // Step 1: Remove old scripts
                const oldScripts = modalContainer.querySelectorAll("script");
                oldScripts.forEach(script => script.remove());

                // Step 2: Clear the container
                modalContainer.innerHTML = "";

                // Step 3: Fetch the modal content
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        // Step 4: Run new scripts inside modal
                        const newScripts = modalContainer.querySelectorAll("script");
                        newScripts.forEach(oldScript => {
                            const newScript = document.createElement("script");
                            newScript.type = oldScript.type || "text/javascript";

                            if (oldScript.src) {
                                newScript.src = oldScript.src;
                            } else {
                                newScript.textContent = oldScript.textContent;
                            }

                            modalContainer.appendChild(newScript);
                            oldScript.remove();
                        });

                        // Step 5: Show the modal (assuming modal ID is in the fetched HTML)
                        const modal = modalContainer.querySelector(".modal");
                        if (modal) {
                            const bsModal = new bootstrap.Modal(modal);
                            bsModal.show();

                            attachSlugListener(modal);
                            if (modal.querySelector('#investmentSelect')) {
                                attachInvestmentSubcategoryHandler(modal);
                            }

                        }
                    })
                    .catch(error => {
                        console.error("Error loading modal:", error);
                        modalContainer.innerHTML = `<div class="text-danger">Failed to load content.</div>`;
                    });
            });
        });
    });
</script>



@endsection
