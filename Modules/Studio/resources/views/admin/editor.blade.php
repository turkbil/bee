@extends('studio::layouts.editor')

@section('content')
    <livewire:studio::editor-component :module="$module" :id="$id" />
@endsection