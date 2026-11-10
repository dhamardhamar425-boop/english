@if (session('success'))
    <div class="mb-4">
        <x-alert type="success" :message="session('success')" />
    </div>
@endif

@if (session('error'))
    <div class="mb-4">
        <x-alert type="error" :message="session('error')" />
    </div>
@endif
