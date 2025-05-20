@extends('admin.layout')

@section('content')
@include('ai::admin.helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Sol Kısım -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <form method="GET" action="{{ route('admin.ai.conversations.index') }}">
                        <input type="text" name="search" class="form-control" placeholder="Aramak için yazmaya başlayın..." 
                            value="{{ request('search') }}">
                    </form>
                </div>
            </div>
            <!-- Sağ Kısım -->
            <div class="col-auto">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                        <i class="fas fa-comment me-2"></i> AI Asistana Git
                    </a>
                </div>
            </div>
        </div>

        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Son Mesaj</th>
                        <th>Oluşturulma</th>
                        <th class="text-center" style="width: 120px">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($conversations as $conversation)
                        <tr class="hover-trigger">
                            <td>
                                <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}" class="text-reset">
                                    {{ Str::limit($conversation->title, 50) }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $lastMessage = \Modules\AI\App\Models\Message::where('conversation_id', $conversation->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                @endphp
                                @if($lastMessage)
                                    <span class="text-muted">
                                        {{ Str::limit($lastMessage->content, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $conversation->created_at->format('d.m.Y H:i') }}</td>
                            <td class="text-center align-middle">
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Görüntüle">
                                                <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col lh-1">
                                            <div class="dropdown mt-1">
                                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <form action="{{ route('admin.ai.conversations.delete', $conversation->id) }}" method="POST" 
                                                        onsubmit="return confirm('Bu konuşmayı silmek istediğinizden emin misiniz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item link-danger">
                                                            <i class="fas fa-trash me-2"></i> Sil
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-comments fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">Henüz konuşma yok</p>
                                    <p class="empty-subtitle text-muted">
                                        AI asistanı kullanarak yeni bir konuşma başlatabilirsiniz.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                                            <i class="fas fa-comment me-2"></i> AI Asistana Git
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{ $conversations->links() }}
</div>
@endsection