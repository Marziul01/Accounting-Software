<div class="dropdown" data-bs-boundary="viewport">
    <button class="btn p-0 btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false"
        style=" padding: 0px 5px !important ;">
        Actions <i class="bx bx-dots-vertical-rounded" style="font-size: 20px !important;"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="cardOpt6">
        <div class="d-flex flex-column gap-1 cursor-pointer">
            {{-- <a class=" btn btn-sm btn-primary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}"
                href="" data-bs-toggle="modal" data-bs-target="#updateModal{{ $row->id }}"><i
                    class="bx bx-wallet me-1"></i> Update Asset Transaction
            </a> --}}
            <a class="btn btn-sm btn-primary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}"
   href="javascript:void(0)"
   data-id="{{ $row->id }}"
   data-bs-toggle="modal"
   data-bs-target="#updateAssetModal">
   <i class="bx bx-wallet me-1"></i> Update Asset Transaction
</a>

            <a class=" btn btn-sm btn-outline-primary d-block" href="{{ route('seeAssetTrans', $row->slug) }}"><i
                    class="bx bx-wallet me-1"></i> See All Asset Transactions
            </a>

            {{-- <a class=" btn btn-sm btn-outline-secondary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}"
                href="" data-bs-toggle="modal" data-bs-target="#editModal{{ $row->id }}"><i
                    class="bx bx-edit-alt me-1"></i> Edit
            </a> --}}
            <a class="btn btn-sm btn-outline-secondary d-block {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}"
   href="javascript:void(0);" 
   data-id="{{ $row->id }}" 
   id="editAssetBtn">
   <i class="bx bx-edit-alt me-1"></i> Edit
</a>
            <form action="{{ route('asset.destroy', $row->id) }}" method="POST" class="w-100">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="btn btn-sm btn-outline-danger w-100 delete-confirm {{ Auth::user()->access->asset == 1 ? 'disabled' : '' }}"><i
                        class="bx bx-trash me-1"></i> Delete</button>
            </form>
        </div>
    </div> 
</div>
