<?php

namespace Modules\WidgetManagement\app\Policies;

use App\Models\User;
use Modules\WidgetManagement\app\Models\Widget;
use Illuminate\Auth\Access\HandlesAuthorization;

class WidgetPolicy
{
    use HandlesAuthorization;

    /**
     * Sadece root kullanıcıları widget yapılandırmasını düzenleyebilir
     */
    public function update(User $user, Widget $widget): bool
    {
        return $user->hasRole('root');
    }
    
    /**
     * Widget içeriğini düzenleme izni (admin veya editör)
     */
    public function manageContent(User $user, Widget $widget): bool
    {
        return $user->hasModulePermission('widgetmanagement', 'update');
    }
}