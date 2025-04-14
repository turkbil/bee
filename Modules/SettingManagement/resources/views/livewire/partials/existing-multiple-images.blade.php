@if (!empty($images))
<div class="mb-3">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Mevcut Resimler</h4>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach ($images as $imageIndex => $imagePath)
                <div class="col-md-4 col-lg-3">
                    <div class="card">
                        <div class="img-responsive img-responsive-1x1 card-img-top" style="background-image: url('{{ cdn($imagePath) }}')"></div>
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between">
                                <a href="{{ cdn($imagePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        wire:click="removeMultipleImage({{ $settingId }}, {{ $imageIndex }})"
                                        wire:confirm="Bu resmi silmek istediğinize emin misiniz?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif