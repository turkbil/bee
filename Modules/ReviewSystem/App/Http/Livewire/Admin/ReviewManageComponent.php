<?php

declare(strict_types=1);

namespace Modules\ReviewSystem\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class ReviewManageComponent extends Component
{
    public ?int $reviewId = null;
    public ?int $ratingId = null;

    // Form fields
    public string $modelType = '';
    public string $modelId = '';
    public ?int $userId = null;
    public string $authorName = '';
    public string $reviewBody = '';
    public ?int $ratingValue = null;
    public bool $isApproved = true;

    // Validation errors
    public array $validationErrors = [];

    // Available models (buraya eklenebilir)
    public array $availableModels = [
        'Modules\Shop\App\Models\ShopProduct' => 'Shop Ürünü',
        'Modules\Page\App\Models\Page' => 'Sayfa',
        'Modules\Blog\App\Models\BlogPost' => 'Blog Yazısı',
    ];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->reviewId = $id;
            $this->loadReview();
        }
    }

    protected function loadReview(): void
    {
        $review = Review::find($this->reviewId);
        if (!$review) {
            return;
        }

        $this->modelType = $review->reviewable_type;
        $this->modelId = (string) $review->reviewable_id;
        $this->userId = $review->user_id;
        $this->authorName = $review->author_name ?? '';
        $this->reviewBody = $review->review_body;
        $this->ratingValue = $review->rating_value;
        $this->isApproved = $review->is_approved;
    }

    public function save(): void
    {
        $this->validationErrors = [];

        // Validation
        if (empty($this->modelType)) {
            $this->validationErrors['modelType'] = 'Model tipi seçilmeli';
        }

        if (empty($this->modelId)) {
            $this->validationErrors['modelId'] = 'Model ID girilmeli';
        }

        if (!$this->userId && empty($this->authorName)) {
            $this->validationErrors['author'] = 'Kullanıcı veya yazar adı girilmeli';
        }

        if (empty($this->reviewBody)) {
            $this->validationErrors['reviewBody'] = 'Yorum metni girilmeli';
        }

        if ($this->ratingValue && ($this->ratingValue < 1 || $this->ratingValue > 5)) {
            $this->validationErrors['ratingValue'] = 'Puan 1-5 arası olmalı';
        }

        if (!empty($this->validationErrors)) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Lütfen tüm zorunlu alanları doldurun'
            ]);
            return;
        }

        // Model varlık kontrolü
        if (!class_exists($this->modelType)) {
            $this->validationErrors['modelType'] = 'Model sınıfı bulunamadı';
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Model sınıfı bulunamadı'
            ]);
            return;
        }

        $model = $this->modelType::find((int) $this->modelId);
        if (!$model) {
            $this->validationErrors['modelId'] = 'Model bulunamadı';
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Belirtilen ID ile model bulunamadı'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Review kaydet/güncelle
            $reviewData = [
                'reviewable_type' => $this->modelType,
                'reviewable_id' => (int) $this->modelId,
                'user_id' => $this->userId,
                'author_name' => $this->authorName ?: ($this->userId ? User::find($this->userId)?->name : 'Anonim'),
                'review_body' => $this->reviewBody,
                'rating_value' => $this->ratingValue,
                'is_approved' => $this->isApproved,
            ];

            if ($this->isApproved) {
                $reviewData['approved_at'] = now();
                $reviewData['approved_by'] = auth()->id();
            }

            if ($this->reviewId) {
                // Update
                Review::where('id', $this->reviewId)->update($reviewData);
                $message = 'Yorum güncellendi';
            } else {
                // Create
                Review::create($reviewData);
                $message = 'Yorum eklendi';
            }

            // Rating kaydet (varsa ve userId varsa)
            if ($this->ratingValue && $this->userId) {
                Rating::updateOrCreate(
                    [
                        'user_id' => $this->userId,
                        'ratable_type' => $this->modelType,
                        'ratable_id' => (int) $this->modelId,
                    ],
                    ['rating_value' => $this->ratingValue]
                );
            }

            DB::commit();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => $message
            ]);

            // Formu temizle
            if (!$this->reviewId) {
                $this->reset(['modelType', 'modelId', 'userId', 'authorName', 'reviewBody', 'ratingValue']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'label' => "{$user->name} ({$user->email})"
            ]);

        return view('reviewsystem::admin.livewire.review-manage-component', [
            'users' => $users,
        ]);
    }
}
