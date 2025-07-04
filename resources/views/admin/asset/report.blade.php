@extends('admin.master')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assets Report</h5>
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
                <label for="asset_category">Category:</label>
                <select class="form-select category-select" name="category_id" id="asset_category">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $loop->first ? 'selected' : '' }} data-slug="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mobile-reports-filter-group">   
                <label for="asset_subcategory">Subcategory:</label>
                <select class="form-select category-select" name="subcategory_id" id="asset_subcategory">
                    <option value="">Select Subcategory</option>
                </select>
            </div>

            {{-- <div class="mobile-reports-filter-group1">
                <label for="asset_subsubcategory">Sub-subcategory:</label>
                <select class="form-select category-select" name="subsubcategory_id" id="asset_subsubcategory">
                    <option value="">Select Sub-subcategory</option>
                </select>
            </div> --}}


            
        </div>

        <div class="card-footer d-flex gap-2 w-100 mobile-reports-filter-btns">
            <button id="filterButton" class="btn btn-secondary"
                data-url="{{ route('admin.filteredAssets') }}">
                Filter with Details
            </button>

            <button class="btn btn-primary {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}" onclick="viewFullAssetReport()">View Full Asset Report</button>
            <button
                id="categoryReportBtn"
                data-url="{{ route('admin.asset.categoryReport', ['slug' => 'CATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Category Report
            </button>
            <button
                id="subcategoryReportBtn"
                data-url="{{ route('admin.asset.subcategoryReport', ['slug' => 'SUBCATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Subcategory Report
            </button>
            {{-- <button
                id="subsubcategoryReportBtn"
                data-url="{{ route('admin.asset.subsubcategoryReport', ['slug' => 'SUBSUBCATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Sub-subcategory Report
            </button> --}}
        </div>
    </div>

    {{-- Asset Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start flex-column flex-md-row gap-2 align-items-md-center">
            <h5 class="mb-0">Assets</h5>
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="assetTable" data-asset-url="{{ route('admin.asset.assetreport', ['slug' => 'SLUG']) }}">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Current Amount</th>
                            <th>Entry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filteredAssets as $asset)
                        @php
                            // 1. Total transactions (all time)
                            
                            $initialAmount = $asset->allTransactions->first()->amount ?? 0;

                            // 2. Filtered transactions (between start and end)
                            $depositInRange = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                            $withdrawInRange = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                            $currentAmount = $depositInRange - $withdrawInRange;

                            

                            if ($asset->allTransactions->isNotEmpty() && $asset->allTransactions->first()->transaction_date >= $startDate) {
                                $previousAmount = $initialAmount;
                            } else {
                                // Start date is on or after investment date
                                $depositBeforeStart = $asset->allTransactions
                                    ->where('transaction_type', 'Deposit')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $withdrawBeforeStart = $asset->allTransactions
                                    ->where('transaction_type', 'Withdraw')
                                    ->where('transaction_date', '<', $startDate)
                                    ->sum('amount');

                                $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->description ?? 'N/A' }}</td>
                            <td>
                                {{ number_format($currentAmount, 2) }} Tk</span>
                                
                            </td>
                            <td>{{ \Carbon\Carbon::parse($asset->entry_date)->format('d M, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.asset.assetreport', ['slug' => $asset->slug, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                   class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}">
                                    View Report
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if ($filteredAssets->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">No Asset found.</td>
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
    const allSubSubcategories = @json($subSubcategories ?? []);

    const routes = {
        getSubcategories: "{{ route('subcategories.byCategory', ['id' => 'CATEGORY_ID']) }}",
        getSubSubcategories: "{{ route('subsubcategories.bySubCategory', ['id' => 'SUBCATEGORY_ID']) }}",
    };

    function populateSubcategories(categoryId, autoSelect = true) {
        const subcategorySelect = document.getElementById('asset_subcategory');
        const subsubcategorySelect = document.getElementById('asset_subsubcategory');
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        // subsubcategorySelect.innerHTML = '<option value="">Select Sub-subcategory</option>';

        if (!categoryId) return;

        const url = routes.getSubcategories.replace('CATEGORY_ID', categoryId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                data.forEach((subcat, index) => {
                    const opt = document.createElement('option');
                    opt.value = subcat.id;
                    opt.textContent = subcat.name;
                    opt.setAttribute('data-slug', subcat.slug);
                    subcategorySelect.appendChild(opt);
                });

                if (autoSelect && data.length > 0) {
                    subcategorySelect.selectedIndex = 1;
                    // populateSubSubcategories(data[0].id);
                }
            });
    }

    // function populateSubSubcategories(subcategoryId, autoSelect = true) {
    //     const subsubcategorySelect = document.getElementById('asset_subsubcategory');
    //     subsubcategorySelect.innerHTML = '<option value="">Select Sub-subcategory</option>';

    //     if (!subcategoryId) return;

    //     const url = routes.getSubSubcategories.replace('SUBCATEGORY_ID', subcategoryId);

    //     fetch(url)
    //         .then(response => response.json())
    //         .then(data => {
    //             data.forEach((subsub, index) => {
    //                 const opt = document.createElement('option');
    //                 opt.value = subsub.id;
    //                 opt.textContent = subsub.name;
    //                 opt.setAttribute('data-slug', subsub.slug);
    //                 subsubcategorySelect.appendChild(opt);
    //             });

    //             if (autoSelect && data.length > 0) {
    //                 subsubcategorySelect.selectedIndex = 1;
    //             }
    //         });
    // }

    document.addEventListener('DOMContentLoaded', function () {
        const defaultCategoryId = document.getElementById('asset_category').value;
        populateSubcategories(defaultCategoryId);

        document.getElementById('asset_category').addEventListener('change', function () {
            populateSubcategories(this.value);
        });

        document.getElementById('asset_subcategory').addEventListener('change', function () {
            populateSubSubcategories(this.value);
        });
    });

    
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('asset_category');
        const subcategorySelect = document.getElementById('asset_subcategory');
        // const subsubcategorySelect = document.getElementById('asset_subsubcategory');

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        const categoryReportBtn = document.getElementById('categoryReportBtn');
        const subcategoryReportBtn = document.getElementById('subcategoryReportBtn');
        // const subsubcategoryReportBtn = document.getElementById('subsubcategoryReportBtn');

        function generateReportUrl(baseUrl, placeholder, slug) {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const replacedUrl = baseUrl.replace(placeholder, slug);
            return `${replacedUrl}?start_date=${startDate}&end_date=${endDate}`;
        }

        categoryReportBtn.addEventListener('click', function () {
            const selectedOption = categorySelect.selectedOptions[0];
            const slug = selectedOption?.dataset.slug;
            if (!slug) return alert('Please select a valid category.');

            const fullUrl = generateReportUrl(this.dataset.url, 'CATEGORY_SLUG', slug);
            window.location.href = fullUrl;
        });

        subcategoryReportBtn.addEventListener('click', function () {
            const selectedOption = subcategorySelect.selectedOptions[0];
            const slug = selectedOption?.dataset.slug;
            if (!slug) return alert('Please select a valid subcategory.');

            const fullUrl = generateReportUrl(this.dataset.url, 'SUBCATEGORY_SLUG', slug);
            window.location.href = fullUrl;
        });

        // subsubcategoryReportBtn.addEventListener('click', function () {
        //     const selectedOption = subsubcategorySelect.selectedOptions[0];
        //     const slug = selectedOption?.dataset.slug;
        //     if (!slug) return alert('Please select a valid sub-subcategory.');

        //     const fullUrl = generateReportUrl(this.dataset.url, 'SUBSUBCATEGORY_SLUG', slug);
        //     window.location.href = fullUrl;
        // });
    });
</script>



<script>
function fetchFilteredAssetData() {
    const categorySelect = document.getElementById('asset_category');
    const subcategorySelect = document.getElementById('asset_subcategory');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterButton = document.getElementById('filterButton');
    // const subSubcategorySelect = document.getElementById('asset_subsubcategory');
    // const subsubcategoryId = subSubcategorySelect.value;


    const categoryId = categorySelect.value;
    const subcategoryId = subcategorySelect.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    const table = document.getElementById('assetTable');
    const routeTemplate = table.dataset.assetUrl;

    const tableBody = document.querySelector('#assetTable tbody');

    const baseUrl = filterButton.dataset.url;

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
            data.forEach((asset, index) => {

                const reportUrl = `${routeTemplate.replace('SLUG', asset.slug)}?start_date=${encodeURIComponent(asset.start_date)}&end_date=${encodeURIComponent(asset.end_date)}`;

                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        
                        <td>${asset.name}</td>
                        <td>${asset.description ?? 'N/A'}</td>
                        <td>${asset.value} Tk </td>
                        <td>${asset.formatted_date}</td>
                        <td>
                            <a href="${reportUrl}" class="btn btn-sm btn-outline-secondary">
                                 View Report
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            rows = `<tr><td colspan="7" class="text-center">No Asset found.</td></tr>`;
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
            fetchFilteredAssetData();
        });
    }
});
</script>

<script>
function viewFullAssetReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const url = `{{ route('admin.asset.fullreport') }}?start_date=${startDate}&end_date=${endDate}`;
    window.location.href = url;
}
</script>
@endsection
