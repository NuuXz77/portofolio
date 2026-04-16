@php
    $isOwner = isset($ownedCommentLookup[(int) $comment->id]);
    $isLiked = isset($likedCommentLookup[(int) $comment->id]);
    $canReply = (int) $comment->depth < 2;
    $isEditing = (int) ($editingCommentId ?? 0) === (int) $comment->id;
    $replyComposerOpen = (bool) ($replyOpen[$comment->id] ?? false);
@endphp

<article class="journal-comment-card rounded-2xl border border-white/10 bg-base-100/55 p-4 shadow-lg backdrop-blur-lg" style="animation-delay: {{ min($level, 4) * 40 }}ms;">
    <div class="flex flex-wrap items-start justify-between gap-2">
        <div>
            <p class="text-sm font-semibold text-base-content">{{ $comment->guest_name }}</p>
            <p class="text-xs text-base-content/60">
                {{ optional($comment->created_at)->diffForHumans() }}
                @if ($comment->is_edited)
                    • edited
                @endif
            </p>
        </div>

        <span class="badge badge-outline rounded-full text-[10px] uppercase tracking-[0.12em] text-base-content/55">
            L{{ (int) $comment->depth }}
        </span>
    </div>

    @if ($isEditing)
        <div class="mt-3 space-y-2">
            <textarea
                wire:model.defer="editingBody"
                rows="3"
                class="textarea textarea-bordered w-full rounded-xl border-white/15 bg-base-100/70"
            ></textarea>

            @error('editingBody')
                <p class="text-xs text-error">{{ $message }}</p>
            @enderror

            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="saveEditedComment({{ $comment->id }})"
                    class="btn btn-info btn-sm rounded-xl"
                >
                    Save
                </button>
                <button
                    type="button"
                    wire:click="cancelEditingComment"
                    class="btn btn-outline btn-sm rounded-xl"
                >
                    Cancel
                </button>
            </div>
        </div>
    @else
        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-base-content/82">{!! nl2br(e($comment->body)) !!}</p>
    @endif

    <div class="mt-3 flex flex-wrap items-center gap-2">
        @if ($canReply)
            <button
                type="button"
                wire:click="toggleReplyComposer({{ $comment->id }})"
                class="btn btn-ghost btn-xs rounded-lg"
            >
                <i data-lucide="reply" class="h-3.5 w-3.5"></i>
                Reply
            </button>
        @endif

        <button
            type="button"
            wire:click="toggleCommentLike({{ $comment->id }})"
            class="journal-like-btn btn btn-ghost btn-xs rounded-lg {{ $isLiked ? 'is-active' : '' }}"
        >
            <i data-lucide="heart" class="h-3.5 w-3.5"></i>
            {{ number_format($comment->likes_count) }}
        </button>

        @if ($isOwner && ! $isEditing)
            <button
                type="button"
                wire:click="startEditingComment({{ $comment->id }})"
                class="btn btn-ghost btn-xs rounded-lg"
            >
                <i data-lucide="pencil" class="h-3.5 w-3.5"></i>
                Edit
            </button>

            <button
                type="button"
                wire:click="deleteComment({{ $comment->id }})"
                class="btn btn-ghost btn-xs rounded-lg text-error"
            >
                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                Delete
            </button>
        @endif
    </div>

    @if ($replyComposerOpen && $canReply)
        <div class="journal-reply-composer mt-3 rounded-xl border border-white/10 bg-base-100/45 p-3">
            <label class="form-control">
                <span class="label pb-1">
                    <span class="label-text text-[11px] text-base-content/65">Reply as {{ $guestName !== '' ? $guestName : 'guest' }}</span>
                </span>
                <textarea
                    wire:model.defer="replyDrafts.{{ $comment->id }}"
                    rows="3"
                    class="textarea textarea-bordered w-full rounded-xl border-white/15 bg-base-100/70"
                    placeholder="Write your reply..."
                ></textarea>
            </label>

            @error('replyDrafts.'.$comment->id)
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror

            <div class="mt-2 flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="postReply({{ $comment->id }})"
                    class="btn btn-info btn-sm rounded-xl"
                >
                    Send Reply
                </button>
                <button
                    type="button"
                    wire:click="toggleReplyComposer({{ $comment->id }})"
                    class="btn btn-outline btn-sm rounded-xl"
                >
                    Cancel
                </button>
            </div>
        </div>
    @endif

    @if ($comment->replies->isNotEmpty())
        <div class="mt-3 space-y-3 border-l border-white/10 pl-3 sm:pl-5">
            @foreach ($comment->replies as $reply)
                @include('public.journal.partials.comment-item', [
                    'comment' => $reply,
                    'level' => $level + 1,
                    'ownedCommentLookup' => $ownedCommentLookup,
                    'likedCommentLookup' => $likedCommentLookup,
                ])
            @endforeach
        </div>
    @endif
</article>
