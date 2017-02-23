<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;

class Payment extends Model implements BillableContract
{
    use SoftDeletes;
    use Billable;

    protected $dates = ['deleted_at', 'trial_ends_at', 'subscription_ends_at'];

    protected $guarded = array('id');

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
