@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 mb-0 pb-0">
                <h5 class="mb-0">Income Report</h5>
            </div>
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <div class="d-flex align-items-end gap-2">
                    <div class="form-group">
                        <label class="form-label" for="start_date">Select Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="end_date">Select End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <button type="button" id="filterBtn" class="btn btn-secondary">Filter</button>

                </div>
                <button type="button"  class="btn btn-primary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" onclick="viewFullReport()">
                    View Full Income Report
                </button>
            </div>
        </div>
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">Investment</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" data-bs-toggle="modal"
                    data-bs-target="#addmodals">Add New Investment</button>
            </div>
            <div class="card-body  text-nowrap">
                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Investment Category</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Investment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if($investments->isNotEmpty()) 
                            @foreach ($investments as $investment )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $investment->investmentCategory->name ?? 'Investment Category Not Assigned' }} - ( {{ $investment->investmentSubCategory->name ?? 'Investment Sub Category Not Assigned' }} ) </td>
                                
                                <td>{{ $investment->name }}</td>
                                <td>{{ $investment->description ?? 'N/A' }}</td>
                                <td>{{ $investment->amount ?? 'N/A' }}</td> <!-- ✅ Amount -->
                                <td>{{ \Carbon\Carbon::parse($investment->income_date)->format('d M, Y') ?? 'N/A' }}</td> <!-- ✅ Income Date -->
                                <td>
                                    <div class="d-flex align-items-center gap-1 cursor-pointer">
                                        <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}" href="{{ route('admin.IncomecategoryReport', $incomeCategory->slug) }}"><i class="bx bx-edit-alt me-1"></i> View Report</a>
                                    </div>
                                </td>
                            </tr>
                            
                            @endforeach
                            @else
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
