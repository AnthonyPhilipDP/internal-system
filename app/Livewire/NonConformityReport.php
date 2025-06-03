<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NcfReport;
use Livewire\Attributes\Layout;

class NonConformityReport extends Component
{

    #[Layout('components.layouts.vanilla')]

    public $selectedReport;

    public function mount($reportId)
    {
        $this->selectedReport = NcfReport::find($reportId);
    }

    public function render()
    {
        return view('livewire.non-conformity-report');
    }
}
