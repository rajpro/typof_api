<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';

    protected $fillable = [
    	'model_id',
    	'model_type',
    	'type',
    	'data'
    ];

    protected $casts = [
    	'type' => 'string',
    	'data' => 'array'
    ];

    public function model()
    {
        return $this->morphTo();
    }
    
}