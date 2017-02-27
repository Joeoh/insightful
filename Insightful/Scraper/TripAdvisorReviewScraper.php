<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 05/02/2017
 * Time: 18:18
 */

namespace Insightful\Scraper;

use Insightful\Review;
use simplehtmldom_1_5\simple_html_dom;
use Sunra\PhpSimple\HtmlDomParser;


class TripAdvisorReviewScraper extends ReviewScraper
{
    const baseUrl = "https://www.tripadvisor.ie/";

    function __construct($slug)
    {
        parent::__construct($slug);
    }

    public function getReviewsAfterDate(\DateTime $date) : array
    {
        $url = self::baseUrl . $this->slug;

        //Do first call here as we need to know how many pages to get next
        $html = HtmlDomParser::file_get_html($url);
        $totalReviewsAvailable = self::getNumberOfAvailableReviews($html);

        $reviews = [];
        $reviewElements = $html->find('.reviewSelector');

        foreach ($reviewElements as $reviewElement) {
            $review = new Review();
            $reviewText = ($reviewElement->find(".entry > p")[0]->text());
            echo $reviewText;
            $review->text = htmlspecialchars_decode($reviewText,ENT_QUOTES);
            //var_dump($element->find(".ratingDate"));

        }
        return [];
        $nextUrls = self::generateUrlsForNextPages($url, $totalReviewsAvailable);

        foreach ($nextUrls as $nextUrl) {
            foreach (self::getReviewsAtUrl($nextUrl) as $review){
                $reviews[] = $review;
            }
        }

        //print_r($reviews);

        return [];
    }

    private static function getReviewsAtUrl($url) : array
    {
        $reviews = [];
        $html = HtmlDomParser::file_get_html($url);
        foreach ($html->find('.reviewSelector > .entry') as $element) {
            $reviews[] = htmlspecialchars_decode($element->text(),ENT_QUOTES);
        }

        return $reviews;
    }


    private static function getNumberOfAvailableReviews(simple_html_dom $html) : int
    {
        foreach ($html->find('a[href="#REVIEWS"]') as $element) {
            return (int)$element->content;
        }
    }

    private static function generateUrlsForNextPages(string $url, int $numberOfReviews) : array
    {
        $numberOfPagesRemaining = ($numberOfReviews % 10);
        $offsetLocation = strpos($url, "-Reviews-") + 9;
        $urls = [];
        for ($i = 1; $i <= $numberOfPagesRemaining; $i++) {
            $offsetString = "or" . ($i * 10) . "-";
            $newstr = substr_replace($url, $offsetString, $offsetLocation, 0);
            $urls[] = $newstr;
        }

        return $urls;
    }
}