<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            welcome {{ auth()->user()->name }}
        </flux:subheading>
        <flux:separator />
    </div>
</div>