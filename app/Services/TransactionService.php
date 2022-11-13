<?php

namespace App\Services;

use App\Enums\TypesEnum;
use App\Models\Account;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

abstract class TransactionService
{
    const MINIMUM_ALLOWED_VALUE = 1;

    protected Account $account;
    protected Event $event;
    protected float $amount;

    public function __construct(Account $account, Event $event)
    {
        $this->account = $account;
        $this->event = $event;
        $this->amount = $this->event->getAmount();
    }

    private function isTheMinimumAllowed(): bool
    {
        return $this->amount >= self::MINIMUM_ALLOWED_VALUE;
    }

    public function checkingMinimumAllowed(): void
    {
        if (!$this->isTheMinimumAllowed()) {
            $message = "The amount %s informed must be greater than or equal to %s";
            throw new InvalidArgumentException(sprintf($message, $this->amount, self::MINIMUM_ALLOWED_VALUE));
        }
    }

    /**
     * @throws Throwable
     */
    public function persist(): bool
    {
        $this->checkingMinimumAllowed();
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
                'amount' => $this->amount
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
