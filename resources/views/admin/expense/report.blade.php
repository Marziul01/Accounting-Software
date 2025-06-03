@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 mb-0 pb-0">
                <h5 class="mb-0">Expense Report</h5>
            </div>
            <div class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-2 align-items-md-center ">
                <div class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-2 align-items-md-center p-0 w-100">
                    <div class="d-flex align-items-start gap-2 flex-column flex-md-row align-items-md-end mobile-reports-filter">
                        <div class="form-group mobile-reports-filter-group">
                            <label class="form-label" for="start_date">Select Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ request('start_date') ?? $firstDate }}">
                        </div>

                        <div class="form-group mobile-reports-filter-group">
                            <label class="form-label" for="end_date">Select End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ request('end_date') ?? $lastDate }}">
                        </div>

                        <div class="form-group mobile-reports-filter-group1">
                            <label class="form-label" for="category">Select Category:</label>
                            <select class="form-select" id="category" name="category">
                                
                                @foreach($expenseCategories as $category)
                                    <option value="{{ $category->id }}" {{ $expenseCategory->id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex mt-2 gap-2 align-items-md-end justify-content-md-end form-group mobile-reports-filter-btns">
                        <button type="button" id="filterBtn" class="btn btn-secondary mt-md-4">Filter</button> 
                        <button type="button"  class="btn btn-primary {{ Auth::user()->access->expense == 1 ? 'disabled' : '' }}" onclick="viewFullReport()">
                            View Full Expense Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card ">
            <div class="card-body" id="expenseTableContent">
                {{-- This is where filtered HTML will be injected --}}
                @include('admin.expense.partial-table', [
                    'expenseCategories' => $expenseCategories,
                    'expenses' => $expenses,
                    'startDate' => $firstDate,
                    'endDate' => $lastDate,
                ])
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

@endsection


@section('scripts')

<script>
    document.getElementById('filterBtn').addEventListener('click', function () {
        let startDate = document.getElementById('start_date').value;
        let endDate = document.getElementById('end_date').value;
        let category = document.getElementById('category').value;

        if (!startDate || !endDate) {
            toastr.error("Please select both start and end dates.");
            return;
        }

        // Show loader
        let loader = `<div class="text-center my-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
        document.getElementById('expenseTableContent').innerHTML = loader;

        fetch(`{{ route('admin.expenseReport.filter') }}?start_date=${startDate}&end_date=${endDate}&category=${category}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('expenseTableContent').innerHTML = data.html;
            })
            .catch(error => {
                toastr.error("Error loading data.");
                console.error(error);
            });
    });

</script>

<script>
    function viewFullReport() {
        let start = document.getElementById('start_date').value;
        let end = document.getElementById('end_date').value;
        let url = `{{ url('/admin/full-expense-report') }}?start_date=${start}&end_date=${end}`;
        window.location.href = url;
    }
</script>


<script>
    function viewFullCategoryReport() {
        let start = document.getElementById('start_date').value;
        let end = document.getElementById('end_date').value;
        let url = `{{ url('/admin/expenses/category/report') }}?start_date=${start}&end_date=${end}`;
        window.location.href = url;
    }
</script>
@endsection
