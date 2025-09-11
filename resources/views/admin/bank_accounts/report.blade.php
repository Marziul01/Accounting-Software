@extends('admin.master')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Bank Account Transactions Report</h5>
        </div>
        <div class="card-body d-flex flex-column justify-content-between flex-md-row gap-2 align-items-start align-items-md-end">
            <div class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-end mobile-reports-filter">
                <div class="mobile-reports-filter-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" class="form-control myDate" value="{{ $startDate }}">
                </div>
                <div class="mobile-reports-filter-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" class="form-control myDate" value="{{ $endDate }}">
                </div>
                <div class="mobile-reports-filter-group1">
                    <label for="bank_account_id">Bank Account:</label>
                    <select class="form-select category-select" name="bank_account_id" id="bank_account_id">
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ $account->id == $selectedBankAccount ? 'selected' : '' }}>
                                {{ $account->bank_name }} ({{ $account->account_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
            </div>
            <div class="mobile-reports-filter-btns">
                <button id="filterButton" class="btn btn-primary"
                    data-url="{{ route('admin.filteredBankTransactions') }}">
                    Filter Transactions
                </button>
                <button
                    id="bankAccountReportBtn"
                    data-base-url="{{ route('admin.report.bankaccount', ['id' => 'BANKACCOUNT_ID']) }}"
                    class="btn btn-primary">
                    Bank Account Report
                </button>
            </div>
        </div>
        
    </div>

    {{-- Transactions Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start flex-column flex-md-row gap-2 align-items-md-center">
            <h5 class="mb-0">Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="transactionTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Transactions Name</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filteredTransactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M, Y') }}</td>
                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                            <td>{{ ucfirst($transaction->transaction_type) }}</td>
                            <td>{{ $transaction->amount }}</td>
                           
                        </tr>
                        @endforeach
                        @if ($filteredTransactions->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">No Transactions found.</td>
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
function fetchFilteredTransactions() {
    const bankAccountId = document.getElementById('bank_account_id').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const filterUrl = document.getElementById('filterButton').dataset.url;
    const tableBody = document.querySelector('#transactionTable tbody');

    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    fetch(`${filterUrl}?bank_account_id=${bankAccountId}&start_date=${startDate}&end_date=${endDate}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        let rows = '';
        if (data.length > 0) {
            data.forEach((transaction, index) => {
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${transaction.name}</td>
                        <td>${transaction.transaction_date}</td>
                        <td>${transaction.description ?? 'N/A'}</td>
                        <td>${transaction.transaction_type}</td>
                        <td>${transaction.amount}</td>
                        
                    </tr>
                `;
            });
        } else {
            rows = `<tr><td colspan="6" class="text-center">No Transactions found.</td></tr>`;
        }
        tableBody.innerHTML = rows;
    })
    .catch(err => {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error loading data.</td></tr>`;
        console.error(err);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const filterButton = document.getElementById('filterButton');
    const reportButton = document.getElementById('bankAccountReportBtn');

    // Initial Load
    fetchFilteredTransactions();

    // Filter on button click
    filterButton.addEventListener('click', function (e) {
        e.preventDefault();
        fetchFilteredTransactions();
    });

    // Change triggers filter too
    // ['bank_account_id', 'start_date', 'end_date'].forEach(id => {
    //     document.getElementById(id).addEventListener('change', fetchFilteredTransactions);
    // });

    // Bank Account Report Button
    reportButton.addEventListener('click', function () {
        const bankAccountId = document.getElementById('bank_account_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const baseReportUrl = this.dataset.baseUrl.replace('BANKACCOUNT_ID', bankAccountId);

        window.location.href = `${baseReportUrl}&start_date=${startDate}&end_date=${endDate}`;
    });
});
</script>
@endsection
