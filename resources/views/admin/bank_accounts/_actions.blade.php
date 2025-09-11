@if($row->from === null || $row->from === 'scheduled')

<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button type="button" class="btn btn-sm btn-outline-secondary openBankEditModal"
        data-id="{{ $row->id }}"
        data-name="{{ $row->name }}"
        data-slug="{{ $row->slug }}"
        data-transaction-id="{{ $row->transaction_id }}"
        data-amount="{{ $row->amount }}"
        data-date="{{ $row->transaction_date }}"
        data-type="{{ $row->transaction_type }}"
        data-description="{{ $row->description }}"
        data-bank-id="{{ $row->bank_account_id }}"
        data-transfared-from="{{ $row->transfer_to ? $row->bank_account_id : '' }}"
        data-transfared-to="{{ $row->transfer_to ? $row->transfer_to : '' }}"
        {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>

    <form action="{{ route('banktransaction.destroy', $row->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->bankbook == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
</div>
@endif