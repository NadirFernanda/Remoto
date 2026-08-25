<?php

namespace App\Livewire\Client;

use App\Models\PlatformSetting;
use Livewire\Component;

class ServiceValue extends Component
{
    public float $valor       = 5;   // o que o cliente escreve — significado depende de $modo
    public string $modo       = 'acrescentar'; // 'acrescentar' | 'descontar'
    public float $taxa        = 10.0; // percentagem (não fracção)
    public float $valorMinimo = 5;

    public function mount()
    {
        $this->valorMinimo = (float) PlatformSetting::get('project_min_value', 5);
        $this->valor       = $this->valorMinimo;

        // Se já houver dados de projecto na sessão, respeita o valor definido anteriormente
        $order = session('client_order', []);
        if (isset($order['payment']['valor_digitado'])) {
            $this->valor = (float) $order['payment']['valor_digitado'];
            $this->modo  = $order['payment']['modo'] ?? 'acrescentar';
        }
    }

    public function updatedValor($value): void
    {
        $this->valor = max(0, (float) $value);
    }

    public function updatedModo(): void
    {
        // Nada a recalcular aqui — a decomposição é sempre derivada de $valor + $modo
        // no computed getBreakdownProperty(), avaliado de novo em cada render.
    }

    /**
     * Decomposição do valor consoante o modo escolhido:
     *
     *  - "acrescentar": $valor é o valor do projecto (o que o freelancer usa
     *    como referência). A plataforma acrescenta 10% por cima — o cliente
     *    paga $valor + 10%.
     *  - "descontar": $valor é o TOTAL que o cliente quer mesmo pagar (nem
     *    mais, nem menos). Para que $base + 10% de $base reproduza esse
     *    total exactamente, $base = $valor / (1 + taxa) — não uma simples
     *    subtracção de 10%, que não fecha a conta (ver commit desta
     *    funcionalidade para o porquê).
     */
    public function getBreakdownProperty(): array
    {
        $rate = $this->taxa / 100;

        if ($this->modo === 'descontar') {
            $total       = round($this->valor, 2);
            $base        = $rate > 0 ? round($total / (1 + $rate), 2) : $total;
            $taxaCliente = round($total - $base, 2);
        } else {
            $base        = round($this->valor, 2);
            $taxaCliente = round($base * $rate, 2);
            $total       = round($base + $taxaCliente, 2);
        }

        $taxaFreelancer = round($base * $rate, 2);
        $liquido        = round($base - $taxaFreelancer, 2);

        return [
            'base'            => $base,
            'taxa_cliente'    => $taxaCliente,
            'total'           => $total,
            'taxa_freelancer' => $taxaFreelancer,
            'liquido'         => $liquido,
        ];
    }

    public function submitValue()
    {
        // Garante que o briefing foi preenchido antes de definir o valor
        $order = session('client_order', []);
        if (empty($order['briefing_raw']) && empty($order['briefing_text'])) {
            session()->flash('error', 'Preencha o briefing antes de definir o valor do serviço.');
            return redirect()->route('client.briefing');
        }

        $this->validate([
            'valor' => 'required|numeric|min:' . $this->valorMinimo,
        ], [
            'valor.min' => 'O valor deve ser no mínimo ' . number_format($this->valorMinimo, 0, ',', '.') . ' Kz.',
        ]);

        $bd = $this->breakdown;

        // Guarda sempre o valor BASE do projecto — é a partir dele que o
        // ecrã de pagamento (PaymentEscrow) calcula a sobretaxa do cliente
        // (sempre por acréscimo, ver FeeService::calculateServiceFee), por
        // isso o modo "descontar" já tem de entregar aqui o valor
        // back-calculado, não o total que o cliente escreveu.
        $order['payment'] = [
            'valor'          => $bd['base'],
            'valor_digitado' => $this->valor,
            'modo'           => $this->modo,
            'taxa'           => $this->taxa,
            'valor_liquido'  => $bd['liquido'],
        ];
        session([
            'client_order' => $order,
            // Mantém estrutura antiga para compatibilidade
            'pagamento' => $order['payment'],
        ]);
        return redirect()->route('client.payment', ['service' => session('client_order.service_id', 0), 'valor' => $bd['base']]);
    }

    public function render()
    {
        return view('livewire.client.service-value')
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Definir Valor']);
    }
}
