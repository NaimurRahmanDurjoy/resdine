<?php

namespace App\Models\Account;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $table = 'acc_vouchers';

    protected $fillable = [
        'branch_id',
        'voucher_no',
        'voucher_type_id',
        'fiscal_year_id',
        'voucher_date',
        'reference_no',
        'total_debit',
        'total_credit',
        'narration',
        'status',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'approved_at' => 'datetime',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function voucherType()
    {
        return $this->belongsTo(VoucherType::class, 'voucher_type_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function details()
    {
        return $this->hasMany(VoucherDetail::class, 'voucher_id');
    }

    public function sources()
    {
        return $this->hasMany(VoucherSource::class, 'voucher_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
