<?php
namespace App\Models;
use App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $table = 'product_order_table';
    protected $primaryKey = 'porder_id';
    protected $fillable = ['customer_id','order_id','address_id','product_id','quantity','price','status', 'size'];

    public function product() {
        return $this->hasOne(\App\Models\Product::class, 'product_id', 'product_id');
    }

    public function products() {
        return $this->hasMany(\App\Models\Product::class, 'product_id', 'product_id');
    }

    public function orderCom(){
    	return $this->hasMany(\App\Models\OrderComm::class,'porder_id','porder_id');
    }

    public function order(){
        return $this->belongsTo(\App\Models\Order::class,'order_id','order_id');
    }
}