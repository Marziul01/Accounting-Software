@if ($incomeCategories->isNotEmpty())

    <div>
            <div >

                <div class="card-header d-flex justify-content-between align-items-start border-bottom-1 px-0 pt-0 flex-column flex-md-row gap-2 align-items-md-center">
                    <h5 class="mb-0">{{ $incomeCategory->name }} Reports</h5>

                    <a class="btn btn-primary"
                       href="{{ url('/admin/incomes/category/report') }}?slug={{ $incomeCategory->slug }}&start_date={{ $startDate }}&end_date={{ $endDate }}"> 
                        View Full {{ $incomeCategory->name }} Category Income Report
                    </a>
                </div>

                <div class="card-body text-nowrap px-0 table-responsive">
                    <table class="table" id="myTable{{ $incomeCategory->id }}">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Income Subcategory</th>
                                <th>Amount</th>
                                <th>Report</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @php
                                $subCategories = $incomeCategory->incomeSubCategories;
                                $filteredIncome = $incomes->where('income_category_id', $incomeCategory->id);
                            @endphp

                            @if($subCategories->isNotEmpty())
                                @foreach ($subCategories as $incomeSubCategory)
                                    @php
                                        $totalIncome = $incomes->where('income_sub_category_id', $incomeSubCategory->id)->sum('amount');
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $incomeSubCategory->name }}</td>
                                        <td>{{ $totalIncome }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-secondary"
                                               href="{{ route('admin.IncomesubcategoryReport', ['slug' => $incomeSubCategory->slug, 'start_date' => $startDate, 'end_date' => $endDate]) }}" >
                                                 View Report
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" class="text-start">Total Income:</td>
                                    <td>{{ $filteredIncome->sum('amount') }}</td>
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
    <div class="text-center">No Income Categories Found.</div>
@endif
