<?php

use App\Models\Employee;
use App\Models\Payment;
use App\Models\Salary;
use Livewire\Component;

new class extends Component
{
    public ?int $salary_id = null;
    public ?int $employee_id = null;
    public string $amount = '';
    public string $payment_date = '';
    public string $method = '';
    public string $notes = '';

    public function mount(?int $salary_id = null): void
    {
        if ($salary_id) {
            $salary = Salary::query()
                ->whereHas('payroll', fn ($q) => $q->inCompany())
                ->with('employee')
                ->whereKey($salary_id)
                ->firstOrFail();

            $this->salary_id = $salary->id;
            $this->employee_id = $salary->employee_id;
            $this->amount = (string) $salary->gross_salary;
            $this->payment_date = now()->toDateString();
        }
    }

    public function rules(): array
    {
        return [
            'salary_id' => 'nullable|integer',
            'employee_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public function save()
    {
        $this->validate();

        $employee = Employee::inCompany()
            ->whereKey($this->employee_id)
            ->first();

        if (! $employee) {
            $this->addError('employee_id', 'The selected employee is invalid.');
            return;
        }

        $salary = null;

        if ($this->salary_id) {
            $salary = Salary::query()
                ->whereHas('payroll', fn ($q) => $q->inCompany())
                ->whereKey($this->salary_id)
                ->where('employee_id', $this->employee_id)
                ->first();

            if (! $salary) {
                $this->addError('salary_id', 'The selected salary is invalid.');
                return;
            }
        }

        Payment::create([
            'employee_id' => $employee->id,
            'salary_id' => $salary?->id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'method' => $this->method,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Payment recorded successfully.');

        return $this->redirect(route('payments.index'), navigate: true);
    }

    public function getEmployeesProperty()
    {
        return Employee::inCompany()
            ->orderBy('name')
            ->get();
    }

    public function getSalariesProperty()
    {
        return Salary::query()
            ->whereHas('payroll', fn ($q) => $q->inCompany())
            ->with('employee', 'payroll')
            ->latest()
            ->get();
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Create Payment
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    Record an employee salary payment.
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('payments.index') }}" wire:navigate>
                    <flux:button variant="ghost">
                        Back to Payments
                    </flux:button>
                </a>
            </div>
        </div>

        <flux:separator />

        <form wire:submit.prevent="save" class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                        <flux:heading size="lg">Payment Information</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            Enter payment details for the selected employee.
                        </flux:subheading>
                    </div>

                    <div class="px-5 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Employee
                                </label>

                                <select
                                    wire:model="employee_id"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                    <option value="">Select employee</option>
                                    @foreach ($this->employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>

                                @error('employee_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Related Salary
                                </label>

                                <select
                                    wire:model="salary_id"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >
                                    <option value="">Select salary (optional)</option>
                                    @foreach ($this->salaries as $salary)
                                        <option value="{{ $salary->id }}">
                                            #{{ $salary->id }} - {{ $salary->employee?->name }} - {{ number_format($salary->gross_salary, 2) }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('salary_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Amount
                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="amount"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('amount')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Payment Date
                                </label>

                                <input
                                    type="date"
                                    wire:model="payment_date"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('payment_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Method
                                </label>

                                <input
                                    type="text"
                                    wire:model="method"
                                    placeholder="e.g. Bank Transfer, Cash, Mobile Wallet"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                >

                                @error('method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-zinc-700">
                                    Notes
                                </label>

                                <textarea
                                    wire:model="notes"
                                    rows="4"
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                                ></textarea>

                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="xl:col-span-4">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4">
                        <flux:heading size="lg">Actions</flux:heading>
                    </div>

                    <div class="px-5 py-5">
                        <flux:button variant="primary" type="submit" class="w-full">
                            Save Payment
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </form>
    </div>
</div>