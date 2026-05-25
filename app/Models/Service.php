<?php

declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Subscription;

class Service extends Model
{
    protected $fillable = [
        "name",
        "price",
        "description",
        "status"
    ];

    /**
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }
}