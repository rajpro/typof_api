<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;

class Store extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'store_table';
    protected $primaryKey = 'store_id';
    protected $fillable = ['store_name','address','country','logo','website', 'folder_name','favicon'];

    public function setting()
    {
        return $this->morphMany(Setting::class, 'model');
    }

    public function extra()
    {
        return $this->morphMany(Extras::class, 'model');
    }
    
    public function products() {
        return $this->hasMany(\App\Models\Product::class, 'store_id', 'store_id');
    }

    public function orders() {
        return $this->hasMany(\App\Models\Order::class, 'store_id', 'store_id');
    }

    public function customers() {
        return $this->hasMany(\App\Models\Customer::class, 'store_id', 'store_id');
    }

    public function store_category() {
        return $this->hasMany(\App\Models\StoreCategory::class, 'store_id', 'store_id');
    }
}
