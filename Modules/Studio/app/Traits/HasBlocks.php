<?php

namespace Modules\Studio\App\Traits;

use Illuminate\Support\Collection;

trait HasBlocks
{
    /**
     * Modelin ilişkili blokları
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * Modele blok ekle
     *
     * @param string $blockId
     * @param array $data
     * @return self
     */
    public function addBlock(string $blockId, array $data = []): self
    {
        $this->blocks[$blockId] = array_merge([
            'id' => $blockId,
            'position' => count($this->blocks) + 1
        ], $data);
        
        return $this;
    }
    
    /**
     * Modelden blok kaldır
     *
     * @param string $blockId
     * @return self
     */
    public function removeBlock(string $blockId): self
    {
        if (isset($this->blocks[$blockId])) {
            unset($this->blocks[$blockId]);
        }
        
        return $this;
    }
    
    /**
     * Modelin bloklarını al
     *
     * @return Collection
     */
    public function getBlocks(): Collection
    {
        return collect($this->blocks)->sortBy('position');
    }
    
    /**
     * Model bloklarını ayarla
     *
     * @param array $blocks
     * @return self
     */
    public function setBlocks(array $blocks): self
    {
        $this->blocks = $blocks;
        
        return $this;
    }
    
    /**
     * Modelin belirli bir bloğunu al
     *
     * @param string $blockId
     * @return array|null
     */
    public function getBlock(string $blockId): ?array
    {
        return $this->blocks[$blockId] ?? null;
    }
    
    /**
     * Modelin bloklarını güncelle
     *
     * @param array $blocks
     * @return self
     */
    public function updateBlocks(array $blocks): self
    {
        foreach ($blocks as $blockId => $data) {
            if (isset($this->blocks[$blockId])) {
                $this->blocks[$blockId] = array_merge($this->blocks[$blockId], $data);
            }
        }
        
        return $this;
    }
    
    /**
     * Blokları JSON olarak al
     *
     * @return string
     */
    public function getBlocksAsJson(): string
    {
        return json_encode($this->blocks);
    }
    
    /**
     * JSON'dan blokları ayarla
     *
     * @param string $json
     * @return self
     */
    public function setBlocksFromJson(string $json): self
    {
        $blocks = json_decode($json, true);
        
        if (is_array($blocks)) {
            $this->blocks = $blocks;
        }
        
        return $this;
    }
}