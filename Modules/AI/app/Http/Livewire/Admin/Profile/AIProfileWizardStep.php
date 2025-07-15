<?php

namespace Modules\AI\App\Http\Livewire\Admin\Profile;

use Livewire\Component;
use Modules\AI\app\Models\AITenantProfile;
use Modules\AI\app\Models\AIProfileSector;
use Modules\AI\app\Models\AIProfileQuestion;

class AIProfileWizardStep extends Component
{
    public $step = 1;
    public $sectors = [];
    public $questions = [];
    public $formData = [];
    public $profile;
    public $currentSectorCode = null;
    public $showFounderQuestions = false;
    
    public function mount($step = 1)
    {
        $this->step = $step;
        $this->profile = AITenantProfile::currentOrCreate();
        $this->sectors = AIProfileSector::getCategorizedSectors();
        $this->loadQuestionsForStep();
        $this->loadProfileData();
    }
    
    private function loadQuestionsForStep()
    {
        switch ($this->step) {
            case 1:
                $this->questions = AIProfileQuestion::getByStep(1);
                break;
            case 2:
                $this->questions = AIProfileQuestion::getByStep(2);
                break;
            case 3:
                $this->questions = AIProfileQuestion::getByStep(3, $this->currentSectorCode);
                break;
            case 4:
                $this->questions = AIProfileQuestion::getByStep(4);
                break;
            case 5:
                $this->questions = AIProfileQuestion::getByStep(5);
                $additionalQuestions = AIProfileQuestion::getByStep(6);
                $this->questions = $this->questions->merge($additionalQuestions);
                break;
            default:
                $this->questions = collect([]);
        }
    }
    
    private function loadProfileData()
    {
        if ($this->profile && $this->profile->exists) {
            // Company info
            if ($this->profile->company_info && is_array($this->profile->company_info)) {
                foreach ($this->profile->company_info as $key => $value) {
                    $this->formData["company_info.{$key}"] = $value;
                    $this->formData[$key] = $value;
                }
            }
            
            // Sector details
            if ($this->profile->sector_details && is_array($this->profile->sector_details)) {
                if (isset($this->profile->sector_details['sector_selection'])) {
                    $this->currentSectorCode = $this->profile->sector_details['sector_selection'];
                    $this->formData['sector'] = $this->currentSectorCode;
                } elseif (isset($this->profile->sector_details['sector'])) {
                    $this->currentSectorCode = $this->profile->sector_details['sector'];
                    $this->formData['sector'] = $this->currentSectorCode;
                }
                
                foreach ($this->profile->sector_details as $key => $value) {
                    $this->formData["sector_details.{$key}"] = $value;
                    $this->formData[$key] = $value;
                }
            }
            
            // Success stories
            if ($this->profile->success_stories && is_array($this->profile->success_stories)) {
                foreach ($this->profile->success_stories as $key => $value) {
                    $this->formData["success_stories.{$key}"] = $value;
                    $this->formData[$key] = $value;
                }
            }
            
            // AI behavior rules
            if ($this->profile->ai_behavior_rules && is_array($this->profile->ai_behavior_rules)) {
                foreach ($this->profile->ai_behavior_rules as $key => $value) {
                    $this->formData["ai_behavior_rules.{$key}"] = $value;
                    $this->formData[$key] = $value;
                }
            }
            
            // Founder info
            if ($this->profile->founder_info && is_array($this->profile->founder_info)) {
                foreach ($this->profile->founder_info as $key => $value) {
                    $this->formData["founder_info.{$key}"] = $value;
                    $this->formData[$key] = $value;
                }
            }
        }
    }
    
    public function render()
    {
        return view('ai::admin.profile.wizard-step', [
            'step' => $this->step,
            'sectors' => $this->sectors,
            'questions' => $this->questions,
            'formData' => $this->formData,
            'currentSectorCode' => $this->currentSectorCode,
            'showFounderQuestions' => $this->showFounderQuestions
        ]);
    }
}