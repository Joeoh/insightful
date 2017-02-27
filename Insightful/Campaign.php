<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/02/2017
 * Time: 12:16
 */

namespace Insightful;


use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{

    public function user(){
        $this->belongsTo('App\User');
    }

    public function reviews() {
       return $this->hasMany('Insightful\Review');
    }
}