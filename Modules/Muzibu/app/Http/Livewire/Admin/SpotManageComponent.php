<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\CorporateSpot;
use Modules\Muzibu\App\Models\MuzibuCorporateAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotManageComponent extends Component
{
    use WithFileUploads;

    public $spotId;

    public $inputs = [
        'corporate_account_id' => null,
        'title' => '',
        'is_enabled' => true,
        'is_archived' => false,
        'starts_at_date' => null,
        'starts_at_time' => null,
        'ends_at_date' => null,
        'ends_at_time' => null,
        'position' => 0,
        'duration' => 0,
    ];

    public $audioFile;
    public $existingAudioUrl;
    public $existingAudioName;

    // Yeni yüklenen dosya için geçici preview
    public $tempAudioUrl;
    public $tempAudioName;

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function mount($id = null): void
    {
        view()->share('pretitle', __('muzibu::admin.spot_management'));
        view()->share('title', $id ? __('admin.edit') : __('admin.create'));

        if ($id) {
            $this->spotId = $id;
            $this->loadSpotData($id);
        }
    }

    protected function loadSpotData(int|string $id): void
    {
        $spot = CorporateSpot::with('media')->findOrFail($id);

        $this->inputs = [
            'corporate_account_id' => $spot->corporate_account_id,
            'title' => $spot->title,
            'is_enabled' => $spot->is_enabled,
            'is_archived' => $spot->is_archived,
            'starts_at_date' => $spot->starts_at ? $spot->starts_at->format('Y-m-d') : null,
            'starts_at_time' => $spot->starts_at ? $spot->starts_at->format('H:i') : null,
            'ends_at_date' => $spot->ends_at ? $spot->ends_at->format('Y-m-d') : null,
            'ends_at_time' => $spot->ends_at ? $spot->ends_at->format('H:i') : null,
            'position' => $spot->position,
            'duration' => $spot->duration ?? 0,
        ];

        // Load existing audio
        $audioMedia = $spot->getFirstMedia('audio');
        if ($audioMedia) {
            $this->existingAudioUrl = $audioMedia->getUrl();
            $this->existingAudioName = $audioMedia->file_name;
        }
    }

    protected function rules(): array
    {
        return [
            'inputs.corporate_account_id' => 'required|exists:muzibu_corporate_accounts,id',
            'inputs.title' => 'required|min:3|max:255',
            'inputs.is_enabled' => 'boolean',
            'inputs.is_archived' => 'boolean',
            'inputs.starts_at_date' => 'nullable|date',
            'inputs.starts_at_time' => 'nullable|date_format:H:i',
            'inputs.ends_at_date' => 'nullable|date',
            'inputs.ends_at_time' => 'nullable|date_format:H:i',
            'inputs.position' => 'nullable|integer|min:0',
            'audioFile' => 'nullable|file|max:30720|mimes:mp3,wav,flac,m4a,ogg,aac,wma',
        ];
    }

    protected $messages = [
        'inputs.corporate_account_id.required' => 'Kurumsal hesap seçimi zorunludur.',
        'inputs.corporate_account_id.exists' => 'Geçerli bir kurumsal hesap seçin.',
        'inputs.title.required' => 'Başlık zorunludur.',
        'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
        'inputs.title.max' => 'Başlık en fazla 255 karakter olabilir.',
        'audioFile.max' => 'Ses dosyası en fazla 30 MB olabilir.',
        'audioFile.mimes' => 'Geçerli ses formatları: mp3, wav, flac, m4a, ogg, aac, wma',
    ];

    #[Computed]
    public function corporateAccounts(): \Illuminate\Database\Eloquent\Collection
    {
        return MuzibuCorporateAccount::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('company_name')
            ->get();
    }

    #[Computed]
    public function currentSpot()
    {
        if (!$this->spotId) {
            return null;
        }
        return CorporateSpot::find($this->spotId);
    }

    /**
     * Audio dosya yükleme - otomatik duration ve title çıkarma
     * Livewire tarafından audioFile property değiştiğinde otomatik çağrılır
     */
    public function updatedAudioFile(): void
    {
        try {
            $this->validate([
                'audioFile' => 'file|mimes:mp3,wav,flac,m4a,ogg,aac,wma|max:30720',
            ]);

            // Geçici dosya yolu
            $tempPath = $this->audioFile->getRealPath();

            // Metadata çıkar (duration, title)
            $metadata = $this->extractAudioMetadata($tempPath);

            // Duration'u kaydet
            if (isset($metadata['duration'])) {
                $this->inputs['duration'] = $metadata['duration'];
            }

            // Title boşsa ID3'ten doldur
            if (empty($this->inputs['title']) && isset($metadata['title']) && !empty(trim($metadata['title']))) {
                $this->inputs['title'] = $metadata['title'];
                Log::info('📝 Spot: ID3 tag\'inden title otomatik dolduruldu', [
                    'title' => $metadata['title']
                ]);
            }

            // Geçici preview için URL ve isim
            $this->tempAudioUrl = $this->audioFile->temporaryUrl();
            $this->tempAudioName = $this->audioFile->getClientOriginalName();

            // Mevcut dosyayı kaldır (UI'da sadece yeni dosya gösterilsin)
            $this->existingAudioUrl = null;
            $this->existingAudioName = null;

            $durationFormatted = $this->inputs['duration'] > 0
                ? gmdate('i:s', $this->inputs['duration'])
                : '--:--';

            Log::info('🎙️ Spot: Ses dosyası yüklendi', [
                'filename' => $this->tempAudioName,
                'duration' => $this->inputs['duration'],
                'formatted' => $durationFormatted,
                'title' => $this->inputs['title'] ?? 'boş'
            ]);

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => "Ses dosyası yüklendi! Süre: {$durationFormatted}",
                'type' => 'success'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Spot audio upload hatası', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Dosya yüklenirken hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Audio dosyasından metadata çıkar (duration, title)
     */
    protected function extractAudioMetadata(string $filePath): array
    {
        $metadata = [];

        try {
            // getID3 kütüphanesi ile metadata çıkar
            if (class_exists('\getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);

                // Duration
                if (isset($fileInfo['playtime_seconds'])) {
                    $metadata['duration'] = (int) round($fileInfo['playtime_seconds']);
                }

                // Title (ID3v2 öncelikli, sonra ID3v1)
                if (isset($fileInfo['tags']['id3v2']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v2']['title'][0]);
                } elseif (isset($fileInfo['tags']['id3v1']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v1']['title'][0]);
                }
            }

            // getID3 yoksa veya duration bulunamadıysa FFprobe dene
            if (empty($metadata['duration']) && function_exists('shell_exec')) {
                $ffprobeCmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
                $duration = shell_exec($ffprobeCmd);

                if ($duration && is_numeric(trim($duration))) {
                    $metadata['duration'] = (int) round(floatval(trim($duration)));
                }
            }

            return $metadata;

        } catch (\Exception $e) {
            Log::warning('Spot: Metadata extraction failed', [
                'error' => $e->getMessage()
            ]);
            return $metadata;
        }
    }

    /**
     * Geçici yüklenen dosyayı kaldır
     */
    public function removeTempAudio(): void
    {
        $this->audioFile = null;
        $this->tempAudioUrl = null;
        $this->tempAudioName = null;
        $this->inputs['duration'] = 0;

        $this->dispatch('toast', [
            'title' => 'Bilgi',
            'message' => 'Yüklenen dosya kaldırıldı.',
            'type' => 'info',
        ]);
    }

    /**
     * Mevcut kayıtlı ses dosyasını kaldır
     */
    public function removeAudio(): void
    {
        if ($this->spotId) {
            $spot = CorporateSpot::find($this->spotId);
            if ($spot) {
                $spot->clearMediaCollection('audio');
                $spot->update(['duration' => 0]);
                $this->existingAudioUrl = null;
                $this->existingAudioName = null;
                $this->inputs['duration'] = 0;

                $this->dispatch('toast', [
                    'title' => __('admin.success'),
                    'message' => 'Ses dosyası kaldırıldı.',
                    'type' => 'success',
                ]);
            }
        }
    }

    public function save(bool $redirect = false): void
    {
        $this->validate();

        try {
            // Combine date and time for starts_at and ends_at
            $startsAt = null;
            $endsAt = null;

            if ($this->inputs['starts_at_date']) {
                $time = $this->inputs['starts_at_time'] ?? '00:00';
                $startsAt = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $this->inputs['starts_at_date'] . ' ' . $time
                );
            }

            if ($this->inputs['ends_at_date']) {
                $time = $this->inputs['ends_at_time'] ?? '23:59';
                $endsAt = \Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $this->inputs['ends_at_date'] . ' ' . $time
                );
            }

            $data = [
                'corporate_account_id' => $this->inputs['corporate_account_id'],
                'title' => $this->inputs['title'],
                'slug' => Str::slug($this->inputs['title']),
                'is_enabled' => $this->inputs['is_enabled'] ?? true,
                'is_archived' => $this->inputs['is_archived'] ?? false,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'position' => $this->inputs['position'] ?? 0,
                'duration' => $this->inputs['duration'] ?? 0,
            ];

            $isNew = !$this->spotId;

            if ($this->spotId) {
                $spot = CorporateSpot::findOrFail($this->spotId);
                $spot->update($data);
                log_activity($spot, 'güncellendi');
            } else {
                $spot = CorporateSpot::create($data);
                $this->spotId = $spot->id;
                log_activity($spot, 'oluşturuldu');
            }

            // Handle audio file upload
            if ($this->audioFile) {
                // Clear existing audio
                $spot->clearMediaCollection('audio');

                // Upload new audio
                $media = $spot->addMedia($this->audioFile->getRealPath())
                    ->usingName($spot->slug)
                    ->usingFileName($spot->slug . '.' . $this->audioFile->getClientOriginalExtension())
                    ->toMediaCollection('audio');

                // Duration zaten updatedAudioFile'da hesaplandı, DB'ye kaydet
                if ($this->inputs['duration'] > 0) {
                    $spot->update(['duration' => $this->inputs['duration']]);
                }

                $this->existingAudioUrl = $media->getUrl();
                $this->existingAudioName = $media->file_name;
                $this->audioFile = null;
                $this->tempAudioUrl = null;
                $this->tempAudioName = null;
            }

            $message = $isNew ? 'Spot oluşturuldu.' : 'Spot güncellendi.';

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $message,
                'type' => 'success',
            ]);

            if ($redirect) {
                session()->flash('toast', [
                    'title' => __('admin.success'),
                    'message' => $message,
                    'type' => 'success',
                ]);
                $this->redirectRoute('admin.muzibu.spot.index');
                return;
            }

            if ($isNew) {
                session()->flash('toast', [
                    'title' => __('admin.success'),
                    'message' => $message,
                    'type' => 'success',
                ]);
                $this->redirectRoute('admin.muzibu.spot.manage', ['id' => $spot->id]);
                return;
            }

        } catch (\Exception $e) {
            Log::error('Spot save error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    /**
     * Süreyi formatla (saniyeden mm:ss formatına)
     */
    public function getFormattedDuration(): string
    {
        $duration = $this->inputs['duration'] ?? 0;
        return $duration > 0 ? gmdate('i:s', $duration) : '--:--';
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('muzibu::admin.livewire.spot-manage-component', [
            'corporateAccounts' => $this->corporateAccounts,
        ]);
    }
}
