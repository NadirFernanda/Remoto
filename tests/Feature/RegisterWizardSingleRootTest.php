<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterWizardSingleRootTest extends TestCase
{
    use RefreshDatabase;

    public function test_wire_id_nao_fica_preso_numa_tag_style(): void
    {
        $html = $this->get('/register')->getContent();

        $this->assertDoesNotMatchRegularExpression('/<style[^>]*\bwire:id=/', $html, 'wire:id não pode ficar preso à tag <style> — isso tira o formulário de dentro dos limites do componente Livewire.');
        $this->assertMatchesRegularExpression('/<div[^>]*wire:id="[^"]+"[^>]*wire:name="auth\.register-wizard"/', $html);
    }
}
