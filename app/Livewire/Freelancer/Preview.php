<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use App\Models\User;

class Preview extends Component
{
    public $show = false;
    public $userId;
    public $userModel;

    protected $listeners = ['openPreview'];

    public function openPreview($id)
    {
        $this->userId = $id;
        $this->userModel = User::with(['freelancerProfile', 'portfolios'])->find($id);
        $this->show = true;
    }

    public function openProposal($id)
    {
        $this->dispatch('openProposal', $id);
    }

    public function close()
    {
        $this->reset(['show', 'userId', 'userModel']);
    }

    public function render()
    {
        return view('livewire.freelancer.preview');
    }
}
