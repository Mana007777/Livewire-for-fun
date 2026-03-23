<?php

use App\Models\Designation;
use Livewire\Component;

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
    public function mount($id)
    {
        $this->designation =Designation::find($id);
    }
    public function save()
    {
        $this->validate();
        $this->designation->save();
        session()->flash('success', 'Designation updated successfully.');
        return $this->redirectIntended(route('designations.index'));
    }
};
?>

<div>
    {{-- Simplicity is the essence of happiness. - Cedric Bledsoe --}}
</div>