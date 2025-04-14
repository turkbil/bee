<?php

namespace Modules\Studio\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class StudioAsset extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'studio_assets';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'filename',
        'path',
        'mime_type',
        'size',
        'extension',
        'disk',
        'module',
        'module_id'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'integer',
    ];
    
    /**
     * Get the asset URL
     *
     * @return string
     */
    public function getUrl(): string
    {
        // Check if file exists
        if (!$this->exists()) {
            return asset('images/missing.png');
        }
        
        return Storage::disk($this->disk)->url($this->path);
    }
    
    /**
     * Get the asset path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * Check if file exists
     *
     * @return bool
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }
    
    /**
     * Get file contents
     *
     * @return string|null
     */
    public function getContents(): ?string
    {
        if (!$this->exists()) {
            return null;
        }
        
        return Storage::disk($this->disk)->get($this->path);
    }
    
    /**
     * Delete the asset file
     *
     * @return bool
     */
    public function deleteFile(): bool
    {
        if (!$this->exists()) {
            return false;
        }
        
        return Storage::disk($this->disk)->delete($this->path);
    }
    
    /**
     * Create asset from file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $module
     * @param int $moduleId
     * @param string|null $name
     * @param string $disk
     * @return StudioAsset
     */
    public static function createFromFile($file, string $module, int $moduleId, ?string $name = null, string $disk = 'public'): self
    {
        // Tenant prefix for path
        $tenantPrefix = function_exists('tenant') ? 'tenant/' . tenant()->getTenantKey() . '/' : '';
        
        // Generate file path
        $path = $file->store($tenantPrefix . 'studio/' . $module . '/' . $moduleId, $disk);
        
        // Create asset record
        return self::create([
            'name' => $name ?? $file->getClientOriginalName(),
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'disk' => $disk,
            'module' => $module,
            'module_id' => $moduleId
        ]);
    }
}