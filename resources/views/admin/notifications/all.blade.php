@extends('admin.master')

@section('content')
    
<div class="container-fluid mt-4">
    <h4 class="text-center mb-4 border-bottom pb-3">All Notifications</h4>

    <!-- Notifications Grid -->
    <div class="row">
        @if ($notifications->isEmpty())
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    No notifications found.
                </div>
            </div>
        @else
        @foreach($notifications as $n)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <!-- Header: Occasion + Icons -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted text-capitalize">
                                {{ $n->occasion_name ?? '' }}
                            </small>
                            <div class="d-flex gap-2">
                                @if($n->email_sent)
                                    <i class="fa-solid fa-envelope text-primary"></i>
                                @endif
                                @if($n->sms_sent)
                                    <i class="fa-solid fa-comment-sms text-success"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="text-end text-muted small mb-3">
                            {{ \Carbon\Carbon::parse($n->sent_date)->diffForHumans() }}
                        </div>

                        <!-- Contact Name -->
                        @if($n->contact)
                            <div class="text-muted">
                                <strong>Sent To:</strong> {{ $n->contact->name }}
                            </div>
                        @endif

                        <!-- Message -->
                        <div class="rounded mb-3">
                            <strong>Message:</strong>
                            <p class="mb-0">{{ $n->message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @endif
        
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection


@section('scripts')
    
@endsection
