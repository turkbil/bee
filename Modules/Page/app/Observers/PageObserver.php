<?php

namespace Modules\Page\App\Observers;

use Modules\Page\App\Models\Page;
use Illuminate\Database\Eloquent\Model;

class PageObserver
{
    /**
     * Silme işlemini engelle
     */
    public function deleting(Page $page)
    {
        if ($page->is_homepage) {
            // Silme işlemini engelle
            throw new \Exception('Ana sayfa silinemez!');
        }
    }
}
