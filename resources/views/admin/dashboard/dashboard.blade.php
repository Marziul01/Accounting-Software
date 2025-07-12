@extends('admin.master')

@section('content')
    <script>
        const monthlyData = @json($monthlyData); // from Laravel controller
        const revenueData = @json($monthlyComparisonData);
    </script>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex flex-wrap mb-4 ">
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
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                    <button type="button" class="dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetModal" data-url="{{ route('admin.currentasset.modal') }}">Add New Current Asset</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetTransactionModal"
                        data-url="{{ route('admin.currentassettransaction.modal') }}">Add New Current Asset Transaction</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                        id="loadfixedAssetModal" data-url="{{ route('admin.fixedasset.modal') }}">Add New Fixed Asset</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->asset != 2 ? 'disabled' : '' }}"
                        id="loadfixedAssetTransactionModal"
                        data-url="{{ route('admin.fixedassettransaction.modal') }}">Add New Fixed Asset Transaction</button>
                </div>
            </div>
            <div class="quickdivs">
                <button class="btn btn-primary" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    Liability Options
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                    <button type="button" class="dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetModal" data-url="{{ route('admin.currentliability.modal') }}">Add New Short Term Liability</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetTransactionModal"
                        data-url="{{ route('admin.currentliabilitytransaction.modal') }}">Add New Short Term Liability Transaction</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                        id="loadfixedAssetModal" data-url="{{ route('admin.fixedliability.modal') }}">Add New Long Term Liability</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->liability != 2 ? 'disabled' : '' }}"
                        id="loadfixedliabilityTransactionModal"
                        data-url="{{ route('admin.fixedliabilitytransaction.modal') }}">Add New Long Term Liability Transaction</button>
                </div>
            </div>
            <div class="quickdivs">
                <button class="btn btn-primary" type="button" id="cardOpt5" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    Investment Options
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt5">
                    <button type="button" class="dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetModal" data-url="{{ route('admin.investment.modal') }}">Add New Investment</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                        id="loadcurrentAssetTransactionModal"
                        data-url="{{ route('admin.investmenttransaction.modal') }}">Add New Investment Transaction</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
                        id="loadfixedAssetModal" data-url="{{ route('admin.investmentincome.modal') }}">Add New Investment Gain</button>
                    <button type="button" class="dropdown-item {{ Auth::user()->access->investment != 2 ? 'disabled' : '' }}"
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

        <div class="row">
            <div class="col-xxl-8 mb-6 order-0">
                <div class="card">
                    <div class="d-flex align-items-start row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3">Hello, Welcome again <span
                                        class="text-uppercase">{{ Auth::user()->name }}</span>! 🎉</h5>
                                {{-- <p class="mb-6">
                                    You have done 72% more sales today.<br />Check your new badge in your profile.
                                </p> --}}

                                <a href="{{ route('profile') }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-6">
                                <img src="{{ asset('admin-assets') }}/assets/img/illustrations/man.png" height="175"
                                    class="scaleX-n1-rtl" alt="View Badge User" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 order-1">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Income</p>
                                <h4 class="card-title mb-3 ttoalsamount">{{ number_format($incomes->sum('amount'), 2) }} BDT
                                </h4>
                                {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Expenses</p>
                                <h4 class="card-title mb-3 ttoalsamount">{{ number_format($expenses->sum('amount'), 2) }}
                                    BDT</h4>
                                {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.42%</small> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Revenue -->
            <div class="col-12 col-xxl-8 order-2 order-md-3 order-xxl-2 mb-6">
                <div class="card">
                    <div class="row row-bordered g-0">
                        <div class="col-lg-12">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2">Total Revenue</h5>
                                </div>
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="totalRevenue" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded bx-lg text-muted"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalRevenue">
                                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                    </div>
                                </div> --}}
                            </div>
                            <ul class="nav nav-tabs">
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
            <!--/ Total Revenue -->
            <div class="col-12 col-md-8 col-lg-12 col-xxl-4 order-3 order-md-2">
                <div class="row">
                    <div class="col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/paypal.png"
                                            alt="paypal" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Assets</p>
                                <h4 class="card-title mb-3 ttoalsamount">{{ number_format($assets->sum('amount'), 2) }} BDT
                                </h4>
                                {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/cc-primary.png"
                                            alt="Credit Card" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Liability</p>
                                <h4 class="card-title mb-3 ttoalsamount">
                                    {{ number_format($liabilities->sum('amount'), 2) }} BDT</h4>
                                {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/paypal.png"
                                            alt="paypal" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Investments</p>
                                <h4 class="card-title mb-3 ttoalsamount">
                                    {{ number_format($investments->sum('amount'), 2) }} BDT</h4>
                                {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/cc-primary.png"
                                            alt="Credit Card" class="rounded" />
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <p class="mb-1">Total Bank Amount</p>
                                <h4 class="card-title mb-3 ttoalsamount">{{ number_format($totalbank, 2) }} BDT</h4>
                                {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-12 mb-6">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="d-flex justify-content-between align-items-center flex-sm-row flex-column gap-10">
                                    <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                        <div class="card-title mb-6">
                                            <h5 class="text-nowrap mb-1">Profile Report</h5>
                                            <span class="badge bg-label-warning">YEAR 2022</span>
                                        </div>
                                        <div class="mt-sm-auto">
                                            <span class="text-success text-nowrap fw-medium"><i
                                                    class="bx bx-up-arrow-alt"></i> 68.2%</span>
                                            <h4 class="mb-0">$84,686k</h4>
                                        </div>
                                    </div>
                                    <div id="profileReportChart"></div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Order Statistics -->
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="mb-1 me-2">Transaction Statistics</h5>

                        </div>
                        {{-- <div class="dropdown">
                            <button class="btn text-muted p-0" type="button" id="orederStatistics"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded bx-lg"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        {{-- <div class="d-flex justify-content-between align-items-center mb-6">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <h3 class="mb-1">8,258</h3>
                                <small>Total Orders</small>
                            </div>
                            <div id="orderStatisticsChart"></div>
                        </div> --}}
                        <ul class="p-0 m-0">
                            @if ($merged->isNotEmpty())
                                @foreach ($merged as $trx)
                                    <li class="d-flex align-items-center mb-5 gap-2">
                                        <div class="avatar flex-shrink-0">
                                            <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/chart-success.png"
                                                alt="chart success" class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">{{ $trx->type }}</h6>
                                                <small>{{ $trx->name }}
                                                    {{ $trx->transaction_type ? '- (' . $trx->transaction_type . ')' : '' }}
                                                </small>
                                            </div>
                                            <div class="user-progress">
                                                <h6 class="mb-0">{{ number_format($trx->amount, 2) }} BDT</h6>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif


                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Order Statistics -->

            <!-- Expense Overview -->
            <div class="col-md-6 col-lg-4 order-1 mb-6">
                <div class="card h-100">
                    <div class="card-header nav-align-top">
                        <h5 class="mb-1 me-2 mb-4">Monthly Statistics</h5>
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#income-tab">Income</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#expenses-tab">Expenses</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#assets-tab">Assets</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                    data-bs-target="#liabilities-tab">Liabilities</button>
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

            <!--/ Expense Overview -->

            <!-- Transactions -->
            <div class="col-md-6 col-lg-4 order-2 mb-6">
                <div class="card h-100">
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
                        <ul class="p-0 m-0">
                            @if ($latestBankTransactions->isNotEmpty())
                                @foreach ($latestBankTransactions as $latestBankTransaction)
                                    <li class="d-flex align-items-center mb-6">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('admin-assets') }}/assets/img/icons/unicons/wallet.png"
                                                alt="User" class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small
                                                    class="d-block">{{ $latestBankTransaction->bankAccount->bank_name }} -
                                                    {{ $latestBankTransaction->bankAccount->account_type }} </small>
                                                <h6 class="fw-normal mb-0">{{ $latestBankTransaction->transaction_type }}
                                                </h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-2">
                                                <h6 class="fw-normal mb-0">{{ $latestBankTransaction->amount }}</h6>
                                                <span class="text-muted">BDT</span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Transactions -->
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
             <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
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
