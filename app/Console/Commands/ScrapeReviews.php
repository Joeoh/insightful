<?php

namespace App\Console\Commands;

use App\ReviewScraper;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        //Get all pages to scrape from the DB
        //Pass them to scraper
        //Insert Results
        //$reviews = TripAdvisorReviewScraper::getReviewsAfterDate("Restaurant_Review-g212087-d10124871-Reviews-Fia-Rathgar_County_Dublin.html", Carbon::now()->subWeeks(3));
        //$nextTenBase = "https://www.tripadvisor.ie/Restaurant_Review-g212087-d10124871-Reviews-or10-Fia-Rathgar_County_Dublin.html#REVIEWS";

        $date = Carbon::now()->subYear(2);
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d 00:00:00')); //ensure date is start of given day

        $yelpScraper = new YelpReviewScraper("queen-of-tarts-dublin-4");
        $reviews = $yelpScraper->getReviewsAfterDate($startDate);
        $numReviewsYelp = sizeof($reviews);
        if($numReviewsYelp == 0){
            echo "There were no reviews since: ".$startDate->toDateString();
            if($yelpScraper->latestReview != null){
                echo "\nThe last review was on: ".$yelpScraper->latestReview->toDateString();
            }
        } else {
            echo "There were ".$numReviewsYelp." reviews on Yelp since ".$startDate->toDateString()."\n";
            echo "Here they are: \n";

            foreach ($reviews as $review){
                echo $review->date." | (".$review->percentage."%)";
                echo $review->userName." Said: \n";
                echo $review->text." \n";
            }

        }
    }
}
