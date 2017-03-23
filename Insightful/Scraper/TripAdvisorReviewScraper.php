<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:18
 */

namespace Insightful\Scraper;

use Insightful\Review;
use Carbon\Carbon;
use simplehtmldom_1_5\simple_html_dom;
use Sunra\PhpSimple\HtmlDomParser;


class TripAdvisorReviewScraper extends ReviewScraper
{
    const baseUrl = "https://www.tripadvisor.ie/";
    const sourceCode = 2;
    function __construct($slug)
    {
        parent::__construct($slug);
    }

    public function getReviewsAfterDate(\DateTime $date) : array
    {
        $url = self::baseUrl . $this->slug;
        //Do first call here as we need to know how many pages to get next
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
        $response = file_get_contents($url, false, $context);
        $html = HtmlDomParser::str_get_html($response);
        $totalReviewsAvailable = self::getNumberOfAvailableReviews($html);
        $carbonDate = Carbon::instance($date);                      //convert datetime to carbon type for comparison
        $reviews=(self::parseReviewElements($html));
        $validReviews = self::removeDatesOutsideRange($reviews,$carbonDate);    //reviews within date range
        $reviews = $validReviews;
        $nextUrls = self::generateUrlsForNextPages($url,$totalReviewsAvailable);    //generate urls for all pages of reviews
        foreach ($nextUrls as $nextUrl) {
            foreach (self::getReviewsAtUrl($nextUrl) as $review){   //array of reviews at URL
                if(!($review->date)->greaterThan($carbonDate)){     //terminate if review date newer than set date
                    return $reviews;
                }
                $reviews[] = $review;
            }
        }
        return $reviews;
    }

    private static function getReviewsAtUrl($url) : array
    {
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
        $response = file_get_contents($url, false, $context);
        $html = HtmlDomParser::str_get_html($response);
        $reviews = self::parseReviewElements($html);
        return $reviews;
    }

    private static function parseReviewElements(simple_html_dom $html) : array
    {
        $reviews = [];
        $reviewElements = $html->find('.reviewSelector');
        foreach ($reviewElements as $reviewElement) {
            $review = new Review();
            $reviewText = ($reviewElement->find(".entry > p")[0]->text());
            $review->text = htmlspecialchars_decode($reviewText,ENT_QUOTES);

            //case when review is cut off, find URL for review and get the text
            if(!empty($reviewElement->find('.partial_entry'))){
                $singleURL = ($reviewElement->find('.wrap',0)->find('a',0))->href;
                $review->text=self::getFullReviewText($singleURL);
            }
            $review->num_words = self::countWords($review->text);
            if(!empty($reviewElement->find('.ratingDate'))) {
                $reviewDateElement = $reviewElement->find('.ratingDate')[0];
                $reviewDate = $reviewDateElement->title;
                //date hidden from text in recent reviews, need to extract from title class
                if(!empty($reviewDate)){
                    $review->date = Carbon::createFromFormat("d M Y", $reviewDate)->hour(0)->minute(0)->second(0); ///date given in form "21 March 2017"
                }
                //for older reviews the date is stored in the text
                else {
                    $reviewDate = $reviewDateElement->text();
                    $review->date = Carbon::createFromFormat("d M Y",trim(substr(strstr($reviewDate," "), 1)))->hour(0)->minute(0)->second(0);
                }
            }
            //extract star rating from bubble image name
            if(!empty($reviewElement->find('.sprite-rating_s_fill'))){
                $reviewRating = ($reviewElement->find('.sprite-rating_s_fill')[0]->alt);
                $review->rating = intval(substr($reviewRating,0,1).PHP_EOL);
            }
            $review->source_id = self::sourceCode;
            if(!empty($reviewElement->find('.scrname'))){
                $review->author = $reviewElement->find('.scrname')[0]->text();
            }
            $reviews[] = $review;
        }
        return $reviews;
    }

    private static function getNumberOfAvailableReviews(simple_html_dom $html) : int
    {
        foreach($html->find('label') as $element){
            $labelText = $element->text();
            if(substr($labelText,0,7)==='English'){
                preg_match('#\((.*?)\)#', $labelText, $match);      //extract number of reviews between brackets
                return intval($match[1]);
            }
        }
    }
    /*
     * Get full review text from a single URL. Used when URL is cut off by "More..." button
     * */
    private static function getFullReviewText(string $slug) : string
    {
        $url = self::baseUrl.$slug;
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
        $response = file_get_contents($url, false, $context);
        $html = HtmlDomParser::str_get_html($response);
        $reviewElements = $html->find('.reviewSelector');
        $reviewElement = $reviewElements[0];
        $reviewText = ($reviewElement->find(".entry > p")[0]->text());
        return htmlspecialchars_decode($reviewText,ENT_QUOTES);
    }

    private static function generateUrlsForNextPages(string $url, int $numberOfReviews) : array
    {
        $numberOfPagesRemaining = ($numberOfReviews / 10);
        $offsetLocation = strpos($url, "-Reviews-") + 9;
        $urls = [];
        for ($i = 1; $i <= $numberOfPagesRemaining; $i++) {
            $offsetString = "or" . ($i * 10) . "-";
            $newstr = substr_replace($url, $offsetString, $offsetLocation, 0);
            $urls[] = $newstr;
        }
        return $urls;
    }

    private static function removeDatesOutsideRange(array $reviews,\Carbon\Carbon $date) : array
    {
        $reviewsInRequiredDateRange = [];
        foreach ($reviews as $review){
            $currentReviewDate = $review->date;
            if(!$currentReviewDate->greaterThan($date)){
                continue;
            }
            $reviewsInRequiredDateRange[] = $review;
        }
        return $reviewsInRequiredDateRange;
    }
}