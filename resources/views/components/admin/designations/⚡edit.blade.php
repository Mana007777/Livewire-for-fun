<?php

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public ?int $designationId = null;
    public string $name = '';
    public ?int $department_id = null;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('designations', 'name')
                    ->ignore($this->designationId),
            ],
            'department_id' => 'required|integer',
        ];
    }

    public function mount($id): void
    {
        $designation = Designation::inCompany()
            ->whereKey($id)
            ->firstOrFail();

        $this->designationId = $designation->id;
        $this->name = $designation->name;
        $this->department_id = $designation->department_id;
    }

    public function save()
    {
        $this->validate();

        $departmentExists = Department::inCompany()
            ->whereKey($this->department_id)
            ->exists();

        if (! $departmentExists) {
            $this->addError('department_id', 'The selected department is invalid.');
            return;
        }

        Designation::inCompany()
            ->whereKey($this->designationId)
            ->update([
                'name' => $this->name,
                'department_id' => $this->department_id,
            ]);

        session()->flash('success', 'Designation updated successfully.');

        return redirect()->route('designations.index');
    }

    public function getDepartmentsProperty()
    {
        return Department::inCompany()
            ->orderBy('name')
            ->get();
    }
};
?>
<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Edit Designation
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Update the designation details for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('designations.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Designations
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                        <flux:heading size="lg">Designation Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Edit the primary details for this designation.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Designation Name
                                </label>

                                <input
                                    type="text"
                                    wire:model="name"
                                    placeholder="Enter designation name"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10">

                                @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Department
                                </label>

                                <select
                                    wire:model="department_id"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10">
                                    <option value="">Select department</option>
                                    @foreach ($this->departments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('department_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="xl:col-span-4">
                <div class="space-y-6">
                    <flux:card class="border border-zinc-200 shadow-sm">
                        <div class="border-b border-zinc-200 px-5 py-4">
                            <flux:heading size="lg">Record Info</flux:heading>
                        </div>

                        <div class="space-y-4 px-5 py-5">
                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Designation ID
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    #{{ $designationId }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-zinc-50 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Status
                                </p>
                                <div class="mt-2">
                                    <flux:badge color="emerald">Ready to update</flux:badge>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            <div class="xl:col-span-12">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="flex flex-col gap-3 px-5 py-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-zinc-900">
                                Save your changes
                            </p>
                            <p class="text-sm text-zinc-500">
                                Make sure the designation information is correct before updating.
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('designations.index') }}" wire:navigate>
                                <flux:button variant="ghost">
                                    Cancel
                                </flux:button>
                            </a>

                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">Update Designation</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>