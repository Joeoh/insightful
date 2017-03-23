<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 06/02/2017
 * Time: 22:01
 */

namespace Insightful\Scraper;


use Carbon\Carbon;
use Insightful\RetrievedReview;
use Sunra\PhpSimple\HtmlDomParser;

class YelpReviewScraper extends ReviewScraper
{

    const baseUrl = "https://www.yelp.ie/biz/";
    const sourceCode = 1;
    const maximumRating = 5.0;
    const minimumRating = 0.0;
    const reviewsPerPage = 20;

    function __construct(string $slug)
    {
        parent::__construct($slug);
    }

    public function getReviewsAfterDate(\DateTime $date) : array
    {
        $firstPageReviews = self::getReviewsOnPage($this->slug, 0);
        $reviewsInRequiredDateRange = self::removeReviewsOutsideRange($firstPageReviews, $date);

        //If we got all 20 back - there could be more on the next page
        $numReviews = sizeof($reviewsInRequiredDateRange);

        //We're finished if we got all the possible reviews on the first page
        $finished = $numReviews >= $this->totalReviews;
        if ($finished) {
            return $reviewsInRequiredDateRange;
        }

        //Not +1 as we have already done the first page
        $numPagesRemaining = $this->totalReviews % self::reviewsPerPage;

        for ($i = 0; $i <= $numPagesRemaining; $i++) {
            $offset = self::reviewsPerPage + (self::reviewsPerPage * $i);
            $nextPage = $this->getReviewsOnPage($this->slug, $offset);
            $currentReviewsInRequiredDateRange = self::removeReviewsOutsideRange($nextPage, $date);
            $reviewsInRequiredDateRange = array_merge($reviewsInRequiredDateRange, $currentReviewsInRequiredDateRange);
            //We dropped some reviews which means we got up to the required date
            if(sizeof($currentReviewsInRequiredDateRange) < self::reviewsPerPage) break;
        }

        return $reviewsInRequiredDateRange;
    }


    /*
     *  Thankfully Yelp provide all the review data as JSON within the document without needing to
     *   scrape within individual html elements.
     * */
    private function getReviewsOnPage(string $slug, int $offset) : array
    {
        $url = self::baseUrl . $slug . '?sort_by=date_desc&start='.$offset;
        $html = HtmlDomParser::file_get_html($url);
        $reviewJson = $html->find('script[type=application/ld+json]');
        $json = $reviewJson[0]->innertext();
        $object = \json_decode($json);
        $this->totalReviews = $object->aggregateRating->reviewCount;
        $reviews = $object->review;
        $reviewObjects = [];

        foreach ($reviews as $review) {
            $reviewObject = new RetrievedReview();
            $reviewObject->rating = ($review->reviewRating->ratingValue / self::maximumRating) * 100;
            $dateObject = Carbon::createFromFormat("Y-m-d", $review->datePublished)->hour(0)->minute(0)->second(0);
            $reviewObject->date = $dateObject;
            if ($this->latestReview == null) {
                $this->latestReview = $dateObject;
            }
            $reviewObject->text = $review->description;
            $reviewObject->source_id = self::sourceCode;
            $reviewObject->author = $review->author;
            $reviewObject->num_words = self::countWords($review->description);
            $reviewObjects[] = $reviewObject;
        }

        return $reviewObjects;
    }
}