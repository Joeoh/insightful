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

    public function getReviewsAfterDate(\DateTime $date): array
    {
        $url = self::baseUrl . $this->slug;
        //Do first call here as we need to know how many pages to get next
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
        $response = file_get_contents($url, false, $context);
        $html = HtmlDomParser::str_get_html($response);
        $totalReviewsAvailable = self::getNumberOfAvailableReviews($html);
        $carbonDate = Carbon::instance($date);                      //convert datetime to carbon type for comparison
        $reviews = (self::parseReviewElements($html));
        $validReviews = self::removeDatesOutsideRange($reviews, $carbonDate);    //reviews within date range
        $reviews = $validReviews;
        $nextUrls = self::generateUrlsForNextPages($url, $totalReviewsAvailable);    //generate urls for all pages of reviews
        foreach ($nextUrls as $nextUrl) {
            foreach (self::getReviewsAtUrl($nextUrl) as $review) {   //array of reviews at URL
                if (!($review->date)->greaterThan($carbonDate)) {     //terminate if review date newer than set date
                    return $reviews;
                }
                $reviews[] = $review;
            }
        }
        return $reviews;
    }

    /*
     * Get reviews between two dates. Start from end date and work towards start date
     */
    public function getReviewsBetween(\DateTime $start, \DateTime $end)
    {
        //setup
        $url = self::baseUrl . $this->slug;
        $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
        $response = file_get_contents($url, false, $context);
        $html = HtmlDomParser::str_get_html($response);

        $carbonStartDate = Carbon::instance($start);                      //convert datetime to carbon type for comparison
        $carbonEndDate = Carbon::instance($end);
        $totalReviewsAvailable = self::getNumberOfAvailableReviews($html);
        $allUrls = self::generateUrlsForNextPages($url, $totalReviewsAvailable);

        array_unshift($allUrls,$url);                                    //prepend first url
        $endDateIndex = self::getEndDateUrlIndex($allUrls, $carbonEndDate);   //array index containing URL last url before end date i.e. first page of reviews we care about
        $newUrlArray=array_slice($allUrls,$endDateIndex);               //remove unnecessary urls
        $reviews = [];
        //deal with urls on first page
        foreach(self::getReviewsAtUrl($newUrlArray[0]) as $review){
            if(!($review->date)->greaterThan($carbonEndDate)){  //if review date is newer than end date
                $reviews[] = $review;
            }
        }
        //deal with rest of urls, cutting off reviews before start date
        $newUrlArray = array_slice($newUrlArray,1);
        foreach($newUrlArray as $currentUrl){
            foreach (self::getReviewsAtUrl($currentUrl) as $review) {      //array of reviews at URL
                if (!($review->date)->greaterThan($carbonStartDate)) {     //terminate if review date older than start date
                    return $reviews;
                }
                $reviews[] = $review;
            }
        }
        return $reviews;
    }

    private static function getEndDateUrlIndex(array $nextUrls, \Carbon\Carbon $end):int
    {
        $count=0;   //index of url
        foreach ($nextUrls as $currentUrl) {
            $context = stream_context_create(array('http' => array('header' => 'User-Agent: Mozilla compatible')));
            $response = file_get_contents($currentUrl, false, $context);
            $html = HtmlDomParser::str_get_html($response);
            $urlEndDate = self::getLastDateOnPage($html);
            if($end->greaterThan($urlEndDate)){
                return $count;
            }
            $count++;
        }
        return -1;
    }
    //returns the date of the last review on the page
    private static function getLastDateOnPage(simple_html_dom $html):    \Carbon\Carbon
    {
        $lastReviewDateElement = $html->find('.reviewSelector',-1)->find('.ratingDate',0);
        $reviewDateTitle = $lastReviewDateElement->title;
        if(!empty($reviewDateTitle)){
            return Carbon::createFromFormat("d M Y", $reviewDateTitle)->hour(0)->minute(0)->second(0); ///date given in form "21 March 2017"
        }
        //for older reviews the date is stored in the text
        else {
            $reviewDate = $lastReviewDateElement->text();
            return Carbon::createFromFormat("d M Y",trim(substr(strstr($reviewDate," "), 1)))->hour(0)->minute(0)->second(0);
        }

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
            $reviewText = ($reviewElement->find(".entry > p",0)->text());
            $review->text = htmlspecialchars_decode($reviewText,ENT_QUOTES);

            //case when review is cut off, find URL for review and get the text
            if(!empty($reviewElement->find('.partial_entry',0))){
                $singleURL = ($reviewElement->find('.wrap',0)->find('a',0))->href;
                $review->text=self::getFullReviewText($singleURL);
            }
            $review->num_words = self::countWords($review->text);
            $reviewDateElement = $reviewElement->find('.ratingDate',0);
            if(!empty($reviewDateElement)) {
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
            $ratingElement = $reviewElement->find('.sprite-rating_s_fill',0);
            if(!empty($ratingElement)){
                $reviewRating = ($ratingElement->alt);
                $review->rating = intval(substr($reviewRating,0,1));
            }
            $review->source_id = self::sourceCode;
            $authorElement = $reviewElement->find('.scrname',0);
            if(!empty($authorElement)){
                $review->author =$authorElement->text();
            }
            $reviews[] = $review;
        }
        return $reviews;
    }

    private static function getNumberOfAvailableReviews(simple_html_dom $html) : int
    {
        foreach($html->find('label[for=taplc_prodp13n_hr_sur_review_filter_controls_0_filterLang_en]') as $element){
            $labelText = $element->text();
            if(substr($labelText,0,7)==='English'){
                preg_match('#\((.*?)\)#', $labelText, $match);      //extract number of reviews between brackets
                $result = str_replace(",","",$match[1]);            //get rid of commas (values over 1,000)
                return intval($result);
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
        $reviewElement = $html->find('.reviewSelector',0);
        $reviewText = ($reviewElement->find(".entry > p",0)->text());
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