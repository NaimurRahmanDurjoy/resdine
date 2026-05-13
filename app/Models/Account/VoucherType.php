<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class VoucherType extends Model
{
    protected $table = 'acc_voucher_types';

    protected $fillable = [
        'name',
        'prefix',
        'starting_number',
        'status'
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'voucher_type_id');
    }
}
