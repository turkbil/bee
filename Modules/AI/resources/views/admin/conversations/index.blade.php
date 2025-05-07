@include('ai::admin.helper')

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Konuşmalarım</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                                <i class="fas fa-comment me-2"></i> AI Asistana Git
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(count($conversations) > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Başlık</th>
                                            <th>Son Mesaj</th>
                                            <th>Oluşturulma</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($conversations as $conversation)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}">
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
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ route('admin.ai.conversations.show', $conversation->id) }}" class="btn btn-icon btn-ghost-secondary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.ai.conversations.delete', $conversation->id) }}" method="POST" onsubmit="return confirm('Bu konuşmayı silmek istediğinizden emin misiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-icon btn-ghost-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>