<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = 'acc_account_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'status'
    ];

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'account_type_id');
    }
}
