<?php

namespace App\Http\Controllers\Campaign;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Insightful\Campaign;
use Insightful\Keyword;
use Insightful\Review;

class Single extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('campaign.new');
    }


    public function view($id)
    {
        $campaign = Campaign::find($id);

        $user = \Auth::user();

        if ($campaign->user_id != $user->id){
            return redirect('/dashboard');
        }

        $numReviews = $campaign->getNumberOfReviews();


        $popularKeywords = $campaign->getKeywords();


        $sinceDate = Carbon::now()->subWeeks(2)->hour(0)->minute(0)->second(0);

        $averageSentiment = $campaign->getAverageSentimentForPeriod(Carbon::now()->subYear(5), Carbon::now());
        $sentimentLastTwoWeeks = $campaign->getAverageSentimentForPeriod($sinceDate, Carbon::now());
        $lastReview = $campaign->getDateOfLastReviewStored();
        $weeksSinceLastReview = $lastReview->diffInWeeks(Carbon::now());

        $lastFiveWeeks = $campaign->sentimentForPreviousWeeks(20 + $weeksSinceLastReview);
        echo "<pre>";
        print_r($lastFiveWeeks);
        echo "</pre>";

        return view('campaign.landing', compact('campaign','popularKeywords','averageSentiment','sentimentLastTwoWeeks','numReviews'));
    }


}
