<?php

namespace Modules\WidgetManagement\resources\views\blocks\modules\portfolio;

use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
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
        $query = Portfolio::where('is_active', true);

        if ($categoryId) {
            $query->where('portfolio_category_id', $categoryId);
        }

        $projects = $query->orderBy('created_at', $orderDirection)
            ->limit($limit)
            ->get();
            
        $result = [
            'projects' => $projects->map(function($project) {
                return [
                    'id' => $project->portfolio_id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'description' => Str::limit(strip_tags($project->body), 150),
                    'cover_image' => $project->image,
                    'category' => $project->category ? [
                        'id' => $project->category->portfolio_category_id,
                        'name' => $project->category->title
                    ] : null,
                    'created_at' => $project->created_at->format('d.m.Y H:i'),
                    'url' => '/portfolio/' . $project->slug,
                ];
            })->toArray(),
            'meta' => [
                'total' => Portfolio::where('is_active', true)->count(),
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
            $project = Portfolio::find($projectId);
        } elseif ($projectSlug) {
            $project = Portfolio::where('slug', $projectSlug)->first();
        } else {
            $project = Portfolio::where('is_active', true)
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
            $relatedProjects = Portfolio::where('is_active', true)
                ->where('portfolio_id', '!=', $project->portfolio_id)
                ->when($project->portfolio_category_id, function($query) use ($project) {
                    return $query->where('portfolio_category_id', $project->portfolio_category_id);
                })
                ->limit(3)
                ->get()
                ->map(function($relatedProject) {
                    return [
                        'id' => $relatedProject->portfolio_id,
                        'title' => $relatedProject->title,
                        'slug' => $relatedProject->slug,
                        'cover_image' => $relatedProject->image,
                        'url' => '/portfolio/' . $relatedProject->slug,
                    ];
                })
                ->toArray();
        }
        
        return [
            'project' => [
                'id' => $project->portfolio_id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => Str::limit(strip_tags($project->body), 150),
                'content' => $project->body,
                'cover_image' => $project->image,
                'gallery' => $project->getMedia('images') ?? [],
                'category' => $project->category ? [
                    'id' => $project->category->portfolio_category_id,
                    'name' => $project->category->title
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
        $categories = PortfolioCategory::where('is_active', true)
            ->orderBy('title')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->portfolio_category_id,
                    'name' => $category->title,
                    'slug' => $category->slug,
                    'project_count' => $category->portfolios()->where('is_active', true)->count()
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