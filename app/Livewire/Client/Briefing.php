<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use App\Modules\Marketplace\Services\BriefingTextGenerator;
use App\Modules\Marketplace\Services\BriefingTemplateService;

class Briefing extends Component
{
    // Step 1
    public string $business_type1 = '';
    public string $business_type1_outro = '';

    // Step 2
    public string $title1 = '';
    public string $necessity1 = '';

    // Step 3
    public string $generated_description = '';

    public int $step = 1;
    public ?int $edit = null;

    // Reactive template data (populated when service type changes)
    public array $currentTemplate = [];

    public function updatedBusinessType1(string $value): void
    {
        $tpl = BriefingTemplateService::get($value) ?? BriefingTemplateService::generic();
        $this->currentTemplate = $tpl;
    }

    public function goToStep2(): void
    {
        if (empty($this->business_type1)) {
            $this->addError('business_type1', 'Selecione o tipo de serviço.');
            return;
        }
        $tpl = BriefingTemplateService::get($this->business_type1) ?? BriefingTemplateService::generic();
        $this->currentTemplate = $tpl;
        $this->step = 2;
    }

    public function goToStep3(): void
    {
        $this->validate([
            'title1'    => 'required|string|max:100',
            'necessity1' => 'required|string|min:20|max:2000',
        ], [], [
            'title1'    => 'título do pedido',
            'necessity1' => 'descrição detalhada',
        ]);
        $this->generateDescription();
        $this->step = 3;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function generateDescription(): void
    {
        $serviceType = $this->business_type1 === 'Outro'
            ? $this->business_type1_outro
            : $this->business_type1;

        $briefingData = [
            'title'         => $this->title1,
            'business_type' => $serviceType,
            'necessity'     => $this->necessity1,
        ];
        $this->generated_description = BriefingTextGenerator::generate($briefingData);
    }

    public function submitBriefing()
    {
        $this->validate([
            'title1'               => 'required|max:100',
            'generated_description' => 'required|min:10',
        ], [], [
            'title1'               => 'título do pedido',
            'generated_description' => 'descrição gerada',
        ]);

        $serviceType = $this->business_type1 === 'Outro'
            ? $this->business_type1_outro
            : $this->business_type1;

        $serviceId = null;

        if ($this->edit) {
            $service = Service::find($this->edit);
            if ($service && $service->cliente_id === auth()->id()) {
                $service->update([
                    'titulo'       => $this->title1,
                    'briefing'     => $this->generated_description,
                    'service_type' => $serviceType,
                ]);
                $serviceId = $service->id;
            }
        } else {
            $service = Service::create([
                'cliente_id'   => auth()->id(),
                'titulo'       => $this->title1,
                'briefing'     => $this->generated_description,
                'service_type' => $serviceType,
                'taxa'         => 10.00,
                'status'       => 'published',
            ]);
            $serviceId = $service->id;
        }

        $order = session('client_order', []);
        $order['briefing_text'] = $this->generated_description;
        $order['title']         = $this->title1;
        $order['service_id']    = $serviceId;
        session(['client_order' => $order]);

        session()->flash('success', $this->edit
            ? 'Pedido atualizado com sucesso!'
            : 'Pedido criado com sucesso! Defina o orçamento para publicar.'
        );

        return redirect()->route('client.value', ['service' => $serviceId]);
    }

    public function mount(): void
    {
        $editId = request()->query('edit');
        if ($editId) {
            $service = Service::find($editId);
            if ($service && $service->cliente_id === auth()->id()) {
                $this->title1            = $service->titulo ?? '';
                $this->business_type1    = $service->service_type ?? '';
                $this->necessity1        = $service->briefing ?? '';
                $this->generated_description = $service->briefing ?? '';
                $this->edit              = (int) $editId;
                $this->step              = 2;
                $tpl = BriefingTemplateService::get($this->business_type1)
                    ?? BriefingTemplateService::generic();
                $this->currentTemplate = $tpl;
            }
        }
    }

    public function render()
    {
        $templates     = array_keys(BriefingTemplateService::templates());
        $allCategories = array_merge($templates, ['Outro']);

        return view('livewire.client.briefing', compact('allCategories'))
            ->layout('layouts.dashboard', ['dashboardTitle' => $this->edit ? 'Editar Pedido' : 'Novo Pedido']);
    }
}
