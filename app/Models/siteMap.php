<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class siteMap extends Model
{
    protected $table = 'themeforest_sitemap';
    
    protected $fillable = ['url','status'];
}
