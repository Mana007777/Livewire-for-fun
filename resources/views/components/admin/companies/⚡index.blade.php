<?php

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;
    public function delete($id)
    {
        $company = Company::find($id);
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        $company->delete();
        session()->flash('message', 'Company deleted successfully.');
    }
    public function getCompaniesProperty()
    {
        return Company::latest()->paginate(10);
    }
};
?>

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">Companies</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            List of companies 
        </flux:subheading>
        <flux:separator />
    </div>
</div>