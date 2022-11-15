<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @package App\Models
 *
 * @property-read int $id
 * @property-read  float $amount
 * @property-read  int $origin
 * @property-read  int $destination
 * @property-read  string $type
 */
class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'type', 'origin', 'destination', 'amount'
    ];

    public function getAmount(): float
    {
        $amount = $this->amount;
        if (!is_numeric($amount)) {
            throw new InvalidArgumentException('Amount not set as float');
        }
        return (float) $amount;
    }
}
