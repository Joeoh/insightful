<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:11
 */

namespace Insightful;


use Illuminate\Database\Eloquent\Model;

class UnprocessedReview extends Model
{
    public $userName;
    public $source;
    public $date;
    public $text;
    public $percentage;

}