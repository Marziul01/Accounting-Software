@if ($expenseCategories->isNotEmpty())
    

    <div >
        
            <div>

                <div class="card-header d-flex justify-content-between align-items-start border-bottom-1 px-0 pt-0 flex-column flex-md-row gap-2 align-items-md-center">
                    <h5 class="mb-0">{{ $expenseCategory->name }} Reports</h5>

                    <a class="btn btn-primary"
                       href="{{ url('/admin/expenses/category/report') }}?slug={{ $expenseCategory->slug }}&start_date={{ $startDate }}&end_date={{ $endDate }}"> 
                        View Full {{ $expenseCategory->name }} Category expense Report
                    </a>
                </div>

                <div class="card-body text-nowrap px-0 table-responsive">
                    <table class="table" id="myTable{{ $expenseCategory->id }}">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>expense Subcategory</th>
                                <th>Amount</th>
                                <th>Report</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php
                                $subCategories = $expenseCategory->expenseSubCategories;
                                $filteredexpense = $expenses->where('expense_category_id', $expenseCategory->id);
                            @endphp

                            @if($subCategories->isNotEmpty())
                                @foreach ($subCategories as $expenseSubCategory)
                                    @php
                                        $totalexpense = $expenses->where('expense_sub_category_id', $expenseSubCategory->id)->sum('amount');
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $expenseSubCategory->name }}</td>
                                        <td>{{ $totalexpense }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-secondary"
                                               href="{{ route('admin.expensesubcategoryReport', ['slug' => $expenseSubCategory->slug, 'start_date' => $startDate, 'end_date' => $endDate]) }}">
                                                <i class="bx bx-edit-alt me-1"></i> View Report
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-start">Total expense:</td>
                                    <td>{{ $filteredexpense->sum('amount') }}</td>
                                    <td></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">No subcategories found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        
    </div>
@else
    <div class="text-center">No expense Category Found.</div>
@endif
