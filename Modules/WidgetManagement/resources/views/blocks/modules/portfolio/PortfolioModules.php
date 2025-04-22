<?php

namespace Modules\WidgetManagement\resources\views\blocks\modules\portfolio;

use Modules\Portfolio\app\Models\Project;
use Modules\Portfolio\app\Models\Category;
use Illuminate\Support\Str;

class PortfolioModules
{
    /**
     * Listelenecek projeleri getir
     */
    public function getProjectList($settings)
    {
        $limit = $settings['limit'] ?? 6;
        $orderDirection = $settings['order_direction'] ?? 'desc';
        $categoryId = $settings['category_id'] ?? null;
        
        // Veritabanından doğrudan çek, önbellek yok
        $query = Project::where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $projects = $query->orderBy('created_at', $orderDirection)
            ->limit($limit)
            ->get();
            
        $result = [
            'projects' => $projects->map(function($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'description' => Str::limit(strip_tags($project->description), 150),
                    'cover_image' => $project->cover_image,
                    'category' => $project->category ? [
                        'id' => $project->category->id,
                        'name' => $project->category->name
                    ] : null,
                    'created_at' => $project->created_at->format('d.m.Y H:i'),
                    'url' => '/portfolio/' . $project->slug,
                ];
            })->toArray(),
            'meta' => [
                'total' => Project::where('is_active', true)->count(),
                'count' => $projects->count()
            ]
        ];
        
        return $result;
    }
    
    /**
     * Belirli bir projeyi getir
     */
    public function getProjectDetail($settings)
    {
        $projectId = $settings['project_id'] ?? null;
        $projectSlug = $settings['project_slug'] ?? null;
        
        $project = null;
        
        if ($projectId) {
            $project = Project::find($projectId);
        } elseif ($projectSlug) {
            $project = Project::where('slug', $projectSlug)->first();
        } else {
            $project = Project::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$project) {
            return [
                'project' => null,
                'meta' => [
                    'found' => false
                ]
            ];
        }
        
        // İlişkili projeleri getir
        $relatedProjects = [];
        if ($settings['show_related'] ?? false) {
            $relatedProjects = Project::where('is_active', true)
                ->where('id', '!=', $project->id)
                ->when($project->category_id, function($query) use ($project) {
                    return $query->where('category_id', $project->category_id);
                })
                ->limit(3)
                ->get()
                ->map(function($relatedProject) {
                    return [
                        'id' => $relatedProject->id,
                        'title' => $relatedProject->title,
                        'slug' => $relatedProject->slug,
                        'cover_image' => $relatedProject->cover_image,
                        'url' => '/portfolio/' . $relatedProject->slug,
                    ];
                })
                ->toArray();
        }
        
        return [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'content' => $project->content,
                'cover_image' => $project->cover_image,
                'gallery' => $project->gallery,
                'category' => $project->category ? [
                    'id' => $project->category->id,
                    'name' => $project->category->name
                ] : null,
                'created_at' => $project->created_at->format('d.m.Y H:i'),
                'url' => '/portfolio/' . $project->slug,
            ],
            'related_projects' => $relatedProjects,
            'meta' => [
                'found' => true
            ]
        ];
    }
    
    /**
     * Tüm kategorileri getir
     */
    public function getCategories($settings)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'project_count' => $category->projects()->where('is_active', true)->count()
                ];
            })
            ->toArray();
            
        return [
            'categories' => $categories,
            'meta' => [
                'count' => count($categories)
            ]
        ];
    }
    
    /**
     * Veri al
     */
    public function getData($settings)
    {
        $moduleType = $settings['module_type'] ?? 'project_list';
        
        switch ($moduleType) {
            case 'project_list':
                return $this->getProjectList($settings);
            case 'project_detail':
                return $this->getProjectDetail($settings);
            case 'categories':
                return $this->getCategories($settings);
            default:
                return [];
        }
    }
}