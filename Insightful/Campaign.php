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

    public $id;
    public $business_name;
    public $yelp_slug;

    public function user(){
        $this->hasOne('App\User');
    }


    public function reviews() {
        $this->hasMany('Insightful\Review');
    }
}