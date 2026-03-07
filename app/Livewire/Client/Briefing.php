<?php

namespace App\Livewire\Client;

use Livewire\Component;
use App\Models\Service;
use App\Services\BriefingTextGenerator;

class Briefing extends Component
{
    public $title1 = null;
    public $business_type1 = null;
    public $business_type1_outro = null;
    public $necessity1 = null;
    public $target_audience1 = null;
    public $style1 = null;
    public $colors1 = null;
    public $usage = null;
    public int $step = 1;
    public $edit = null;
    public $generated_description = '';
    public function generateDescription()
    {
        $briefingData = [
            'title' => $this->title1,
            'business_type' => $this->business_type1 === 'Outro' ? $this->business_type1_outro : $this->business_type1,
            'necessity' => $this->necessity1,
        ];
        $this->generated_description = BriefingTextGenerator::generate($briefingData);
    }

    public function nextStep()
    {
        // Garante que cada campo mantém seu valor individual ao avançar
        $this->title1 = (string) $this->title1;
        $this->business_type1 = (string) $this->business_type1;
        $this->business_type1_outro = (string) $this->business_type1_outro;
        $this->necessity1 = (string) $this->necessity1;
        $this->step++;
    }

    public function prevStep()
    {
        $this->step--;
    }

    public function submitBriefing()
    {
        $rules = [
            'title1'    => 'required|max:100',
            'business_type1' => 'nullable|max:100',
            'necessity1' => 'required|max:100',
        ];
        if ($this->business_type1 === 'Outro') {
            $rules['business_type1_outro'] = 'required|max:100';
        }
        $this->validate($rules);
        $briefingData = [
            'title' => $this->title1,
            'business_type' => $this->business_type1,
            'necessity' => $this->necessity1,
            'target_audience' => $this->target_audience1,
            'style' => $this->style1,
            'colors' => $this->colors1,
            'usage' => $this->usage,
        ];
        // Gerar texto profissional do briefing
        $briefingText = BriefingTextGenerator::generate($briefingData);
        // Se estiver editando, salva direto no serviço
        $serviceId = null;
        if ($this->edit) {
            $service = Service::find($this->edit);
            if ($service && $service->cliente_id === auth()->id()) {
                $service->titulo = $this->title1;
                $service->briefing = $briefingText;
                $service->save();
                $serviceId = $service->id;
            }
        } else {
            // Novo pedido: cria Service imediatamente com valor a ser definido depois
            $service = Service::create([
                'cliente_id'    => auth()->id(),
                'titulo'        => $this->title1,
                'briefing'      => $briefingText,
                'taxa'          => 10.00,
                'status'        => 'published',
            ]);
            $serviceId = $service->id;
        }

        // Salvar briefing e título na sessão para uso posterior (valor/pagamento)
        $order = session('client_order', []);
        $order['briefing_raw']  = $briefingData;
        $order['briefing_text'] = $briefingText;
        $order['title']         = $this->title1;
        $order['service_id']    = $serviceId;
        session([
            'client_order'   => $order,
            'briefing'       => $briefingData,
            'briefing_title' => $this->title1,
        ]);

        if ($this->edit) {
            session()->flash('success', 'Pedido atualizado com sucesso!');
        } else {
            session()->flash('success', 'Pedido criado com sucesso! Defina o orçamento para publicar.');
        }
        return redirect()->route('client.value', ['service' => $serviceId]);
    }

    public function mount()
    {
        $editId = request()->query('edit');
        if ($editId) {
            $service = Service::find($editId);
            if ($service && $service->cliente_id === auth()->id()) {
                $briefing = json_decode($service->briefing, true);
                $this->title1 = $service->titulo ?? '';
                $this->business_type1 = $briefing['business_type'] ?? '';
                $this->necessity1 = $briefing['necessity'] ?? '';
                $this->target_audience1 = $briefing['target_audience'] ?? '';
                $this->style1 = $briefing['style'] ?? '';
                $this->colors1 = $briefing['colors'] ?? '';
                $this->usage = $briefing['usage'] ?? '';
                $this->edit = $editId;
            }
        } else {
            // Limpa todos os campos ao iniciar novo briefing
            $this->title1 = '';
            $this->business_type1 = '';
            $this->necessity1 = '';
            $this->target_audience1 = '';
            $this->style1 = '';
            $this->colors1 = '';
            $this->usage = '';
        }
    }

    public function render()
    {
        return view('livewire.client.briefing');
    }
}
