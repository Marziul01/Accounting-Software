@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card mb-4">
            <div
                class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">Export All Data</h5>
                </div>
            </div>
        </div>

        <div class="row g-2">
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export Investment</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Categories and Investments</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('investments.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('investments.export', ['format' => 'csv']) }}">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('investments.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export Incomes</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Categories and Incomes</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('income.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('income.export', ['format' => 'csv']) }}">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('income.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export Expense</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Categories and Expenses</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('expense.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('expense.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export Assets</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Categories and Assets</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('asset.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('asset.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export Liabilities</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Categories and Liabilities</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('liability.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('liability.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 mb-2">
                <div class="card p-4 rounded-lg shadow mb-4">
                    <h5 class=" mb-4">Export BankBook</h5>
                    <div class="row">
                        <div class="flex p-2 col-12">
                            <span class="text-capitalize mb-2">Banks and Transactions</span>
                            <div class="flex items-center space-x-2 mt-3">
                                <div class="flex overflow-hidden">
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('bank.export', ['format' => 'pdf']) }}">PDF</a>
                                    {{-- <a class="px-3 py-1 text-sm btn btn-primary status-btn">CSV</a> --}}
                                    <a class="px-3 py-1 text-sm btn btn-primary status-btn" href="{{ route('bank.export', ['format' => 'xlsx']) }}">Exel</a>
                                </div>
                            </div>
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
                        <p id="successMessage" class="text-center"></p>
                    </div>
                </div>
            </div>
        </div>
    @endsection


    @section('scripts')

    @endsection
