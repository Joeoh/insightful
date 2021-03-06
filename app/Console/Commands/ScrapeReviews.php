<?php

namespace App\Console\Commands;

use App\ReviewScraper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Insightful\Campaign;
use Insightful\Review;
use Insightful\Scraper\TripAdvisorReviewScraper;
use Insightful\Scraper\YelpReviewScraper;


class ScrapeReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape all reviews';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $campaigns = Campaign::all();

        foreach ($campaigns as $campaign) {
            if ($campaign->yelp_slug != "") {
                $yelpScraper = new YelpReviewScraper($campaign->yelp_slug);
                $reviews = $yelpScraper->getReviewsAfterDateForSource($campaign->getDateOfLastReviewStored(YelpReviewScraper::sourceCode));

                foreach ($reviews as $review) {
                    $storeReview = new Review;
                    $storeReview->author = $review->author;
                    $storeReview->date = $review->date;
                    $storeReview->text = $review->text;
                    $storeReview->rating = $review->rating;
                    $storeReview->num_words = $review->num_words;
                    $storeReview->campaign_id = $campaign->id;
                    $storeReview->source_id = $review->source_id;
                    $storeReview->save();
                }
            }

            if ($campaign->tripadvisor_slug != "") {
                $tripadvisorScraper = new TripAdvisorReviewScraper($campaign->tripadvisor_slug);
                $reviews = $tripadvisorScraper->getReviewsAfterDate($campaign->getDateOfLastReviewStoredForSource(TripAdvisorReviewScraper::sourceCode));

                foreach ($reviews as $review) {
                    $storeReview = new Review;
                    $storeReview->author = $review->author;
                    $storeReview->date = $review->date;
                    $storeReview->text = $review->text;
                    $storeReview->rating = $review->rating;
                    $storeReview->num_words = $review->num_words;
                    $storeReview->campaign_id = $campaign->id;
                    $storeReview->source_id = $review->source_id;
                    $storeReview->save();
                }
            }


        }
    }
}
