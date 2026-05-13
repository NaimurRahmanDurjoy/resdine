<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $table = 'acc_chart_of_accounts';

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'account_type_id',
        'opening_balance',
        'balance_type',
        'allow_transaction',
        'is_system',
        'status',
        'description'
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function voucherDetails()
    {
        return $this->hasMany(VoucherDetail::class, 'chart_of_account_id');
    }

    /**
     * Calculate current balance based on voucher entries.
     */
    public function getBalanceAttribute()
    {
        $debits = $this->voucherDetails()->sum('debit');
        $credits = $this->voucherDetails()->sum('credit');

        if ($this->balance_type === 'D') {
            return $this->opening_balance + $debits - $credits;
        } else {
            return $this->opening_balance + $credits - $debits;
        }
    }
}
