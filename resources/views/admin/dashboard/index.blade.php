<section class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-base-content/60">Total Projects</p>
                    <p class="mt-2 text-3xl font-semibold text-base-content">{{ $totalProjects }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info/15 text-info">
                    <i data-lucide="folder" class="h-5 w-5"></i>
                </span>
            </div>
        </article>
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-base-content/60">Total Skills</p>
                    <p class="mt-2 text-3xl font-semibold text-base-content">{{ $totalSkills }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info/15 text-info">
                    <i data-lucide="cpu" class="h-5 w-5"></i>
                </span>
            </div>
        </article>
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-base-content/60">Total Testimonials</p>
                    <p class="mt-2 text-3xl font-semibold text-base-content">{{ $totalTestimonials }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info/15 text-info">
                    <i data-lucide="message-square" class="h-5 w-5"></i>
                </span>
            </div>
        </article>
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-base-content/60">Total Articles</p>
                    <p class="mt-2 text-3xl font-semibold text-base-content">{{ $totalArticles }}</p>
                    <p class="mt-1 text-xs text-info">Published: {{ $publishedArticles }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info/15 text-info">
                    <i data-lucide="notebook" class="h-5 w-5"></i>
                </span>
            </div>
        </article>
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-base-content/60">Total Messages</p>
                    <p class="mt-2 text-3xl font-semibold text-base-content">{{ $totalMessages }}</p>
                    <p class="mt-1 text-xs text-info">Unread: {{ $unreadMessages }}</p>
                </div>
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info/15 text-info">
                    <i data-lucide="mail" class="h-5 w-5"></i>
                </span>
            </div>
        </article>
    </div>

    <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm" data-admin-live-users data-endpoint="/api/active-users" data-window="60">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-base-content/60">Realtime analytics</p>
                <h2 class="mt-1 text-xl font-semibold text-base-content">Live Active Users</h2>
            </div>
            <div class="glass-card-soft rounded-xl border border-base-content/10 bg-base-200/60 px-4 py-3 text-right">
                <p class="text-xs uppercase tracking-[0.16em] text-base-content/55">Current</p>
                <p class="mt-1 text-3xl font-semibold text-info" data-live-current>0</p>
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="glass-card-soft rounded-xl border border-base-content/10 bg-base-200/50 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.12em] text-base-content/55">Current Active</p>
                <p class="mt-1 text-lg font-semibold text-base-content" data-live-stat-current>0</p>
            </div>
            <div class="glass-card-soft rounded-xl border border-base-content/10 bg-base-200/50 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.12em] text-base-content/55">Peak (Window)</p>
                <p class="mt-1 text-lg font-semibold text-base-content" data-live-stat-peak>0</p>
            </div>
            <div class="glass-card-soft rounded-xl border border-base-content/10 bg-base-200/50 px-4 py-3">
                <p class="text-xs uppercase tracking-[0.12em] text-base-content/55">Average (Window)</p>
                <p class="mt-1 text-lg font-semibold text-base-content" data-live-stat-avg>0</p>
            </div>
        </div>

        <div class="mt-5">
            <div data-live-skeleton class="grid gap-3">
                <div class="skeleton h-5 w-48"></div>
                <div class="skeleton h-64 w-full rounded-xl"></div>
            </div>
            <div data-live-chart-wrapper class="glass-card-soft hidden rounded-2xl border border-info/20 bg-linear-to-br from-info/10 via-base-100/80 to-base-200/70 p-3 shadow-inner">
                <div data-live-chart class="min-h-65 w-full rounded-xl"></div>
            </div>
            <p class="mt-2 text-xs text-base-content/55" data-live-status>Waiting for real-time data...</p>
        </div>
    </article>

    <div class="grid gap-6 lg:grid-cols-[1.4fr,1fr]">
        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-base-content">Recent Activity</h2>
                <span class="text-xs text-base-content/50">Latest updates</span>
            </div>

            <div class="space-y-3">
                @forelse ($activities as $activity)
                    <div class="glass-card-soft rounded-xl border border-base-content/10 bg-base-200/50 px-4 py-3">
                        <p class="text-sm font-medium text-base-content">{{ ucfirst($activity->action) }} • {{ $activity->module ?? 'General' }}</p>
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

        <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-base-content">Quick Actions</h2>
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
