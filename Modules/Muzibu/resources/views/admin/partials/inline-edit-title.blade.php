<td wire:key="title-{{ $muzibu->muzibu_id }}" class="position-relative">
    @if ($editingTitleId === $muzibu->muzibu_id)
        <div class="d-flex align-items-center gap-3" x-data @click.outside="$wire.updateTitleInline()">
            <div class="flexible-input-wrapper">
                <input type="text"
                       wire:model.defer="newTitle"
                       class="form-control form-control-sm flexible-input"
                       placeholder="{{ __('muzibu::admin.enter_new_title') }}"
                       wire:keydown.enter="updateTitleInline"
                       wire:keydown.escape="$set('editingTitleId', null)"
                       x-init="$nextTick(() => {
                           $el.focus();
                           $el.style.width = '20px';
                           $el.style.width = ($el.scrollWidth + 2) + 'px';
                       })"
                       x-on:input="
                           $el.style.width = '20px';
                           $el.style.width = ($el.scrollWidth + 2) + 'px'
                       "
                       style="min-width: 60px; max-width: 100%;">
            </div>
            <button class="btn px-2 py-1 btn-outline-success" wire:click="updateTitleInline">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn px-2 py-1 btn-outline-danger" wire:click="$set('editingTitleId', null)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @else
        <div class="d-flex align-items-center">
            <span class="editable-title pr-4">{{ $muzibu->getTranslated('title') }}</span>
            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                wire:click="startEditingTitle({{ $muzibu->muzibu_id }}, '{{ addslashes($muzibu->getTranslated('title')) }}')">
                <i class="fas fa-pen"></i>
            </button>
        </div>
    @endif
</td>
