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
    /*    public $id;
        public $author;
        public $date;
        public $text;
        public $rating;
        public $num_words;
        public $source_id;*/


    public function sentences()
    {
        return $this->hasMany('Insightful\Sentence');
    }


    public function campaign()
    {
        return $this->belongsTo('Insightful\Campaign');
    }

    public function source()
    {
        return $this->hasOne('Insightful\Source');
    }

}