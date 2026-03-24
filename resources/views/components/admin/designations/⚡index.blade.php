<?php

use App\Models\Designation;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected string $paginationTheme = 'tailwind';

    public function delete($id): void
    {
        Designation::inCompany()->findOrFail($id)->delete();

        session()->flash('success', 'Designation deleted successfully.');

        if ($this->page > 1 && Designation::inCompany()->paginate(5)->isEmpty()) {
            $this->previousPage();
        }
    }

    public function getDesignationsProperty()
    {
        return Designation::inCompany()
            ->latest()
            ->paginate(5);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Designations
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Manage designation records for the selected company.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('designations.create') }}" wire:navigate>
                    <flux:button variant="primary">
                        Create Designation
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3000)"
                x-show="show"
                x-transition
                style="display:none;"
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
            >
                {{ session('success') }}
            </div>
        @endif

        <flux:card class="border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <flux:heading size="lg">Designation List</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            View and manage all designations assigned to the current company.
                        </flux:subheading>
                    </div>

                    <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700">
                        Total: {{ $this->designations->total() }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if ($this->designations->count())
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    ID
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Name
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Created
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($this->designations as $designation)
                                <tr wire:key="designation-{{ $designation->id }}">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900">
                                        #{{ $designation->id }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-zinc-700">
                                        {{ $designation->name }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $designation->created_at?->format('M d, Y') }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('designations.edit', $designation->id) }}" wire:navigate>
                                                <flux:button size="sm" variant="ghost">
                                                    Edit
                                                </flux:button>
                                            </a>

                                            <flux:button
                                                size="sm"
                                                variant="danger"
                                                wire:click="delete({{ $designation->id }})"
                                                wire:confirm="Are you sure you want to delete this designation?"
                                            >
                                                Delete
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                            </svg>
                        </div>

                        <p class="mt-4 text-sm font-semibold text-zinc-900">No designations found</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            Create your first designation for this company.
                        </p>

                        <div class="mt-5">
                            <a href="{{ route('designations.create') }}" wire:navigate>
                                <flux:button variant="primary">
                                    Create Designation
                                </flux:button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if ($this->designations->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4 sm:px-6">
                    {{ $this->designations->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>