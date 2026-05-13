<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class VoucherSource extends Model
{
    protected $table = 'acc_voucher_sources';

    protected $fillable = [
        'voucher_id',
        'source_type',
        'source_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    // Source Type Constants
    const TYPE_POS_PAYMENT = 1;
    const TYPE_PURCHASE = 2;
    const TYPE_SALARY = 3;
    const TYPE_EXPENSE = 4;

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
