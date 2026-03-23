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

    public function mount($id)
    {
        $this->department = Department::find($id);
    }   

    public function save()
    {
        $this->validate();
        $this->department->save();
        session()->flash('success', 'Department updated successfully.');
        return $this->redirectIntended(route('departments.index'));
    }
};
?>

<div>
    {{-- Simplicity is an acquired taste. - Katharine Gerould --}}
</div>