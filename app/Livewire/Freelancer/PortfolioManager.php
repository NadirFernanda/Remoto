<?php

namespace App\Livewire\Freelancer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Storage;

class PortfolioManager extends Component
{
    use WithFileUploads;

    public string $tab = 'imagem';
    public string $title = '';
    public string $description = '';
    public ?string $external_url = null;
    public ?string $issuer = null;
    public ?int $issued_year = null;
    public bool $featured = false;
    public $file = null;
    public ?int $editingId = null;
    public bool $showForm = false;

    public static array $categories = [
        'imagem'         => 'Imagens',
        'documento'      => 'Documentos',
        'link'           => 'Links Externos',
        'certificacao'   => 'Certificações',
        'estudo_de_caso' => 'Estudos de Caso',
    ];

    public function openForm(string $category = 'imagem', ?int $id = null): void
    {
        $this->resetForm();
        $this->tab      = $category;
        $this->showForm = true;

        if ($id) {
            $item             = auth()->user()->portfolios()->findOrFail($id);
            $this->editingId  = $id;
            $this->tab        = $item->category ?? 'imagem';
            $this->title      = $item->title;
            $this->description = $item->description ?? '';
            $this->external_url = $item->external_url;
            $this->issuer     = $item->issuer;
            $this->issued_year = $item->issued_year;
            $this->featured   = (bool) $item->featured;
        }
    }

    public function save(): void
    {
        $rules = [
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'featured'    => 'boolean',
        ];

        if (in_array($this->tab, ['imagem', 'documento'])) {
            $maxKb = $this->tab === 'imagem' ? 8192 : 20480;
            $rules['file'] = ($this->editingId ? 'nullable' : 'required') . "|file|max:{$maxKb}";
        }

        if ($this->tab === 'link') {
            $rules['external_url'] = 'required|url|max:500';
        }

        if ($this->tab === 'certificacao') {
            $rules['issuer']      = 'required|string|max:150';
            $rules['issued_year'] = 'nullable|integer|min:1990|max:' . date('Y');
        }

        $this->validate($rules);

        $data = [
            'user_id'      => auth()->id(),
            'title'        => $this->title,
            'description'  => $this->description ?: null,
            'category'     => $this->tab,
            'external_url' => $this->external_url,
            'issuer'       => $this->issuer,
            'issued_year'  => $this->issued_year,
            'featured'     => $this->featured,
            'is_public'    => true,
        ];

        if ($this->file) {
            $subdir    = $this->tab === 'imagem' ? 'images' : 'documents';
            $path      = $this->file->store("portfolio/{$subdir}", 'public');
            $data['media_path'] = $path;
            $data['media_type'] = $this->tab === 'imagem' ? 'image' : 'document';
        }

        if ($this->editingId) {
            $item = auth()->user()->portfolios()->findOrFail($this->editingId);
            // Remove old file if replaced
            if ($this->file && $item->media_path) {
                Storage::disk('public')->delete($item->media_path);
            }
            $item->update($data);
            session()->flash('success', 'Item atualizado.');
        } else {
            auth()->user()->portfolios()->create($data);
            session()->flash('success', 'Item adicionado ao portfólio.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $item = auth()->user()->portfolios()->findOrFail($id);
        if ($item->media_path) {
            Storage::disk('public')->delete($item->media_path);
        }
        $item->delete();
        session()->flash('success', 'Item removido.');
    }

    public function toggleFeatured(int $id): void
    {
        $item = auth()->user()->portfolios()->findOrFail($id);
        $item->update(['featured' => !$item->featured]);
    }

    public function resetForm(): void
    {
        $this->editingId    = null;
        $this->title        = '';
        $this->description  = '';
        $this->external_url = null;
        $this->issuer       = null;
        $this->issued_year  = null;
        $this->featured     = false;
        $this->file         = null;
        $this->showForm     = false;
        $this->resetValidation();
    }

    public function render()
    {
        $items = auth()->user()->portfolios()
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('category');

        return view('livewire.freelancer.portfolio-manager', [
            'items'      => $items,
            'categories' => self::$categories,
        ])->layout('layouts.dashboard', ['dashboardTitle' => 'Portfólio']);
    }
}
