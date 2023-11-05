<style>
.progress-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: white;
    overflow: hidden;
}

.progress-bar {
    width: 100%;
    height: 100%;
    bottom: 0;
    background-color: #007bff;
    animation: progressAnimation 2s linear forwards;
}

@keyframes progressAnimation {
    0% {
        width: 0;
    }
    100% {
        width: 100%;
    }
}
</style>

@if(session()->has('message'))
<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show"
    class="fixed-top w-100 text-primary text-center py-3" style="background-color: aliceblue; display: flex; align-items: center;">
        <p class="m-0">
            {{ session('message') }}
        </p>
        <div class="progress-container">
            <div class="progress-bar" role="progressbar"></div>
        </div>
</div>
@endif
