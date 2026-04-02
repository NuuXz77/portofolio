@if (session()->has('success'))
    <x-ui.toast type="success" :message="session('success')" />
@endif

@if (session()->has('error'))
    <x-ui.toast type="error" :message="session('error')" />
@endif