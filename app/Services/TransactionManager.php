<?php

namespace App\Services;

use App\Enums\TypesEnum;
use App\Models\Event;
use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Throwable;

class TransactionManager
{
    private Event $event;

    public function reset(): void
    {
        Transaction::query()->truncate();
        Event::query()->truncate();
        Account::query()->truncate();
    }

    private function setEvent(array $payload): void
    {
        $this->event = new Event($payload);
        $this->event->save();
    }

    private function getAccount(int $accountId): Account
    {
        $service = new AccountService($accountId);
        return $service->get();
    }

    private function getOriginAccount(): Account
    {
        $originAccountId = $this->event->origin;
        return $this->getAccount($originAccountId);
    }

    private function getDestinationAccount(): Account
    {
        $originAccountId = $this->event->destination;
        return $this->getAccount($originAccountId);
    }

    public function getBalance(int $accountId): float
    {
        return $this
            ->getAccount($accountId)
            ->getBalance();
    }

    /**
     * @throws \Throwable
     */
    private function deposit(): array
    {
        try {
            $destinationAccount = $this->getDestinationAccount();
        } catch (ModelNotFoundException) {
            $destinationAccount = new Account();
            $destinationAccount->id = $this->event->destination;
            $destinationAccount->save();
        }
        $deposit = new DepositService($destinationAccount, $this->event);
        $isDeposited = $deposit->persist();
        if (!$isDeposited) {
            throw new Exception("Error to deposit amount");
        }
        return [
            'destination' => [
                'id' => $destinationAccount->id,
                'balance' => $this->getBalance($destinationAccount->id)
            ]
        ];
    }

    /**
     * @throws \Throwable
     */
    private function withdraw(): array
    {
        $originAccount = $this->getOriginAccount();
        $withdraw = new WithdrawService($originAccount, $this->event);
        $isWithdrawn = $withdraw->persist();
        if (!$isWithdrawn) {
            throw new Exception("Error to withdraw amount");
        }
        return [
            'origin' => [
                'id' => $originAccount->id,
                'balance' => $this->getBalance($originAccount->id)
            ]
        ];
    }

    /**
     * @throws \Throwable
     */
    private function transfer(): array
    {
        $withdraw = $this->withdraw();
        $deposit = $this->deposit();
        return array_merge($withdraw, $deposit);
    }

    /**
     * @throws \Throwable
     */
    public function persist(array $payload): array
    {
        try {
            DB::beginTransaction();
            $this->setEvent($payload);

            $type = $this->event->type;
            $response = match ($type) {
                TypesEnum::deposit() => $this->deposit(),
                TypesEnum::withdraw() => $this->withdraw(),
                TypesEnum::transfer() => $this->transfer(),
                default => throw new NotAcceptableHttpException("Type $type informed not acceptable"),
            };

            DB::commit();
            return $response;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
