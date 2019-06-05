<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'billing_name', 'billing_email', 'billing_address', 'billing_state',
        'billing_city', 'billing_province', 'billing_lga', 'billing_phone',
        'billing_name_on_card', 'billing_tax', 'billing_total', 'error', 'billing_postal_code',
        'billing_discount', 'billing_discount_code', 'billing_subtotal'
    ];
    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->belongsToMany('App\product')->withPivot('qty');
    }
}
