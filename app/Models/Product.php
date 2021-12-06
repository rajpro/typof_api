<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'product_table';
    protected $primaryKey = 'product_id';
    protected $fillable = ['store_id','seller_id','category','sub_category','product_name','available','mrp','price','cost','description','image','info', 'published_status', 'slug', 'special_category', 'is_saleable', 'sku', 'size_chart', 'brand', 'shipping_cost', 'video'];

    public function setting()
    {
        return $this->morphOne(\App\Models\Setting::class, 'model');
    }

    public function getMediaCollectionAttribute()
    {
        $media = Media::where(['model_id'=>$this->product_id,'collection_name'=>'products'])->first();
        if(!empty($media)){
            return $media->getUrl();
        }else{
            return "https://typof.in/dashboard/images/no-image.png";
        }
    }
}