<section class="grid gap-6 xl:grid-cols-[1.05fr,1.45fr]">
    <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-white">Contact Info</h2>
        <p class="mt-1 text-sm text-base-content/60">Update contact channels displayed on landing page.</p>

        <form wire:submit="saveContactInfo" class="mt-5 space-y-4">
            <x-ui.input-field label="Email" name="email" type="email" wire:model.defer="email" required />
            <x-ui.input-field label="WhatsApp" name="whatsapp" wire:model.defer="whatsapp" required />
            <x-ui.input-field label="LinkedIn" name="linkedin" wire:model.defer="linkedin" required />
            <x-ui.input-field label="GitHub" name="github" wire:model.defer="github" required />

            <button type="submit" class="btn btn-info rounded-xl text-white">Save Contact Info</button>
        </form>
    </article>

    <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-white">Inbox Messages</h2>
            <div class="flex gap-2">
                <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search message" inputClass="input-sm" />
                <x-ui.select-field name="statusFilter" wire:model.live="statusFilter" selectClass="select-sm">
                    <option value="all">All</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </x-ui.select-field>
            </div>
        </div>

        <x-ui.table wrapperClass="mt-4 rounded-xl" tableClass="table-zebra">
            <x-slot:head>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($messages as $message)
                <tr>
                    <td>{{ $message->name }}</td>
                    <td>{{ $message->email }}</td>
                    <td class="max-w-sm truncate">{{ $message->message }}</td>
                    <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $message->is_read ? 'badge-ghost' : 'badge-info' }}">{{ $message->is_read ? 'Read' : 'Unread' }}</span>
                    </td>
                    <td class="text-right">
                        <x-ui.dropdown-action>
                            @if (! $message->is_read)
                                <li><button type="button" wire:click="markAsRead({{ $message->id }})">Mark as Read</button></li>
                            @endif
                            <li><button type="button" wire:click="deleteMessage({{ $message->id }})" wire:confirm="Delete this message?" class="text-error">Delete</button></li>
                        </x-ui.dropdown-action>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-base-content/55">No messages found.</td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">{{ $messages->links() }}</div>
    </article>
</section>
