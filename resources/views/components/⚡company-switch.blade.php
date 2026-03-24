<?php

use App\Models\Company;
use Livewire\Component;

new class extends Component
{
    public Company $company;

    public function mount(Company $company)
    {
        $this->company = $company;
    }

    public function selectCompany()
    {
        session()->put('company_id', $this->company->id);

        $this->redirect(route('dashboard'), navigate: true);
    }
};
?>
<div>
    <flux:menu.item wire:click="selectCompany" class="cursor-pointer">
        <div class="flex w-full items-center justify-between">
            <span>{{ $company->name }}</span>

            @if (session('company_id') == $company->id)
                <span class="text-xs font-medium text-emerald-600">Selected</span>
            @endif
        </div>
    </flux:menu.item>
</div>