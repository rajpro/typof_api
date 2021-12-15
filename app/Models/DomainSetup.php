<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class DomainSetup extends Model
{
    protected $table = 'domain_setup';
    protected $fillable = ['store_id','primary', 'custom', 'custom_verification', 'ssl_setup', 'virtual_host', 'domain_status'];
}
