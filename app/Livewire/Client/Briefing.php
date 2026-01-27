<?php

namespace App\Livewire\Client;

use Livewire\Component;


use App\Models\Service;

class Briefing extends Component
{
    public string $business_type = '';
    public string $target_audience = '';
    public string $style = '';
    public string $colors = '';
    public string $usage = '';
    public int $step = 1;
    public $edit = null;

    public function nextStep()
    {
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function submitBriefing()
    {
        $this->validate([
            'business_type' => 'required|max:100',
            'target_audience' => 'required|max:100',
            'style' => 'required|max:100',
            'colors' => 'required|max:100',
            'usage' => 'required|max:100',
        ]);
        $briefingData = [
            'business_type' => $this->business_type,
            'target_audience' => $this->target_audience,
            'style' => $this->style,
            'colors' => $this->colors,
            'usage' => $this->usage,
        ];
        // Se estiver editando, salva direto no serviço
        if ($this->edit) {
            $service = Service::find($this->edit);
            if ($service && $service->cliente_id === auth()->id()) {
                $service->briefing = json_encode($briefingData);
                $service->save();
            }
        }
        // Salvar briefing na sessão e redirecionar para definição de valor
        session(['briefing' => $briefingData]);
        return redirect()->route('client.value');
    }

    public function mount()
    {
        $editId = request()->query('edit');
        if ($editId) {
            $service = Service::find($editId);
            if ($service && $service->cliente_id === auth()->id()) {
                $briefing = json_decode($service->briefing, true);
                $this->business_type = $briefing['business_type'] ?? '';
                $this->target_audience = $briefing['target_audience'] ?? '';
                $this->style = $briefing['style'] ?? '';
                $this->colors = $briefing['colors'] ?? '';
                $this->usage = $briefing['usage'] ?? '';
                $this->edit = $editId;
            }
        }
    }

    public function render()
    {
        return view('livewire.client.briefing');
    }
}
