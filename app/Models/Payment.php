<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    protected $fillable = ['fk_id', 'table_name', 'ref_no'];

    public function paymentMethod()
    {
        return $this->belongsTo(Category::class, 'payment_method_id');
    }

    public function paymentAccount()
    {
    	return $this->belongsTo(Category::class, 'payment_account_id');
    }
}
