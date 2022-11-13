<?php

namespace App\Services;

use App\TypesEnum;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class DepositService extends TransactionService
{
    public function setAmount(float $amount): DepositService
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @throws Throwable
     */
    public function persist(): bool
    {
        $amount = $this->amount;
        if (!$this->isTheMinimumAllowed($amount)) {
            $message = "The deposit amount informed must be greater than or equal to" . self::MINIMUM_ALLOWED_VALUE;
            throw new InvalidArgumentException($message);
        }

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
