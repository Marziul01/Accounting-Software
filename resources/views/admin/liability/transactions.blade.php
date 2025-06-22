@extends('admin.master')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Basic Bootstrap Table -->
        <div class="card ">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-1">
                <h5 class="mb-0">{{ $liability->name }} Liability Transactions</h5>
                <button type="button" class="btn btn-primary {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}"
                    data-bs-toggle="modal" data-bs-target="#updateModal{{ $liability->id }}">Add New Transactions </button>
            </div>
            <div class="card-body position-relative ">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="myTable ">
                        <thead>
                            <tr>
                                <th colspan="5" class="w-50">
                                            <div class="text-center w-full py-2">Deposit</div>
                                        </th>
                                        <th colspan="5" class="w-50">
                                            <div class="text-center w-full py-2">Withdraw</div>
                                        </th>
                            </tr>
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Desc</th>
                                <th>Actions</th>

                                <th>Sl</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Desc</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $depositTransactions = $transactions->where('transaction_type', 'Deposit')->values();
                                $withdrawTransactions = $transactions->where('transaction_type', 'Withdraw')->values();
                                $maxCount = max($depositTransactions->count(), $withdrawTransactions->count());
                            @endphp

                            @for ($i = 0; $i < $maxCount; $i++)
                                <tr>
                                    {{-- Deposit --}}
                                    @if (isset($depositTransactions[$i]))
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $depositTransactions[$i]->transaction_date }}</td>
                                        <td>{{ $depositTransactions[$i]->amount }}</td>
                                        <td>{{ $depositTransactions[$i]->description }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 cursor-pointer">
                                                <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}"
                                                    href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edittranModal{{ $depositTransactions[$i]->id }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form
                                                    action="{{ route('liabilitytransaction.destroy', $depositTransactions[$i]->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @else
                                        <td colspan="5" class="text-center text-muted">-</td>
                                    @endif

                                    {{-- Withdraw --}}
                                    @if (isset($withdrawTransactions[$i]))
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $withdrawTransactions[$i]->transaction_date }}</td>
                                        <td>{{ $withdrawTransactions[$i]->amount }}</td>
                                        <td>{{ $withdrawTransactions[$i]->description }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 cursor-pointer">
                                                <a class="btn btn-sm btn-outline-secondary {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}"
                                                    href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edittranModal{{ $withdrawTransactions[$i]->id }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form
                                                    action="{{ route('liabilitytransaction.destroy', $withdrawTransactions[$i]->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger delete-confirm {{ Auth::user()->access->liability == 1 ? 'disabled' : '' }}">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @else
                                        <td colspan="5" class="text-center text-muted">-</td>
                                    @endif
                                </tr>
                            @endfor
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="updateModal{{ $liability->id }}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> Add Liability Transaction </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="assetForms{{ $liability->id }}">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="liability_id" value="{{ $liability->id }}">
                            <div class="col-12 mb-3">
                                <label>Transation Type</label>
                                <select name="transaction_type" id="" class="form-select">
                                    <option value="Deposit">জমা </option>
                                    <option value="Withdraw">উত্তোলন</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label>Amount</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label>Transaction Date</label>
                                <input type="date" name="transaction_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"> </textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($transactions->isNotEmpty())
        @foreach ($transactions as $liabilityTransaction)
            <div class="modal fade" id="edittranModal{{ $liabilityTransaction->id }}">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Liability Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editTransCategoryForms{{ $liabilityTransaction->id }}"
                            action="{{ route('liabilitytransaction.update', $liabilityTransaction->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <input type="hidden" name="liability_id"
                                        value="{{ $liabilityTransaction->liability_id }}">
                                    <div class="col-12 mb-3">
                                        <label>Transation Type</label>
                                        <select name="transaction_type" id="" class="form-select">
                                            <option value="Deposit"
                                                {{ $liabilityTransaction->transaction_type == 'Deposit' ? 'Selected' : '' }}>
                                                জমা </option>
                                            <option value="Withdraw"
                                                {{ $liabilityTransaction->transaction_type == 'Withdraw' ? 'Selected' : '' }}>
                                                উত্তোলন</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label>Amount</label>
                                        <input type="number" name="amount" class="form-control" required
                                            value="{{ $liabilityTransaction->amount }}">
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label>Transaction Date</label>
                                        <input type="date" name="transaction_date" class="form-control" required
                                            value="{{ $liabilityTransaction->transaction_date }}">
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control"> {{ $liabilityTransaction->description }} </textarea>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex justify-content-center flex-column align-items-center auth-success-modal">
                    <img src="{{ asset('admin-assets/img/double-check.gif') }}" width="25%" alt="">
                    <h5 class="modal-title text-center" id="successModalLabel">Success</h5>
                    <p id="successMessage" class="text-center">Login successful!</p>
                </div>
            </div>
        </div>
    </div>

    <div id="fullscreenLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="display:flex; justify-content:center; align-items:center; width:100%; height:100%;">
             <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    @if ($transactions->isNotEmpty())
    @endif
    <script>
        const banglaToEnglishMap = {
            'অ': 'a',
            'আ': 'aa',
            'ই': 'i',
            'ঈ': 'ii',
            'উ': 'u',
            'ঊ': 'uu',
            'এ': 'e',
            'ঐ': 'oi',
            'ও': 'o',
            'ঔ': 'ou',
            'ক': 'k',
            'খ': 'kh',
            'গ': 'g',
            'ঘ': 'gh',
            'ঙ': 'ng',
            'চ': 'ch',
            'ছ': 'chh',
            'জ': 'j',
            'ঝ': 'jh',
            'ঞ': 'n',
            'ট': 't',
            'ঠ': 'th',
            'ড': 'd',
            'ঢ': 'dh',
            'ণ': 'n',
            'ত': 't',
            'থ': 'th',
            'দ': 'd',
            'ধ': 'dh',
            'ন': 'n',
            'প': 'p',
            'ফ': 'ph',
            'ব': 'b',
            'ভ': 'bh',
            'ম': 'm',
            'য': 'j',
            'র': 'r',
            'ল': 'l',
            'শ': 'sh',
            'ষ': 'ss',
            'স': 's',
            'হ': 'h',
            'ড়': 'r',
            'ঢ়': 'rh',
            'য়': 'y',
            'ৎ': 't',
            'ং': 'ng',
            'ঃ': '',
            'ঁ': ''
        };

        function transliterate(text) {
            return text.split('').map(char => banglaToEnglishMap[char] || char).join('');
        }

        function generateSlug(text) {
            const englishText = transliterate(text);
            return englishText
                .toLowerCase()
                .replace(/[^\w\s]/gi, '') // remove special characters
                .trim()
                .replace(/\s+/g, '_'); // replace spaces with "_"
        }

        function attachSlugListener(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.addEventListener('shown.bs.modal', () => {
                const input = modal.querySelector('.name-input');
                const slugInput = modal.querySelector('.slug-output');
                if (input && slugInput) {
                    input.addEventListener('input', function() {
                        slugInput.value = generateSlug(this.value);
                    });
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('form[id^="editTransCategoryForms"] button[type="submit"]').on('click', function(e) {
                e.preventDefault();

                toastr.clear();

                let form = $(this).closest('form')[0]; // get the closest form to the clicked button
                let formData = new FormData(form);

                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                $.ajax({
                    url: form.action,
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#successMessage').text(response.message);
                        $('#successModal').modal('show');

                        form.reset();
                        $('#edittranModal' + response.id).modal('hide');

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {


            $('form[id^="assetForms"] button[type="submit"]').on('click', function(e) {
                e.preventDefault();


                toastr.clear();

                let form = $(this).closest('form')[0]; // ✅ get the actual form element
                let formData = new FormData(form); // ✅ pass the form here

                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                $('#fullscreenLoader').fadeIn();

                $.ajax({
                    url: "{{ route('liabilitytransaction.store') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#fullscreenLoader').fadeOut();
                        $('#successMessage').text(response
                        .message); // Set dynamic success message
                        $('#successModal').modal('show');

                        form.reset();
                        $('#updateModal' + response.id).modal('hide');

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        $('#fullscreenLoader').fadeOut();
                        console.log('Error:', xhr);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                toastr.error(errors[key][0]);
                            }
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
