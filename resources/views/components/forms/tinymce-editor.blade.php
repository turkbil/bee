@props([
    'id' => 'tinymce-' . uniqid(),
    'name' => null,
    'value' => '',
    'label' => null,
    'wireModel' => null,
    'placeholder' => '',
    'required' => false,
    'height' => 400,
    'minHeight' => 400
])

<div class="mb-3" wire:ignore>
  @if($label)
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
  @endif
  
  <textarea 
    id="{{ $id }}"
    name="{{ $name }}"
    @if($wireModel) wire:model.defer="{{ $wireModel }}" @endif
    class="form-control tinymce"
    placeholder="{{ $placeholder }}"
    data-height="{{ $height }}"
    data-min-height="{{ $minHeight }}"
    @if($required) required @endif
  >{!! $value !!}</textarea>
</div>