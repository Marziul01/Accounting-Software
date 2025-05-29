@extends('admin.master')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liabilities Report</h5>
        </div>
        <div class="card-body d-flex justify-content-between align-items-start gap-2 flex-column flex-md-row align-items-md-end ">
            <div>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div>
                <label for="liability_category">Category:</label>
                <select class="form-select category-select" name="category_id" id="liability_category">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $loop->first ? 'selected' : '' }} data-slug="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="liability_subcategory">Subcategory:</label>
                <select class="form-select category-select" name="subcategory_id" id="liability_subcategory">
                    <option value="">Select Subcategory</option>
                </select>
            </div>

            <div>
                <label for="liability_subsubcategory">Sub-subcategory:</label>
                <select class="form-select category-select" name="subsubcategory_id" id="liability_subsubcategory">
                    <option value="">Select Sub-subcategory</option>
                </select>
            </div>

            <button id="filterButton" class="btn btn-primary"
                data-url="{{ route('admin.filteredLiabilities') }}">
                Filter with Details
            </button>

            <button class="btn btn-primary {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}" onclick="viewFullLiabilityReport()">View Full Liability Report</button>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2 flex-column flex-md-row align-items-md-end">
            <button
                id="categoryReportBtn"
                data-url="{{ route('admin.liability.categoryReport', ['slug' => 'CATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Category Report
            </button>
            <button
                id="subcategoryReportBtn"
                data-url="{{ route('admin.liability.subcategoryReport', ['slug' => 'SUBCATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Subcategory Report
            </button>
            <button
                id="subsubcategoryReportBtn"
                data-url="{{ route('admin.liability.subsubcategoryReport', ['slug' => 'SUBSUBCATEGORY_SLUG']) }}"
                class="btn btn-primary">
                Sub-subcategory Report
            </button>
        </div>
    </div>

    {{-- Liability Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start flex-column flex-md-row gap-2 align-items-md-center">
            <h5 class="mb-0">Liabilities</h5>
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="liabilityTable" data-liability-url="{{ route('admin.liability.liabilityreport', ['slug' => 'SLUG']) }}">
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
                        @foreach ($filteredLiabilities as $liability)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $liability->name }}</td>
                            <td>{{ $liability->description ?? 'N/A' }}</td>
                            <td>{{ $liability->amount }}</td>
                            <td>{{ \Carbon\Carbon::parse($liability->entry_date)->format('d M, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.liability.liabilityreport', $liability->slug) }}"
                                   class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}">
                                   <i class="bx bx-edit-alt me-1"></i> View Report
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if ($filteredLiabilities->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">No Liability found.</td>
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
        getSubcategories: "{{ route('liabilitysubcategories.byCategory', ['id' => 'CATEGORY_ID']) }}",
        getSubSubcategories: "{{ route('liabilitysubsubcategories.bySubCategory', ['id' => 'SUBCATEGORY_ID']) }}",
    };

    function populateSubcategories(categoryId, autoSelect = true) {
        const subcategorySelect = document.getElementById('liability_subcategory');
        const subsubcategorySelect = document.getElementById('liability_subsubcategory');
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        subsubcategorySelect.innerHTML = '<option value="">Select Sub-subcategory</option>';

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
                    populateSubSubcategories(data[0].id);
                }
            });
    }

    function populateSubSubcategories(subcategoryId, autoSelect = true) {
        const subsubcategorySelect = document.getElementById('liability_subsubcategory');
        subsubcategorySelect.innerHTML = '<option value="">Select Sub-subcategory</option>';

        if (!subcategoryId) return;

        const url = routes.getSubSubcategories.replace('SUBCATEGORY_ID', subcategoryId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                data.forEach((subsub, index) => {
                    const opt = document.createElement('option');
                    opt.value = subsub.id;
                    opt.textContent = subsub.name;
                    opt.setAttribute('data-slug', subsub.slug);
                    subsubcategorySelect.appendChild(opt);
                });

                if (autoSelect && data.length > 0) {
                    subsubcategorySelect.selectedIndex = 1;
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const defaultCategoryId = document.getElementById('liability_category').value;
        populateSubcategories(defaultCategoryId);

        document.getElementById('liability_category').addEventListener('change', function () {
            populateSubcategories(this.value);
        });

        document.getElementById('liability_subcategory').addEventListener('change', function () {
            populateSubSubcategories(this.value);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('liability_category');
        const subcategorySelect = document.getElementById('liability_subcategory');
        const subsubcategorySelect = document.getElementById('liability_subsubcategory');

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        const categoryReportBtn = document.getElementById('categoryReportBtn');
        const subcategoryReportBtn = document.getElementById('subcategoryReportBtn');
        const subsubcategoryReportBtn = document.getElementById('subsubcategoryReportBtn');

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

        subsubcategoryReportBtn.addEventListener('click', function () {
            const selectedOption = subsubcategorySelect.selectedOptions[0];
            const slug = selectedOption?.dataset.slug;
            if (!slug) return alert('Please select a valid sub-subcategory.');

            const fullUrl = generateReportUrl(this.dataset.url, 'SUBSUBCATEGORY_SLUG', slug);
            window.location.href = fullUrl;
        });
    });
</script>

<script>
function fetchFilteredLiabilityData() {
    const categorySelect = document.getElementById('liability_category');
    const subcategorySelect = document.getElementById('liability_subcategory');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const filterButton = document.getElementById('filterButton');
    const subSubcategorySelect = document.getElementById('liability_subsubcategory');
    const subsubcategoryId = subSubcategorySelect.value;

    const categoryId = categorySelect.value;
    const subcategoryId = subcategorySelect.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    const table = document.getElementById('liabilityTable');
    const routeTemplate = table.dataset.liabilityUrl;

    const tableBody = document.querySelector('#liabilityTable tbody');

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

    const fullUrl = `${baseUrl}?category_id=${categoryId}&subcategory_id=${subcategoryId}&subsubcategory_id=${subsubcategoryId}&start_date=${startDate}&end_date=${endDate}`;

    fetch(fullUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        let rows = '';
        if (data.length > 0) {
            data.forEach((liability, index) => {
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${liability.name}</td>
                        <td>${liability.description ?? 'N/A'}</td>
                        <td>${liability.value}</td>
                        <td>${liability.formatted_date}</td>
                        <td>
                            <a href="${routeTemplate.replace('SLUG', liability.slug)}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-edit-alt me-1"></i> View Report
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            rows = `<tr><td colspan="7" class="text-center">No Liability found.</td></tr>`;
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
            fetchFilteredLiabilityData();
        });
    }
});
</script>

<script>
function viewFullLiabilityReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    const url = `{{ route('admin.liability.fullreport') }}?start_date=${startDate}&end_date=${endDate}`;
    window.location.href = url;
}
</script>
@endsection
