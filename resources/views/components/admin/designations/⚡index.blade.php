<?php

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination  , WithoutUrlPagination;

    public function delete($id)
    {
        Designation::find($id)->delete();
        session()->flash('success', 'Designation deleted successfully.');
    }
    public function getCompaniesProperty()
    {
        return Designation::inCompany()->paginate(5);
    }
};
?>

<div>
    {{-- Life is available only in the present moment. - Thich Nhat Hanh --}}
</div>