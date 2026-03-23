<?php

use Livewire\Component;
use App\Models\Designation;
new class extends Component
{
    public $designation;
    public function rules()
    {
        return [
            'designation.name' => 'required|string|max:255|unique:designations',
            'designation.department_id' => 'required|exists:departments,id',
            ];
    }
    public function mount()
    {
        $this->designation = new Designation();
    }
    public function save()
    {
        $this->validate();
        $this->designation->save();
        session()->flash('success', 'Designation created successfully.');
        return $this->redirectIntended('designations.index');
    }
};
?>

<div>
    {{-- Smile, breathe, and go slowly. - Thich Nhat Hanh --}}
</div>