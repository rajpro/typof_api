<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderComm extends Model
{
    protected $table = 'order_communication';
    protected $primaryKey = 'ocomm_id';
    protected $fillable = ['store_id','porder_id', 'admin_msg', 'customer_msg'];

    protected $casts = [
        'admin_msg' => 'array',
        'customer_msg' => 'array'
    ];
}