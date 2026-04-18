<section class="space-y-4">
    @php
        $columns = [
            ['label' => 'Logo'],
            ['label' => 'Institution Name', 'field' => 'institution_name', 'sortable' => true],
            ['label' => 'Major', 'field' => 'major', 'sortable' => true],
            ['label' => 'Degree', 'field' => 'degree', 'sortable' => true],
            ['label' => 'Year Range', 'field' => 'start_year', 'sortable' => true],
            ['label' => 'Status', 'field' => 'is_active', 'sortable' => true],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search institution, major, degree" />
        <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Education</button>
    </div>

    <x-ui.table
        :columns="$columns"
        :data="$educations"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No education data found."
        emptySubMessage="Add your education history from the button above."
    >
        @foreach ($educations as $education)
            @php
                $yearRange = $education->end_year
                    ? $education->start_year.' - '.$education->end_year
                    : $education->start_year.' - Present';
            @endphp
            <tr wire:key="education-{{ $education->id }}" class="transition-colors hover:bg-base-200/50">
                <td>
                    @if ($education->logo)
                        <img
                            src="{{ str_starts_with($education->logo, 'http') ? $education->logo : Storage::url($education->logo) }}"
                            alt="{{ $education->institution_name }}"
                            class="h-10 w-10 rounded-xl border border-base-content/10 object-cover"
                            loading="lazy"
                        >
                    @else
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-base-content/10 bg-base-100/70 text-info">
                            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
                        </span>
                    @endif
                </td>
                <td class="font-medium text-base-content">{{ $education->institution_name }}</td>
                <td>{{ $education->major }}</td>
                <td>{{ $education->degree }}</td>
                <td>{{ $yearRange }}</td>
                <td>
                    <span class="badge badge-soft {{ $education->is_active ? 'badge-success' : 'badge-ghost' }}">
                        {{ $education->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $education->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $education->id }})" wire:confirm="Delete this education entry?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $educations->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$educationId ? 'Edit Education' : 'Add Education'" maxWidth="max-w-2xl" bodyClass="max-h-[90vh] overflow-y-auto" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Institution Name" name="institutionName" wire:model.defer="institutionName" required />
                <x-ui.input-field label="Major" name="major" wire:model.defer="major" required />
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <x-ui.select-field label="Degree" name="degree" wire:model.defer="degree" required>
                    @foreach ($degreeOptions as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </x-ui.select-field>

                <x-ui.input-field label="Start Year" name="startYear" type="number" min="1900" wire:model.defer="startYear" required />
                <x-ui.input-field label="End Year (optional)" name="endYear" type="number" min="1900" wire:model.defer="endYear" />
            </div>

            <x-ui.textarea-field
                label="Description"
                name="description"
                wire:model.defer="description"
                :rows="4"
                placeholder="Brief summary of your focus, achievements, or activities"
            />

            <x-ui.file-field label="Institution Logo" name="educationLogo" wire:model="educationLogo" accept="image/*" />

            @if ($educationLogo)
                <img src="{{ $educationLogo->temporaryUrl() }}" alt="Logo preview" class="h-16 w-16 rounded-xl border border-base-content/10 object-cover">
            @elseif ($existingLogo)
                <img src="{{ str_starts_with($existingLogo, 'http') ? $existingLogo : Storage::url($existingLogo) }}" alt="Logo preview" class="h-16 w-16 rounded-xl border border-base-content/10 object-cover">
            @endif

            <label class="label cursor-pointer justify-start gap-2">
                <input wire:model.defer="isActive" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                <span class="label-text">Active (show on landing page)</span>
            </label>

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>
</section>
