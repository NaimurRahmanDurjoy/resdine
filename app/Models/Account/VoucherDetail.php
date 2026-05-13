<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class VoucherDetail extends Model
{
    protected $table = 'acc_voucher_details';

    protected $fillable = [
        'voucher_id',
        'chart_of_account_id',
        'debit',
        'credit',
        'narration'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
