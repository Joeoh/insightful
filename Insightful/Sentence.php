<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:11
 */

namespace Insightful;


use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{

    public function review()
    {
        return $this->belongsTo('Insightful\Review');
    }

    public function keywords()
    {
        return $this->hasMany('Insightful\Keyword');
    }

}