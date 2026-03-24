<?php

use App\Models\Department;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function delete($id)
    {
        $department = Department::find($id);

        if (! $department) {
            session()->flash('error', 'Department not found.');
            return;
        }

        $department->delete();

        session()->flash('success', 'Department deleted successfully.');
    }

    public function getDepartmentsProperty()
    {
        return Department::inCompany()->paginate(5);
    }
};
?>
<div>
    {{-- Simplicity is the essence of happiness. - Cedric Bledsoe --}}
</div>