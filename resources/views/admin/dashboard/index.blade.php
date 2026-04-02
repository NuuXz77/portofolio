<section class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <p class="text-sm text-base-content/60">Total Projects</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $totalProjects }}</p>
        </article>
        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <p class="text-sm text-base-content/60">Total Skills</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $totalSkills }}</p>
        </article>
        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <p class="text-sm text-base-content/60">Total Testimonials</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $totalTestimonials }}</p>
        </article>
        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <p class="text-sm text-base-content/60">Total Messages</p>
            <p class="mt-2 text-3xl font-semibold text-white">{{ $totalMessages }}</p>
            <p class="mt-1 text-xs text-info">Unread: {{ $unreadMessages }}</p>
        </article>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.4fr,1fr]">
        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
                <span class="text-xs text-base-content/50">Latest updates</span>
            </div>

            <div class="space-y-3">
                @forelse ($activities as $activity)
                    <div class="rounded-xl border border-base-content/10 bg-base-200/50 px-4 py-3">
                        <p class="text-sm font-medium text-white">{{ ucfirst($activity->action) }} • {{ $activity->module ?? 'General' }}</p>
                        <p class="mt-1 text-xs text-base-content/65">{{ $activity->description ?: 'No description provided.' }}</p>
                        <p class="mt-2 text-[11px] text-base-content/45">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-base-content/20 px-4 py-6 text-center text-sm text-base-content/60">
                        No activity logs yet.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-white">Quick Actions</h2>
            <p class="mt-1 text-sm text-base-content/60">Jump directly to important CMS modules.</p>

            <div class="mt-4 grid gap-2">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" wire:navigate class="btn btn-outline btn-info justify-start rounded-xl">
                        <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        </article>
    </div>
</section>
