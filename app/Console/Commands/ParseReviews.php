<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Insightful\Parser\ReviewParser;
use Insightful\RetrievedReview;
use Insightful\Review;

class ParseReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse stored reviews, send to Cognitive Services API';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $review_texts[] = "From the awesome greeting by Drew, to the wine and cheese happy hour, nice accommodations and coffee in the morning, we couldn't have been happier with our stay. It was very convenient to walk to MSG and Times Square with so many restaurants and bars to choose from. The courtyard was kind of packed up for the winter but we still enjoyed a cocktail during a unseasonably warm weekend for NYC.";
        $review_texts[] = 'Me and my wife wanted to plan a special weekend in NYC. We usually stay at Ritz in Central Park or Battery Park being the service is always over the top. When searching TripAdvisor we found some Great Reviews of the 414 Hotel. So I took the chance and decided to investigate further. Well let me tell you the experience of this property was OVER THE TOP AND THEN SOME. I already knew from the initial phone call and chat room experience that we were making the right choice. They were extremely accommodating providing restaurants to dine and things to do and all of this was done in an e-mail immediately after making the reservation.
						Once we arrived the hospitality was Exceptional to say the least it was like being greeted by family and they also knew it was my wife\'s Birthday and had a special surprise in the room that would have cost over $100 anywhere else. The morning Breakfast was nice and the Happy Hour time with Fireplace and interaction with other guest was also enjoyable.
						This location is hidden and a must stay for those that want service with a smile and then some. We liked it so much that we decided that whenever we stay in the city in the future we will call 414 Hotel our home as that is how they made us feel during the entire visit. Great Staff, Clean Rooms and Great Location.';

        $review1 = new RetrievedReview();
        $review2 = new RetrievedReview();
        $review1->text = $review_texts[0];
        $review2->text = $review_texts[1];
        $review1->id = 1;
        $review2->id = 2;

        $reviewObjects = [
            $review1,
            $review2
        ];

        /*
         *  Get all reviews as Eloquent model - where not parsed
         *  Use same result object to update parsed to true
         * */



        $res = ReviewParser::parseReviews($reviewObjects);

        print_r($res);
    }
}
