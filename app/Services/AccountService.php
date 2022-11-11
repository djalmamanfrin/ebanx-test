<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AccountService
{
    public function getBalance(): int
    {
        $transactions = $this->getTransactions();
        if ($transactions->isEmpty()) {
            return 0;
        }
        return $transactions->pluck('amount')->sum();
    }

    private function getTransactions(): Collection
    {
        return collect();
    }
}
