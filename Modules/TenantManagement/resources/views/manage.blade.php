@extends('admin.layout')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Ad</label>
                <input type="text" id="name" name="data[name]" class="form-control" value="{{ $tenant->name ?? '' }}" required>
            </div>
            <button type="submit" class="btn btn-success">Kaydet</button>
        </form>
    </div>
</div>
@endsection
