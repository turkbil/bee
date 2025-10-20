<?php

namespace Modules\MediaManagement\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\MediaManagement\App\Http\Livewire\Admin\MediaLibraryManager;
use Modules\MediaManagement\App\Models\MediaLibraryItem;
use Modules\MediaManagement\App\Services\MediaService;
use Tests\TestCase;

class MediaLibraryManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config(['filesystems.default' => 'public']);

        // Disable queued conversions for tests
        config(['mediamanagement.conversions.medium.queued' => false]);
        config(['mediamanagement.conversions.large.queued' => false]);
        config(['mediamanagement.conversions.responsive.queued' => false]);

        Schema::dropIfExists('media_library_items');
        Schema::dropIfExists('media');

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_type', 'model_id']);
            $table->string('collection_name')->index();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable()->index();
            $table->string('disk')->index();
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('media_library_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable()->index();
            $table->unsignedBigInteger('media_id')->nullable()->unique();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function test_media_library_lists_uploaded_items(): void
    {
        $media = $this->createLibraryMedia('Sample Image', UploadedFile::fake()->image('sample.jpg', 600, 400));

        Livewire::test(MediaLibraryManager::class)
            ->assertSee('Sample Image')
            ->assertSee($media->file_name);
    }

    public function test_media_library_filters_by_type(): void
    {
        $imageMedia = $this->createLibraryMedia('Gallery Cover', UploadedFile::fake()->image('cover.jpg', 800, 600));
        $documentMedia = $this->createLibraryMedia('Price List', UploadedFile::fake()->createWithContent('price.pdf', '%PDF-1.7 sample content'));

        Livewire::test(MediaLibraryManager::class)
            ->set('typeFilter', 'image')
            ->assertSee('Gallery Cover')
            ->assertDontSee('Price List');

        Livewire::test(MediaLibraryManager::class)
            ->set('typeFilter', 'document')
            ->assertSee('Price List')
            ->assertDontSee('Gallery Cover');
    }

    protected function createLibraryMedia(string $name, UploadedFile $file)
    {
        /** @var MediaService $mediaService */
        $mediaService = app(MediaService::class);

        $item = MediaLibraryItem::create([
            'name' => $name,
            'type' => $mediaService->getMediaTypeFromMime($file->getMimeType()),
        ]);

        $media = $mediaService->uploadMedia($item, $file, 'library');

        $item->update([
            'media_id' => $media->id,
            'type' => $mediaService->getMediaTypeFromMime($media->mime_type ?? ''),
        ]);

        return $media;
    }
    protected function tearDown(): void
    {
        Schema::dropIfExists('media_library_items');
        Schema::dropIfExists('media');

        parent::tearDown();
    }

}
