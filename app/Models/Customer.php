<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Subscription;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status'
    ];

    protected function casts():array {
        return ['status' => 'boolean'];
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }
}