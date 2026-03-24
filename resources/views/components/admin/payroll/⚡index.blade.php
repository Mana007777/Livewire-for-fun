<?php

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected string $paginationTheme = 'tailwind';

    public string $monthYear = '';

    public function rules(): array
    {
        return [
            'monthYear' => 'required|date_format:Y-m',
        ];
    }

    public function generatePayroll(): void
    {
        $this->validate();

        $date = Carbon::createFromFormat('Y-m', $this->monthYear);

        $exists = Payroll::inCompany()
            ->where('month', $date->format('m'))
            ->where('year', $date->format('Y'))
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'monthYear' => ['Payroll for this month already exists.'],
            ]);
        }

        $payroll = Payroll::create([
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
            'company_id' => session('company_id'),
        ]);

        foreach (Employee::inCompany()->get() as $employee) {
            $contract = $employee->getActiveContract(
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString()
            );

            if ($contract) {
                $payroll->salaries()->create([
                    'employee_id' => $employee->id,
                    'gross_salary' => $contract->getTotalEarnings($date->format('Y-m')),
                ]);
            }
        }

        session()->flash('success', 'Payroll generated successfully.');
        $this->reset('monthYear');
        $this->resetPage();
    }

    public function updatePayroll($id): void
    {
        $payroll = Payroll::inCompany()
            ->whereKey($id)
            ->firstOrFail();

        $payroll->salaries()->delete();

        $periodStart = Carbon::createFromFormat('Y-m-d', $payroll->year . '-' . $payroll->month . '-01');
        $periodEnd = $periodStart->copy()->endOfMonth();

        foreach (Employee::inCompany()->get() as $employee) {
            $contract = $employee->getActiveContract(
                $periodStart->toDateString(),
                $periodEnd->toDateString()
            );

            if ($contract) {
                $payroll->salaries()->create([
                    'employee_id' => $employee->id,
                    'gross_salary' => $contract->getTotalEarnings($payroll->year . '-' . $payroll->month),
                ]);
            }
        }

        session()->flash('success', 'Payroll updated successfully.');
    }

    public function getPayrollsProperty()
    {
        return Payroll::inCompany()
            ->withCount('salaries')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(10);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Payroll
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Generate and manage payroll records for the selected company.
                </flux:subheading>
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
                <flux:heading size="lg">Generate Payroll</flux:heading>
                <flux:subheading class="mt-1 text-zinc-500">
                    Select a month to generate payroll for all active employees.
                </flux:subheading>
            </div>

            <div class="px-5 py-5 sm:px-6">
                <form wire:submit.prevent="generatePayroll" class="flex flex-col gap-4 md:flex-row md:items-end">
                    <div class="w-full md:max-w-xs">
                        <label class="mb-2 block text-sm font-medium text-zinc-700">
                            Month
                        </label>

                        <input
                            type="month"
                            wire:model="monthYear"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        >

                        @error('monthYear')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <flux:button variant="primary" type="submit">
                            Generate Payroll
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:card>

        <flux:card class="border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <flux:heading size="lg">Payroll List</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            View generated payroll runs and update them when needed.
                        </flux:subheading>
                    </div>

                    <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700">
                        Total: {{ $this->payrolls->total() }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if ($this->payrolls->count())
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    ID
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Month
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Salaries
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($this->payrolls as $payroll)
                                <tr wire:key="payroll-{{ $payroll->id }}">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900">
                                        #{{ $payroll->id }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-700">
                                        {{ $payroll->month_string }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $payroll->salaries_count }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('payrolls.show', $payroll->id) }}" wire:navigate>
                                                <flux:button size="sm" variant="ghost">
                                                    View
                                                </flux:button>
                                            </a>

                                            <flux:button
                                                size="sm"
                                                variant="primary"
                                                wire:click="updatePayroll({{ $payroll->id }})"
                                            >
                                                Update
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-14 text-center">
                        <p class="text-sm font-semibold text-zinc-900">No payrolls found</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            Generate your first payroll for this company.
                        </p>
                    </div>
                @endif
            </div>

            @if ($this->payrolls->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4 sm:px-6">
                    {{ $this->payrolls->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>