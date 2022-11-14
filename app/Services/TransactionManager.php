<?php

namespace App\Services;

use App\Enums\TypesEnum;
use App\Models\Event;
use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class TransactionManager
{
    private Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    private function getAccount(int $accountId): Account
    {
        $service = new AccountService($accountId);
        return $service->get();
    }

    public function getOriginAccount(): Account
    {
        $originAccountId = $this->event->origin;
        return $this->getAccount($originAccountId);
    }

    public function getDestinationAccount(): Account
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
    private function deposit(): void
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
    }

    /**
     * @throws \Throwable
     */
    private function withdraw(): void
    {
        $originAccount = $this->getOriginAccount();
        $withdraw = new WithdrawService($originAccount, $this->event);
        $isWithdrawn = $withdraw->persist();
        if (!$isWithdrawn) {
            throw new Exception("Error to withdraw amount");
        }
    }

    /**
     * @throws \Throwable
     */
    private function transfer(): void
    {
        try {
            DB::beginTransaction();
            $this->withdraw();
            $this->deposit();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Throwable
     */
    public function persist(): void
    {
        $type = $this->event->type;
        switch ($type) {
            case TypesEnum::deposit():
                $this->deposit();
            break;
            case TypesEnum::withdraw():
                $this->withdraw();
                break;
            case TypesEnum::transfer():
                $this->transfer();
                break;
            default:
                throw new NotAcceptableHttpException("Type $type informed not acceptable");
        }
    }
}
