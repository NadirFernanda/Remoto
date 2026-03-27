<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ServiceCategory;
use App\Modules\Admin\Services\AuditLogger;
use Illuminate\Support\Str;

class Categories extends Component
{
    public string $search  = '';
    public bool   $showForm = false;
    public ?int   $editId  = null;

    // Form fields
    public string $name        = '';
    public string $icon        = '';
    public string $description = '';
    public bool   $active      = true;
    public int    $sort_order  = 0;

    public string $savedMsg = '';
    public string $errorMsg = '';

    protected array $rules = [
        'name'        => 'required|string|max:100',
        'icon'        => 'nullable|string|max:20',
        'description' => 'nullable|string|max:255',
        'active'      => 'boolean',
        'sort_order'  => 'integer|min:0',
    ];

    protected array $messages = [
        'name.required' => 'O nome da categoria é obrigatório.',
        'name.max'      => 'O nome não pode ter mais de 100 caracteres.',
    ];

    public function mount(): void
    {
        abort_if(auth()->user()?->role !== 'admin', 403);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editId   = null;
    }

    public function openEdit(int $id): void
    {
        $cat = ServiceCategory::findOrFail($id);
        $this->editId      = $id;
        $this->name        = $cat->name;
        $this->icon        = $cat->icon ?? '';
        $this->description = $cat->description ?? '';
        $this->active      = $cat->active;
        $this->sort_order  = $cat->sort_order;
        $this->showForm    = true;
        $this->savedMsg    = '';
        $this->errorMsg    = '';
    }

    public function save(): void
    {
        $this->savedMsg = '';
        $this->errorMsg = '';
        $this->validate();

        $slug = $this->editId
            ? ServiceCategory::find($this->editId)?->slug ?? ServiceCategory::generateSlug($this->name)
            : ServiceCategory::generateSlug($this->name);

        $data = [
            'name'        => $this->name,
            'slug'        => $slug,
            'icon'        => $this->icon ?: null,
            'description' => $this->description ?: null,
            'active'      => $this->active,
            'sort_order'  => $this->sort_order,
        ];

        if ($this->editId) {
            ServiceCategory::findOrFail($this->editId)->update($data);
            AuditLogger::log('category_updated', "Categoria #{$this->editId} actualizada: {$this->name}");
            $this->savedMsg = "Categoria «{$this->name}» actualizada.";
        } else {
            ServiceCategory::create($data);
            AuditLogger::log('category_created', "Nova categoria criada: {$this->name}");
            $this->savedMsg = "Categoria «{$this->name}» criada com sucesso.";
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function toggleActive(int $id): void
    {
        $cat = ServiceCategory::findOrFail($id);
        $cat->update(['active' => !$cat->active]);
        AuditLogger::log('category_toggled', "Categoria #{$id} {$cat->name}: active=" . ($cat->active ? 'true' : 'false'));
    }

    public function delete(int $id): void
    {
        $cat = ServiceCategory::findOrFail($id);
        AuditLogger::log('category_deleted', "Categoria #{$id} eliminada: {$cat->name}");
        $cat->delete();
        $this->savedMsg = "Categoria «{$cat->name}» removida.";
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->name        = '';
        $this->icon        = '';
        $this->description = '';
        $this->active      = true;
        $this->sort_order  = 0;
        $this->editId      = null;
        $this->errorMsg    = '';
        $this->resetValidation();
    }

    public function render()
    {
        $categories = ServiceCategory::query()
            ->when($this->search !== '', fn ($q) =>
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('description', 'ilike', '%' . $this->search . '%')
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.categories', compact('categories'))
            ->layout('layouts.dashboard', ['dashboardTitle' => 'Categorias de Serviços']);
    }
}
