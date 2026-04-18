<section class="grid gap-6 xl:grid-cols-[1.05fr,1.45fr]">
    @php
        $messageColumns = [
            ['label' => 'Name'],
            ['label' => 'Email'],
            ['label' => 'Message'],
            ['label' => 'Date'],
            ['label' => 'Status'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-white">Contact Info</h2>
        <p class="mt-1 text-sm text-base-content/60">Update contact channels and bilingual contact section copy.</p>

        <form wire:submit="saveContactInfo" class="mt-5 space-y-4">
            <div class="tabs tabs-boxed inline-flex rounded-xl border border-base-content/10 bg-base-100/55 p-1">
                <button type="button" wire:click="$set('editingLocale', 'id')" class="tab rounded-lg px-4 {{ $editingLocale === 'id' ? 'tab-active bg-info text-base-content' : '' }}">ID</button>
                <button type="button" wire:click="$set('editingLocale', 'en')" class="tab rounded-lg px-4 {{ $editingLocale === 'en' ? 'tab-active bg-info text-base-content' : '' }}">EN</button>
            </div>

            @if ($editingLocale === 'id')
                <x-ui.input-field label="Section Badge (ID)" name="sectionBadgeId" wire:model.defer="sectionBadgeId" required />
                <x-ui.input-field label="Section Title (ID)" name="sectionTitleId" wire:model.defer="sectionTitleId" required />
                <x-ui.textarea-field label="Section Description (ID)" name="sectionDescriptionId" wire:model.defer="sectionDescriptionId" :rows="4" required />
                <x-ui.input-field label="Form Title (ID)" name="formTitleId" wire:model.defer="formTitleId" required />
                <x-ui.input-field label="Submit Button Text (ID)" name="submitTextId" wire:model.defer="submitTextId" required />
            @else
                <x-ui.input-field label="Section Badge (EN)" name="sectionBadgeEn" wire:model.defer="sectionBadgeEn" required />
                <x-ui.input-field label="Section Title (EN)" name="sectionTitleEn" wire:model.defer="sectionTitleEn" required />
                <x-ui.textarea-field label="Section Description (EN)" name="sectionDescriptionEn" wire:model.defer="sectionDescriptionEn" :rows="4" required />
                <x-ui.input-field label="Form Title (EN)" name="formTitleEn" wire:model.defer="formTitleEn" required />
                <x-ui.input-field label="Submit Button Text (EN)" name="submitTextEn" wire:model.defer="submitTextEn" required />
            @endif

            <div class="divider text-xs uppercase tracking-[0.18em] text-base-content/50">Channels</div>

            <x-ui.input-field label="Email" name="email" type="email" wire:model.defer="email" required />
            <x-ui.input-field label="WhatsApp" name="whatsapp" wire:model.defer="whatsapp" required />
            <x-ui.input-field label="LinkedIn" name="linkedin" wire:model.defer="linkedin" required />
            <x-ui.input-field label="GitHub" name="github" wire:model.defer="github" required />

            <button type="submit" class="btn btn-info rounded-xl text-white">Save Contact Info</button>
        </form>
    </article>

    <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
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

        <x-ui.table
            wrapperClass="mt-4 rounded-xl"
            tableClass="table-zebra"
            :columns="$messageColumns"
            :data="$messages"
            emptyMessage="No messages found."
        >
            @foreach ($messages as $message)
                <tr wire:key="message-{{ $message->id }}">
                    <td>{{ $message->name }}</td>
                    <td>{{ $message->email }}</td>
                    <td class="max-w-sm truncate">{{ $message->message }}</td>
                    <td>{{ $message->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge badge-soft {{ $message->is_read ? 'badge-ghost' : 'badge-info' }}">{{ $message->is_read ? 'Read' : 'Unread' }}</span>
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
            @endforeach
        </x-ui.table>

        <div class="mt-4">{{ $messages->links() }}</div>
    </article>
</section>
