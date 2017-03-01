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
        if ($campaign == null){
            return redirect('dashboard');
        }

        $user = \Auth::user();

        if ($campaign->user_id != $user->id){
            return redirect('/dashboard');
        }

        $numReviews = $campaign->getNumberOfReviews();

        $keywordsWithSentiment = $campaign->getKeywordsWithSentimentForPeriod(Carbon::now()->subYear(5), Carbon::now());


        $sinceDate = Carbon::now()->subWeeks(2)->hour(0)->minute(0)->second(0);

        $averageSentiment = $campaign->getAverageSentimentForPeriod(Carbon::now()->subYear(5), Carbon::now());

        $remainingPercentage = (100 - $averageSentiment);

        $percentages = [
          "averagePercentage" =>   $averageSentiment,
          "remainingPercentage" =>   $remainingPercentage,
        ];

        $sentimentLastTwoWeeks = $campaign->getAverageSentimentForPeriod($sinceDate, Carbon::now());

        $lastReview = $campaign->getDateOfLastReviewStored();
        $weeksSinceLastReview = $lastReview->diffInWeeks(Carbon::now());

        $latestReviews = $campaign->getLastReviews(3);


        $weeksData = $campaign->sentimentForPreviousWeeks(40 + $weeksSinceLastReview);

        $weeksDataJson = \json_encode($weeksData);

        return view('campaign.index', compact('campaign','averageSentiment','sentimentLastTwoWeeks','numReviews',
                                                'weeksDataJson', 'keywordsWithSentiment','latestReviews','percentages'));
    }


}
