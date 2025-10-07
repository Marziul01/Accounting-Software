@if($row->type == 'Investment')
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        id="loadcurrentAssetTransactionModal" data-url="{{ route('admin.editinvestmenttransaction.modal', $row->other->id) }}"
        {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>

    <form action="{{ route('investmenttransaction.destroy', $row->other->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->investment == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
</div>
@endif

@if($row->type == 'Income')
@if ($row->other->income_category_id != 13)
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        id="loadIncomeModal" data-url="{{ route('admin.editincome.modal', $row->other->id) }}"
        {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>

    <form action="{{ route('income.destroy', $row->other->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->income == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
</div>
@endif
@endif

@if($row->type == 'Expense')
@if ($row->other->expense_category_id != 7)
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        id="loadExpenseModal" data-url="{{ route('admin.editexpense.modal', $row->other->id) }}"
        {{ Auth::user()->access->expense == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>

    <form action="{{ route('expense.destroy', $row->other->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->expense == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
</div>
@endif
@endif

@if($row->type == 'Asset')
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        id="loadAssetModal" data-url="{{ route('admin.editassettransaction.modal', $row->other->id) }}"
        {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>
    
    <form action="{{ route('assettransaction.destroy', $row->other->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>

    <a href="{{ route('asset.invoice', $row->other->id) }}" 
        target="_blank" 
        class="btn btn-sm btn-outline-primary">
        <i class="fa-solid fa-download me-1"></i> Invoice
    </a>
</div>
@endif

@if($row->type == 'Liability')
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        id="loadLiabilityModal" data-url="{{ route('admin.editliabilitytransaction.modal', $row->other->id) }}"
        {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>
    
    <form action="{{ route('liabilitytransaction.destroy', $row->other->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
    <a href="{{ route('liability.invoice', $row->other->id) }}" 
        target="_blank" 
        class="btn btn-sm btn-outline-primary">
        <i class="fa-solid fa-download  me-1"></i> Invoice
    </a>
</div>
@endif

@if($row->type == 'BankTransaction')
    {{-- Case: from is null --}}
    @if ($row->from === null)
        <div class="d-flex align-items-center gap-1 cursor-pointer">
            <button class="btn btn-sm btn-outline-secondary openEditModal"
                data-url="{{ route('admin.editbanktransaction.modal', $row->other->id) }}"
                {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}>
                <i class="bx bx-edit-alt me-1"></i> Edit
            </button>

            <form action="{{ route('banktransaction.destroy', $row->other->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    @endif

    {{-- Case: from is scheduled --}}
    @if ($row->from === 'scheduled')
        <div class="d-flex align-items-center gap-1 cursor-pointer">
            <button class="btn btn-sm btn-outline-secondary openEditModal"
                data-url="{{ route('admin.editbanktransaction.modal', $row->other->id) }}"
                {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}>
                <i class="bx bx-edit-alt me-1"></i> Edit
            </button>

            <form action="{{ route('banktransaction.destroy', $row->other->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}">
                    <i class="bx bx-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    @endif
@endif