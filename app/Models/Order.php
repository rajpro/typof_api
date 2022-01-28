<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order_table';
    protected $primaryKey = 'order_id';
    protected $fillable = ['store_id','customer_id','address_id', 'address_detail', 'items','total_price','payment_mode','status', 'special_request', 'discount', 'coupon_code', 'shipping_charge'];

    protected $casts =[
        'address_detail'=>'array',
        'items' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function product_order()
    {
        return $this->belongsTo(ProductOrder::class, 'order_id', 'order_id');
    }

    public function getItemsAttribute($value)
    {
        $data = [];
        $items = json_decode($value);
        if(!empty($items)){
            foreach ($items as $key => $value) {
                $d = $value;
                $d->pid = $key;
                $data[] = $d;
            }
        }else{
            $data = null;
        }
        return $data;
    }

    public function getInvoiceIdAttribute($value)
    {
    	return date("Ymd", strtotime($this->created_at))."-".$value;
    }
}