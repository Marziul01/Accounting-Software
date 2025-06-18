@if($row->expense_category_id != 7)
<div class="d-flex align-items-center gap-1 cursor-pointer">
    <button class="btn btn-sm btn-outline-secondary openEditModal"
        data-id="{{ $row->id }}"
        data-name="{{ $row->name }}"
        data-slug="{{ $row->slug }}"
        data-category-id="{{ $row->expense_category_id }}"
        data-sub-category-id="{{ $row->expense_sub_category_id }}"
        data-amount="{{ $row->amount }}"
        data-date="{{ $row->date }}"
        data-description="{{ $row->description }}"
        {{ Auth::user()->access->expense == 1 ? 'disabled' : '' }}>
        <i class="bx bx-edit-alt me-1"></i> Edit
    </button>

    <form action="{{ route('expense.destroy', $row->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->expense == 1 ? 'disabled' : '' }}">
            <i class="bx bx-trash me-1"></i> Delete
        </button>
    </form>
</div>
@endif