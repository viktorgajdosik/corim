@if(session()->has('message'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
    class="fixed-top w-100 progress-container">
            <div class="progress-bar" role="progressbar"></div>
</div>
@endif
