<?php

use App\Models\Payment;
use Livewire\Component;

new class extends Component
{
    public Payment $payment;

    public function mount($id): void
    {
        $this->payment = Payment::inCompany()
            ->with(['employee.designation.department', 'salary'])
            ->whereKey($id)
            ->firstOrFail();
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Payment Details
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    View full details of this payment record.
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

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
            <div class="xl:col-span-8">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4">
                        <flux:heading size="lg">Payment Information</flux:heading>
                    </div>

                    <div class="space-y-5 px-5 py-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Payment ID
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    #{{ $payment->id }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Amount
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ number_format($payment->amount, 2) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Payment Date
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $payment->payment_date ? \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M d, Y') : '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Method
                                </p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900">
                                    {{ $payment->method ?? '—' }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                                    Notes
                                </p>
                                <p class="mt-1 text-sm text-zinc-700">
                                    {{ $payment->notes ?: 'No notes available.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>

            <div class="xl:col-span-4 space-y-6">
                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4">
                        <flux:heading size="lg">Employee</flux:heading>
                    </div>

                    <div class="space-y-3 px-5 py-5">
                        @if ($payment->employee)
                            <p class="text-sm font-semibold text-zinc-900">
                                {{ $payment->employee->name }}
                            </p>

                            <p class="text-sm text-zinc-500">
                                {{ $payment->employee->email ?? '—' }}
                            </p>

                            <p class="text-sm text-zinc-500">
                                {{ $payment->employee->phone ?? '—' }}
                            </p>

                            <div class="pt-2 text-sm text-zinc-500">
                                <p>{{ $payment->employee->designation?->name ?? '—' }}</p>
                                <p>{{ $payment->employee->designation?->department?->name ?? '—' }}</p>
                            </div>
                        @else
                            <p class="text-sm text-zinc-500">
                                No employee assigned.
                            </p>
                        @endif
                    </div>
                </flux:card>

                <flux:card class="border border-zinc-200 shadow-sm">
                    <div class="border-b border-zinc-200 px-5 py-4">
                        <flux:heading size="lg">Related Salary</flux:heading>
                    </div>

                    <div class="px-5 py-5">
                        @if ($payment->salary)
                            <p class="text-sm font-semibold text-zinc-900">
                                Salary #{{ $payment->salary->id }}
                            </p>
                        @else
                            <p class="text-sm text-zinc-500">
                                No salary record linked.
                            </p>
                        @endif
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</div>