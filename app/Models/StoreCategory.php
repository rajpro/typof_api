<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;

class StoreCategory extends Model implements HasMedia
{
	use InteractsWithMedia;
	
    protected $table = 'store_category';
    protected $primaryKey = 'id';
    protected $fillable = ['store_id','category_name','sub_category','slug', 'image'];

    public function getSubCategoryAttribute($value)
    {
        if(!empty($value)){
            return explode(",", $value);
        }else{
            return null;
        }
    }

    public function getMediaCollectionAttribute()
    {
        $media = Media::where(['model_id'=>$this->id,'collection_name'=>'category'])->first();
        if(!empty($media)){
            return $media->getUrl();
        }else{
            return "https://typof.in/dashboard/images/no-image.png";
        }
    }
}