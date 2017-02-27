<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 24/02/2017
 * Time: 12:20
 */

namespace Insightful;


use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{

    protected $table = "review_keywords";
    public $position;
    public $word;

    public function review(){
        $this->hasOne('Insightful\Review');
    }

}