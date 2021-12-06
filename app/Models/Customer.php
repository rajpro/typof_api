<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer_table';
    protected $primaryKey = 'customer_id';
    protected $fillable = ['store_id','customer_name','mobile', 'otp', 'email_id'];

    public function orders()
    {
    	return $this->hasMany(\App\Models\Order::class, 'customer_id', 'customer_id')->where('status', '!=', 'pending');
    }

    public function getOrderCountAttribute()
    {
    	return $this->orders()->count();
    }
}