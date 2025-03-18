@if($show)
    <div class="alert alert-warning text-center py-1 small"
         role="alert"
         style="position: fixed; top: 0; width: 100%; height: 40px; z-index: 1050;">
        Please verify your email to access all features.
        <a href="{{ route('verification.notice') }}" class="alert-link">Verify Now</a>
    </div>
@endif
