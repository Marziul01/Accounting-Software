<div class="dropdown" data-bs-boundary="viewport"> <!-- 👈  only this line added -->
    <button class="btn p-0 btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false"
        style=" padding: 0px 5px !important ;">
        Actions <i class="bx bx-dots-vertical-rounded" style="font-size: 20px !important;"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cardOpt6">
        <div class="d-flex flex-column gap-1 cursor-pointer">
            {{-- <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                href="" data-bs-toggle="modal" data-bs-target="#updateModal{{ $row->id }}"><i
                    class="bx bx-wallet me-1"></i> Add New Transaction</a> --}}
                    <a href="javascript:void(0)" 
                    class="btn btn-sm btn-primary add-transaction-btn {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" 
                    data-id="{{ $row->id }}">
                    <i class="bx bx-wallet me-1"></i> Add New Transaction
                    </a>

            <a class=" btn btn-sm btn-outline-primary" href="{{ route('seeInvestmentTrans', $row->slug) }}"><i
                    class="bx bx-wallet me-1"></i> See All Transactions</a>
            {{-- <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i
                    class="bx bx-edit-alt me-1"></i> Edit</a> --}}
                    <a href="#" 
                    class="btn btn-sm btn-outline-secondary edit-btn {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                    data-id="{{ $row->id }}">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>


            {{-- <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                href="" data-bs-toggle="modal" data-bs-target="#incomeModal{{ $row->id }}"><i
                    class="bx bx-wallet me-1"></i> Gain from Investment</a> --}}
                    <a href="#" class="btn btn-sm btn-primary openIncomeModal {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" 
                        data-id="{{ $row->id }}" data-category="{{ $row->investment_category_id == 4 ? '8' : '9' }}"> 
                        <i class="bx bx-wallet me-1"></i> Gain from Investment 
                    </a>
            {{-- <a class=" btn btn-sm btn-primary {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}"
                href="" data-bs-toggle="modal" data-bs-target="#expenseModal{{ $row->id }}"><i
                    class="bx bx-wallet me-1"></i> Loss from Investment</a> --}}

                    <a href="#" 
   class="btn btn-sm btn-primary openExpenseModal
          {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}" 
   data-id="{{ $row->id }}" 
   data-category="{{ $row->investment_category_id == 4 ? '14' : '15' }}">
   <i class="bx bx-wallet me-1"></i> Loss from Investment
</a>

            <a class=" btn btn-sm btn-outline-primary" href="{{ route('seeInvestmentsinex', $row->slug) }}"><i
                    class="bx bx-wallet me-1"></i> See All Gain/Loss</a>

            <form action="{{ route('investment.destroy', $row->id) }}" method="POST" class="d-inline w-100">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="btn btn-sm btn-outline-danger w-100 delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
