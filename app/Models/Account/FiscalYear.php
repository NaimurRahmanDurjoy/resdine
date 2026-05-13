<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $table = 'acc_fiscal_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'fiscal_year_id');
    }

    public static function active()
    {
        return self::where('is_active', true)->first();
    }
}
