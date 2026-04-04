<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search article" />

            <x-ui.select-field name="categoryFilter" wire:model.live="categoryFilter" placeholder="All Category">
                <option value="all">All Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-ui.select-field>

            <x-ui.select-field name="statusFilter" wire:model.live="statusFilter" placeholder="All Status">
                <option value="all">All Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </x-ui.select-field>

            <x-ui.select-field name="visibilityFilter" wire:model.live="visibilityFilter" placeholder="All Visibility">
                <option value="all">All Visibility</option>
                <option value="public">Public</option>
                <option value="private">Private</option>
            </x-ui.select-field>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.journal.categories') }}" wire:navigate class="btn btn-outline rounded-xl">Categories</a>
            <a href="{{ route('admin.journal.create') }}" wire:navigate class="btn btn-info rounded-xl text-white">Write Article</a>
        </div>
    </div>

    @php
        $columns = [
            ['label' => 'Title'],
            ['label' => 'Category'],
            ['label' => 'Status'],
            ['label' => 'Visibility'],
            ['label' => 'Created', 'field' => 'created_at', 'sortable' => true],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <x-ui.table
        :columns="$columns"
        :data="$articles"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No articles found"
        emptySubMessage="Create your first journal article"
        emptyIcon="notebook-pen"
    >
        @foreach ($articles as $article)
            <tr wire:key="article-{{ $article->id }}" class="hover:bg-base-200/40 transition-colors">
                <td>
                    <p class="font-medium text-base-content">{{ $article->title }}</p>
                    <p class="mt-1 line-clamp-2 text-xs text-base-content/60">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 90) }}</p>
                </td>
                <td>{{ $article->category?->name ?? '-' }}</td>
                <td>
                    <span class="badge {{ $article->status === 'published' ? 'badge-success' : 'badge-ghost' }}">
                        {{ ucfirst($article->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $article->visibility === 'private' ? 'badge-warning' : 'badge-info' }}">
                        {{ ucfirst($article->visibility) }}
                    </span>
                </td>
                <td>
                    <p>{{ $article->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-base-content/50">{{ $article->created_at->format('H:i') }}</p>
                </td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li>
                            <a href="{{ route('admin.journal.edit', $article->id) }}" wire:navigate>Edit</a>
                        </li>
                        <li>
                            <a href="{{ route('journal.show', $article->slug) }}{{ $article->visibility === 'private' && $article->access_token ? '?key='.$article->access_token : '' }}" target="_blank" rel="noreferrer">View</a>
                        </li>
                        <li>
                            <button type="button" wire:click="deleteArticle({{ $article->id }})" wire:confirm="Delete this article?" class="text-error">
                                Delete
                            </button>
                        </li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $articles->links() }}</div>
</section>
