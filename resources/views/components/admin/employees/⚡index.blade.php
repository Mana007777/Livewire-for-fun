<?php

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination  , WithoutUrlPagination;
    public function delete($id)
    {
        $employee = Employee::find($id);
        $employee->delete();
        session()->flash('message', 'Employee deleted successfully.');
    }
    public function getCompaniesProperty()
    {
        return Employee::inCompany()->paginate(5);
    }

};
?>

<div>
    {{-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius --}}
</div>