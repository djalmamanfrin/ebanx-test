<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Event;
use App\TypesEnum;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

abstract class TransactionService
{
    const MINIMUM_ALLOWED_VALUE = 1;

    protected Account $account;
    protected Event $event;

    public function __construct(Account $account, Event $event)
    {
        $this->account = $account;
        $this->event = $event;
    }

    private function isTheMinimumAllowed(float $amount): bool
    {
        return $amount >= self::MINIMUM_ALLOWED_VALUE;
    }

    public function checkingMinimumAllowed(float $amount): void
    {
        if (!$this->isTheMinimumAllowed($amount)) {
            $message = "The amount informed must be greater than or equal to" . self::MINIMUM_ALLOWED_VALUE;
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @throws Throwable
     */
    public function persist(): bool
    {
        $amount = $this->event->amount;
        $this->checkingMinimumAllowed($amount);
        try {
            DB::beginTransaction();
            if (is_null($this->account->id)) {
                $this->account->saveOrFail();
            }

            if (is_null($this->event->id)) {
                $this->event->saveOrFail();
            }

            $attributes = [
                'type_id' => TypesEnum::DEPOSIT_ID,
                'account_id' => $this->account->id,
                'event_id' => $this->event->id,
                'amount' => $amount
            ];
            return $this->account->transactions()
                ->make($attributes)
                ->saveOrFail();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
