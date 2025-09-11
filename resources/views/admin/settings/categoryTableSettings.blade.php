@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card mb-4 contact-card">
            <div class="card-header d-flex justify-content-between align-items-start border-bottom-1 flex-column flex-md-row gap-3 align-items-md-center">
                <div class="">
                    <h5 class="mb-0">Category & Table Settings</h5>
                </div>
            </div>
        </div>
            
                    @php
                        $categories = [
                            'Income' => ['income_category'],
                            'Expense' => ['expense_category'],
                            'Investment' => ['investments_category'],
                            'Asset' => ['asset_category_table', 'asset_name_table', 'asset_category'],
                            'Liability' => ['liability_category_table', 'liability_name_table', 'liability_category'],
                            'Report Buttons' => ['report_up', 'report_back'],
                        ];
                    @endphp

                    @foreach($categories as $title => $fields)
                    <div class="card contact-card p-4 rounded-lg shadow mb-4">
                        <h5 class=" mb-4">{{ $title }}</h5>
                        <div class="row">
                            @foreach($fields as $field)
                            <div class="flex p-2 col-6 col-md-3">
                                <span class="text-capitalize mb-2">{{ str_replace('_', ' ', $field) }}</span>
                                <div class="flex items-center space-x-2 mt-3">
                                    <div class="flex overflow-hidden" data-field="{{ $field }}">
                                        <button class="px-3 py-1 text-sm btn status-btn {{ ($fieldStatuses[$field] ?? 2) == 2 ? 'btn-primary' : 'btn-outline-primary' }}" data-status="2">
                                            Show
                                        </button>
                                        <button class="px-3 py-1 text-sm btn status-btn {{ ($fieldStatuses[$field] ?? 2) == 1 ? 'btn-primary' : 'btn-outline-primary' }}" data-status="1">
                                            Hide
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </div>
                    </div>
                    @endforeach
                


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

<script>
    document.querySelectorAll('.status-btn').forEach(button => {
        button.addEventListener('click', function () {
            const parent = this.closest('[data-field]');
            const field = parent.getAttribute('data-field');
            const selectedStatus = this.getAttribute('data-status');

            // If already selected, do nothing
            if (this.classList.contains('btn-primary')) return;

            if (confirm(`Are you sure you want to change "${field.replace(/_/g, ' ')}" to ${selectedStatus == 2 ? 'Show' : 'Hide'}?`)) {
                fetch("{{ route('admin.settings.updateCategoryField') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ field: field, status: selectedStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset all buttons to outline style
                        parent.querySelectorAll('.status-btn').forEach(btn => {
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-outline-primary');
                        });

                        // Set clicked button as active
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');

                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message || 'Update failed.');
                    }
                })
                .catch(() => {
                    toastr.error('Something went wrong.');
                });
            }
        });
    });
</script>



@endsection
