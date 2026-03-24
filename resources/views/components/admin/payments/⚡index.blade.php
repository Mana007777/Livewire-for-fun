<?php

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected string $paginationTheme = 'tailwind';

    public function getPaymentsProperty()
    {
        return Payment::inCompany()
            ->with(['employee', 'salary'])
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="w-full px-4 py-5 sm:px-6 lg:px-8">
    <div class="w-full space-y-6">

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0">
                <flux:heading size="xl" class="tracking-tight text-zinc-900">
                    Payments
                </flux:heading>

                <flux:subheading size="lg" class="mt-1 text-zinc-500">
                    View payment records for the selected company.
                </flux:subheading>
            </div>
        </div>

        <flux:separator />

        <flux:card class="border border-zinc-200 shadow-sm">
            <div class="border-b border-zinc-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <flux:heading size="lg">Payment List</flux:heading>
                        <flux:subheading class="mt-1 text-zinc-500">
                            View all recorded employee payments.
                        </flux:subheading>
                    </div>

                    <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-700">
                        Total: {{ $this->payments->total() }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if ($this->payments->count())
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    ID
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Employee
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Amount
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Payment Date
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Method
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach ($this->payments as $payment)
                                <tr wire:key="payment-{{ $payment->id }}">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-zinc-900">
                                        #{{ $payment->id }}
                                    </td>

                                    <td class="px-5 py-4 text-sm text-zinc-700">
                                        {{ $payment->employee?->name ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $payment->payment_date ? \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M d, Y') : '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-zinc-500">
                                        {{ $payment->method ?? '—' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('payments.show', $payment->id) }}" wire:navigate>
                                                <flux:button size="sm" variant="ghost">
                                                    View
                                                </flux:button>
                                            </a>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-2.21 0-4 1.343-4 3s1.79 3 4 3 4 1.343 4 3-1.79 3-4 3m0-12c1.657 0 3 1.343 3 3M12 8V7m0 13v-1" />
                            </svg>
                        </div>

                        <p class="mt-4 text-sm font-semibold text-zinc-900">No payments found</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            No payment records are available for this company yet.
                        </p>
                    </div>
                @endif
            </div>

            @if ($this->payments->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4 sm:px-6">
                    {{ $this->payments->links() }}
                </div>
            @endif
        </flux:card>
    </div>
</div>