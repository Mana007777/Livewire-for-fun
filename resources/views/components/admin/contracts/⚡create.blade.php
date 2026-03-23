<?php

use App\Models\Department;
use Livewire\Component;

new class extends Component
{
    public $department;
    public function rules()
    {
        return [
            'department' => 'required|string|max:255',
        ];
    }

    public function mount()
    {
        $this->department = new Department();
    }

    public function save()
    {
        $this->validate();
        $this->department->save();
        session()->flash('success', 'Department created successfully.');
        return $this->redirectIntended(route('departments.index'));
    }
};
?>

<div>
    {{-- Happiness is not something readymade. It comes from your own actions. - Dalai Lama --}}
</div>