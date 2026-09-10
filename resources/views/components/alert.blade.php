@if(auth()->check())
    @include('components.success-modal')
    
    @if(session('error'))
        <div class="mk-alert mk-alert-error" style="margin-bottom:20px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:1.125rem;"></i>
            <div style="flex-grow:1; font-weight:600;">{{ session('error') }}</div>
        </div>
    @endif
@else
    @if(session('success'))
        <div class="mk-alert mk-alert-success">
            <i class="fa-solid fa-circle-check" style="font-size:1.125rem;"></i>
            <div style="flex-grow:1;">{{ session('success') }}</div>
        </div>
    @endif
@endif

@if(session('error'))
    <div class="mk-alert mk-alert-error">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:1.125rem;"></i>
        <div style="flex-grow:1;">{{ session('error') }}</div>
    </div>
@endif

@if(session('info'))
    <div class="mk-alert mk-alert-info">
        <i class="fa-solid fa-circle-info" style="font-size:1.125rem;"></i>
        <div style="flex-grow:1;">{{ session('info') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="mk-alert mk-alert-error">
        <i class="fa-solid fa-circle-xmark" style="font-size:1.125rem;"></i>
        <div style="flex-grow:1;">
            <ul style="list-style:none; padding-left:0; margin-bottom:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
