<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:11
 */

namespace Insightful;


use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $id;
    public $author;
    public $date;
    public $text;
    public $rating;
    public $num_words;



    public function keywords(){
        $this->hasMany('Insightful\Keyword');
    }


    public function campaign(){
        $this->hasOne('Insightful\Campaign');
    }

    public function source(){
        $this->hasOne('Insightful\Source');
    }

    public function id() {
        return $this->id;
    }
}