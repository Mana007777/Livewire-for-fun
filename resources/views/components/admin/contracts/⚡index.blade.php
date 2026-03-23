<?php

use App\Models\Department;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination  , WithoutUrlPagination;

    public function delete($id)
    {
        Department::find($id)->delete();
        session()->flash('success', 'Department deleted successfully.');
    }
    public function getCompaniesProperty()
    {
        return Department::latest()->paginate(10);
    }
};
?>

<div>
    {{-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant --}}
</div>