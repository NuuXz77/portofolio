<section class="relative z-10 w-full max-w-md">
    <div class="glass-card rounded-3xl border border-white/15 bg-base-100/65 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
        <div class="mb-8 text-center">
            <p class="text-sm uppercase tracking-[0.2em] text-info">Admin Portal</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Welcome Back</h1>
            <p class="mt-2 text-sm text-base-content/70">Sign in to manage your dynamic portfolio content.</p>
        </div>

        <form wire:submit="login" class="space-y-5">
            <x-ui.input-field
                label="Email"
                name="email"
                type="email"
                icon="mail"
                placeholder="admin@wisnu.dev"
                labelClass="label-text mb-1 text-sm text-base-content/70"
                inputClass="border-white/15 bg-base-200/60"
                wire:model.defer="email"
                autocomplete="email"
            />

            <x-ui.input-field
                label="Password"
                name="password"
                type="password"
                icon="lock"
                placeholder="••••••••"
                labelClass="label-text mb-1 text-sm text-base-content/70"
                inputClass="border-white/15 bg-base-200/60"
                wire:model.defer="password"
                autocomplete="current-password"
            />

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
