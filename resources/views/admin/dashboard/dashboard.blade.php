@extends('admin.master')

@section('content')
<script>
    const monthlyData = @json($monthlyData); // from Laravel controller
    const revenueData = @json($monthlyComparisonData);
</script>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl-8 mb-6 order-0">
                <div class="card">
                    <div class="d-flex align-items-start row">
                        <div class="col-sm-7">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3">Congratulations John! 🎉</h5>
                                <p class="mb-6">
                                    You have done 72% more sales today.<br />Check your new badge in your profile.
                                </p>

                                <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-6">
                                <img src="{{ asset('admin-assets')  }}/assets/img/illustrations/man.png"
                                    height="175" class="scaleX-n1-rtl" alt="View Badge User" />
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
                                        <img src="{{ asset('admin-assets')  }}/assets/img/icons/unicons/chart-success.png"
                                            alt="chart success" class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1">Profit</p>
                                <h4 class="card-title mb-3">$12,628</h4>
                                <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets')  }}/assets/img/icons/unicons/wallet-info.png"
                                            alt="wallet info" class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1">Sales</p>
                                <h4 class="card-title mb-3">$4,679</h4>
                                <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
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
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="totalRevenue" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded bx-lg text-muted"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="totalRevenue">
                                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#revenue-income">Income</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#revenue-expense">Expense</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#revenue-asset">Asset</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#revenue-liability">Liability</button>
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
                                        <img src="{{ asset('admin-assets')  }}/assets/img/icons/unicons/paypal.png"
                                            alt="paypal" class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1">Payments</p>
                                <h4 class="card-title mb-3">$2,456</h4>
                                <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ asset('admin-assets')  }}/assets/img/icons/unicons/cc-primary.png"
                                            alt="Credit Card" class="rounded" />
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                            <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                            <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-1">Transactions</p>
                                <h4 class="card-title mb-3">$14,857</h4>
                                <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-6">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Order Statistics -->
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title mb-0">
                            <h5 class="mb-1 me-2">Order Statistics</h5>
                            <p class="card-subtitle">42.82k Total Sales</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn text-muted p-0" type="button" id="orederStatistics"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded bx-lg"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <h3 class="mb-1">8,258</h3>
                                <small>Total Orders</small>
                            </div>
                            <div id="orderStatisticsChart"></div>
                        </div>
                        <ul class="p-0 m-0">
                            <li class="d-flex align-items-center mb-5">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary"><i
                                            class="bx bx-mobile-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Electronic</h6>
                                        <small>Mobile, Earbuds, TV</small>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0">82.5k</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-5">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-success"><i
                                            class="bx bx-closet"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Fashion</h6>
                                        <small>T-shirt, Jeans, Shoes</small>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0">23.8k</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-5">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-info"><i
                                            class="bx bx-home-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Decor</h6>
                                        <small>Fine Art, Dining</small>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0">849k</h6>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-secondary"><i
                                            class="bx bx-football"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Sports</h6>
                                        <small>Football, Cricket Kit</small>
                                    </div>
                                    <div class="user-progress">
                                        <h6 class="mb-0">99</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Order Statistics -->

            <!-- Expense Overview -->
            <div class="col-md-6 col-lg-4 order-1 mb-6">
                <div class="card h-100">
                    <div class="card-header nav-align-top">
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#income-tab">Income</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#expenses-tab">Expenses</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#assets-tab">Assets</button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#liabilities-tab">Liabilities</button>
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
                            @foreach ($latestBankTransactions as $latestBankTransaction )
                                <li class="d-flex align-items-center mb-6">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{ asset('admin-assets')  }}/assets/img/icons/unicons/wallet.png"
                                            alt="User" class="rounded" />
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="d-block">{{ $latestBankTransaction->bankAccount->bank_name }} - {{ $latestBankTransaction->bankAccount->account_type }} </small>
                                            <h6 class="fw-normal mb-0">{{ $latestBankTransaction->transaction_type }}</h6>
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
@endsection

@section('script')


<script>
document.addEventListener('DOMContentLoaded', () => {
  // Find the income tab button and programmatically trigger 'shown.bs.tab'
  const incomeTabButton = document.querySelector('button[data-bs-target="#income-tab"]');
  if (incomeTabButton) {
    // Render the chart once the tab is shown (simulate tab shown)
    incomeTabButton.dispatchEvent(new Event('shown.bs.tab'));
  }
});

</script>


@endsection
