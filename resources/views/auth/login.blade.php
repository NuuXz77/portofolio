<section class="relative z-10 w-full max-w-md">
    <div class="rounded-3xl border border-white/15 bg-base-100/65 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
        <div class="mb-8 text-center">
            <p class="text-sm uppercase tracking-[0.2em] text-info">Admin Portal</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Welcome Back</h1>
            <p class="mt-2 text-sm text-base-content/70">Sign in to manage your dynamic portfolio content.</p>
        </div>

        <form wire:submit="login" class="space-y-5">
            <label class="form-control">
                <span class="label-text mb-1 text-sm text-base-content/70">Email</span>
                <label class="input input-bordered flex items-center gap-2 rounded-xl border-white/15 bg-base-200/60">
                    <i data-lucide="mail" class="h-4 w-4 text-base-content/60"></i>
                    <input wire:model.defer="email" type="email" class="grow" placeholder="admin@wisnu.dev" autocomplete="email">
                </label>
                @error('email')
                    <span class="mt-2 text-xs text-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="form-control">
                <span class="label-text mb-1 text-sm text-base-content/70">Password</span>
                <label class="input input-bordered flex items-center gap-2 rounded-xl border-white/15 bg-base-200/60">
                    <i data-lucide="lock" class="h-4 w-4 text-base-content/60"></i>
                    <input wire:model.defer="password" type="password" class="grow" placeholder="••••••••" autocomplete="current-password">
                </label>
                @error('password')
                    <span class="mt-2 text-xs text-error">{{ $message }}</span>
                @enderror
            </label>

            <label class="label cursor-pointer justify-start gap-3">
                <input wire:model="remember" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                <span class="label-text text-sm text-base-content/75">Remember me</span>
            </label>

            <button type="submit" class="btn btn-info w-full rounded-xl text-white" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                    <span class="loading loading-spinner loading-sm"></span>
                    Processing...
                </span>
            </button>
        </form>
    </div>
</section>
