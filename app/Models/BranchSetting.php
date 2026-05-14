<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSetting extends Model
{
    // Since branch_id is the primary key
    protected $primaryKey = 'branch_id';
    public $incrementing = false;

    protected $fillable = [
        'branch_id',
        'currency_id',
        'vat_registration_no',
        'vat_percentage',
        'service_charge_percentage',
        'is_vat_inclusive',
        'timezone',
        'language_code',
        'opening_time',
        'closing_time',
        'receipt_header_title',
        'receipt_footer_text',
        'show_logo_on_receipt',
        'invoice_prefix'
    ];

    protected $casts = [
        'vat_percentage' => 'decimal:2',
        'service_charge_percentage' => 'decimal:2',
        'is_vat_inclusive' => 'boolean',
        'show_logo_on_receipt' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
