<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProgressCircle extends Component
{
    public $percentage;
    public $totalQuestions;
    public $answeredQuestions;
    public $size;
    public $strokeWidth;
    public $radius;
    public $circumference;
    public $strokeDashoffset;

    /**
     * Create a new component instance.
     */
    public function __construct($totalQuestions = 0, $answeredQuestions = 0, $size = 'large', $strokeWidth = 6)
    {
        $this->totalQuestions = $totalQuestions;
        $this->answeredQuestions = $answeredQuestions;
        $this->size = $size;
        $this->strokeWidth = $strokeWidth;
        
        // Calculate percentage with max 100% guarantee
        $this->percentage = $totalQuestions > 0 ? min(100, round(($answeredQuestions / $totalQuestions) * 100)) : 0;
        
        // Calculate SVG dimensions based on size
        $this->radius = $size === 'large' ? 45 : ($size === 'medium' ? 35 : 25);
        
        // Calculate circumference and stroke-dashoffset
        $this->circumference = 2 * pi() * $this->radius;
        $this->strokeDashoffset = $this->circumference - ($this->percentage / 100) * $this->circumference;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.progress-circle');
    }
}