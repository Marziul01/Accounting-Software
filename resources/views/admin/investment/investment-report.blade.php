@extends('admin.master')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Investments Report</h5>
        </div>
        <div class="card-body d-flex justify-content-start align-items-start gap-2 flex-column flex-md-row align-items-md-end mobile-reports-filter">
            <div class="mobile-reports-filter-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="mobile-reports-filter-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="mobile-reports-filter-group">
                <label for="investment_category">Category:</label>
                <select class="form-select category-select" name="category_id" id="investment_category">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $loop->first ? 'selected' : '' }} data-slug="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mobile-reports-filter-group">
                <label for="investment_subcategory">Subcategory:</label>
                <select class="form-select category-select" name="subcategory_id" id="investment_subcategory">
                    <option value="">Select Subcategory</option>
                </select>
            </div>

            
        </div>

        {{-- Category/Subcategory Report Buttons --}}
        <div class="card-footer d-flex gap-2 w-100 mobile-reports-filter-btns">
            <button id="filterButton" class="btn btn-secondary"
                data-url="{{ route('admin.filteredInvestments') }}">
                Filter with Details
            </button>

            <button class="btn btn-primary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" onclick="viewFullReport()">View Full Investment Report</button>
            <button
                id="categoryReportBtn"
                data-url="{{ route('admin.report.category', ['slug' => 'CATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Category Report
            </button>
            <button
                id="subcategoryReportBtn"
                data-url="{{ route('admin.report.subcategory', ['slug' => 'SUBCATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Subcategory Report
            </button>
        </div>
    </div>

    {{-- Investment Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start flex-column flex-md-row gap-2 align-items-md-center">
            <h5 class="mb-0">Investment</h5>
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="investmentTable" data-investment-url="{{ route('admin.report.investment', ['slug' => 'SLUG']) }}">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Investment Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Investment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- To be populated dynamically based on filter --}}
                        @foreach ($filteredInvestments as $investment)
                        @php
                            // 1. Total transactions (all time)
                            $initialAmount = $investment->allTransactions->first()->amount ?? 0;

                            // 2. Filtered transactions (between start and end)
                            $depositInRange = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                            $withdrawInRange = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                            $currentAmount = $depositInRange - $withdrawInRange;

                            

                            if ($investment->allTransactions->isNotEmpty() && $investment->allTransactions->first()->transaction_date >= $startDate) {
                                // If the first transaction is on or after the start date, previous amount is just the initial amount
                                $previousAmount = $initialAmount;
                            } else {
                                // Start date is on or after investment date
                                $depositBeforeStart = $investment->allTransactions
                                    ->where('transaction_type', 'Deposit')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $withdrawBeforeStart = $investment->allTransactions
                                    ->where('transaction_type', 'Withdraw')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
                            }
                            
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $investment->investmentCategory->name }} - ({{ $investment->investmentSubCategory->name }})</td>
                            <td>{{ $investment->name }}</td>
                            <td>{{ $investment->description ?? 'N/A' }}</td>
                             <td> {{ number_format($currentAmount, 2) }} Tk
                            </td>
                            <td>{{ \Carbon\Carbon::parse($investment->date)->format('d M, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.report.investment', ['slug' => $investment->slug, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}">
                                     View Report
                                </a>

                            </td>
                        </tr>
                        @endforeach
                        @if ($filteredInvestments->isEmpty())
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

@endsection

@section('scripts')

<script>
    const allSubcategories = @json($subcategories); // You need to pass this from controller

    function populateSubcategories(categoryId, autoSelect = true) {
        const subcategorySelect = document.getElementById('investment_subcategory');
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

        const filtered = allSubcategories.filter(sc => sc.investment_category_id == categoryId);

        filtered.forEach((sc, index) => {
            const opt = document.createElement('option');
            opt.value = sc.id;
            opt.textContent = sc.name;
            opt.setAttribute('data-slug', sc.slug);
            subcategorySelect.appendChild(opt);
        });

        // ✅ Auto-select the first subcategory and trigger report if needed
        if (autoSelect && filtered.length > 0) {
            subcategorySelect.selectedIndex = 1; // index 0 is "Select Subcategory"
            
        }
    }



    document.addEventListener('DOMContentLoaded', function () {
        const defaultCategoryId = document.getElementById('investment_category').value;
        populateSubcategories(defaultCategoryId);

        document.getElementById('investment_category').addEventListener('change', function () {
            populateSubcategories(this.value);
        });

        // document.getElementById('filterBtn').addEventListener('click', function () {
        //     const categoryId = document.getElementById('investment_category').value;
        //     const subcategoryId = document.getElementById('investment_subcategory').value;
        //     const startDate = document.getElementById('start_date').value;
        //     const endDate = document.getElementById('end_date').value;

        //     const url = `{{ route('admin.filteredInvestments') }}?category_id=${categoryId}&subcategory_id=${subcategoryId}&start_date=${startDate}&end_date=${endDate}`;
        //     window.location.href = url;
        // });

        document.getElementById('categoryReportBtn').addEventListener('click', function () {
            const selectedOption = document.getElementById('investment_category').selectedOptions[0];
            const categorySlug = selectedOption.dataset.slug;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            const baseUrl = this.dataset.url.replace('CATEGORY_SLUG', categorySlug);
            const fullUrl = `${baseUrl}?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = fullUrl;
        });

        document.getElementById('subcategoryReportBtn').addEventListener('click', function () {
            const selectedOption = document.getElementById('investment_subcategory').selectedOptions[0];

            if (!selectedOption || !selectedOption.dataset.slug) {
                alert("Please select a valid subcategory.");
                return;
            }

            const subcategorySlug = selectedOption.dataset.slug;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            const baseUrl = this.dataset.url.replace('SUBCATEGORY_SLUG', subcategorySlug);
            const fullUrl = `${baseUrl}?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = fullUrl;
        });



    });
</script>

<script>
function fetchFilteredData() {
    const categorySelect = document.getElementById('investment_category');
    const subcategorySelect = document.getElementById('investment_subcategory');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterButton = document.getElementById('filterButton');

    const categoryId = categorySelect.value;
    const subcategoryId = subcategorySelect.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    const table = document.getElementById('investmentTable');
    const routeTemplate = table.dataset.investmentUrl;

    const tableBody = document.querySelector('#investmentTable tbody');

    // Build the correct base URL from the Blade route
    const baseUrl = filterButton.dataset.url;

    // Show loading spinner
    tableBody.innerHTML = `
        <tr>
            <td colspan="7" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    const fullUrl = `${baseUrl}?category_id=${categoryId}&subcategory_id=${subcategoryId}&start_date=${startDate}&end_date=${endDate}`;

    fetch(fullUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        let rows = '';
        if (data.length > 0) {
            data.forEach((investment, index) => {

                const reportUrl = `${routeTemplate.replace('SLUG', investment.slug)}?start_date=${encodeURIComponent(investment.start_date)}&end_date=${encodeURIComponent(investment.end_date)}`;

                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${investment.category_name} - (${investment.subcategory_name})</td>
                        <td>${investment.name}</td>
                        <td>${investment.description ?? 'N/A'}</td>
                        <td>${investment.amount}</td>
                        <td>${investment.formatted_date}</td>
                        <td>
                            <a href="${reportUrl}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-edit-alt me-1"></i> View Report
                            </a>
                        </td>
                    </tr>
                `;
            });

        } else {
            rows = `<tr><td colspan="7" class="text-center">No Investment found.</td></tr>`;
        }
        tableBody.innerHTML = rows;
    })
    .catch(err => {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error loading data.</td></tr>`;
        console.error(err);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const filterButton = document.getElementById('filterButton');

    if (filterButton) {
        filterButton.addEventListener('click', function (e) {
            e.preventDefault();
            fetchFilteredData();
        });
    }
});
</script>

<script>
function viewFullReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const url = `{{ route('admin.report.fullinvestment') }}?start_date=${startDate}&end_date=${endDate}`;
    window.location.href = url;
}
</script>



@endsection
