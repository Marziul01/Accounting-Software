@if ($incomeCategories->isNotEmpty())
    <ul class="nav nav-tabs" id="bankAccountTabs" role="tablist">
        @foreach ($incomeCategories as $index => $incomeCategory)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                        id="tab-{{ $incomeCategory->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#content-{{ $incomeCategory->id }}"
                        type="button" role="tab"
                        aria-controls="content-{{ $incomeCategory->id }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ $incomeCategory->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content px-0" id="bankAccountTabsContent">
        @foreach ($incomeCategories as $index => $incomeCategory)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                 id="content-{{ $incomeCategory->id }}"
                 role="tabpanel"
                 aria-labelledby="tab-{{ $incomeCategory->id }}">

                <div class="card-header d-flex justify-content-between align-items-center border-bottom-1 px-0 pt-0">
                    <h5 class="mb-0">{{ $incomeCategory->name }} Reports</h5>

                    <a class="btn btn-primary"
                       href="{{ url('/admin/incomes/category/report') }}?slug={{ $incomeCategory->slug }}&start_date={{ $startDate }}&end_date={{ $endDate }}" target="_blank"> 
                        View Full {{ $incomeCategory->name }} Category Income Report
                    </a>
                </div>

                <div class="card-body text-nowrap px-0">
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
                                               href="{{ route('admin.IncomesubcategoryReport', ['slug' => $incomeSubCategory->slug, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank">
                                                <i class="bx bx-edit-alt me-1"></i> View Report
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
        @endforeach
    </div>
@else
    <div class="text-center">No Income Categories Found.</div>
@endif
