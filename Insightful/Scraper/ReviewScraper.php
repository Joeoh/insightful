<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:07
 */

namespace Insightful\Scraper;


use Carbon\Carbon;

abstract class ReviewScraper
{
    public $slug;
    public $totalReviews;
    public $latestReview;

    function __construct(string $slug){
        $this->slug = $slug;
    }

    public abstract function getReviewsAfterDate(\DateTime $date) : array;

    //Removes encoded HTML encoded tags including single quotes
    public static function cleanHtmlTags(string $text) : string {
        return htmlspecialchars_decode($text,ENT_QUOTES);
    }


    //Checks array of Reviews and only returns those with a date greater than the cutoff
    protected static function removeReviewsOutsideRange(array $reviews, \DateTime $date) : array
    {
        $reviewsInRequiredDateRange = [];
        foreach ($reviews as $review){
            $currentReviewDate = Carbon::createFromFormat("Y-m-d",$review->date);
            if(!$currentReviewDate->greaterThan($date)){
                continue;
            }
            $reviewsInRequiredDateRange[] = $review;
        }

        return $reviewsInRequiredDateRange;
    }


    //Wrap inbuilt method incase we need to alter text before
    public static function countWords(string $text) : int {
        return str_word_count($text);
    }
}