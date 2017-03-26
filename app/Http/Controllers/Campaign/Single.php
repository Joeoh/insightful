<?php

namespace App\Http\Controllers\Campaign;

use Carbon\Carbon;
use Insightful\Campaign;


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
        if ($campaign == null) {
            return redirect('dashboard');
        }

        $user = \Auth::user();

        if ($campaign->user_id != $user->id) {
            return redirect('/dashboard');
        }

        $numReviews = $campaign->getNumberOfReviews();

        $now = Carbon::now();

        $keywordsWithSentiment = $campaign->getKeywordsWithSentimentForPeriod(Carbon::now()->subMonth(1), $now);


        $sinceDate = Carbon::now()->subMonth(1)->hour(0)->minute(0)->second(0);

        $averageSentiment = $campaign->getAverageSentimentForPeriod(Carbon::now()->subMonth(1), $now);

        $remainingPercentage = (100 - $averageSentiment);

        $percentages = [
            "averagePercentage"   => round($averageSentiment),
            "remainingPercentage" => round($remainingPercentage),
        ];


        $latestReviews = $campaign->getReviewsForPeriod($sinceDate, $now);


        $weeksData = $campaign->sentimentForPeriod($sinceDate, $now);

        $weeksDataJson = \json_encode($weeksData);

        $startDate =  Carbon::now()->subMonth(1)->hour(0)->minute(0)->second(0);

        $endDate = Carbon::now();

        return view('campaign.index', compact('campaign', 'averageSentiment', 'numReviews',
            'weeksDataJson', 'keywordsWithSentiment', 'latestReviews', 'percentages', 'startDate','endDate'));
    }

    public function insight($id)
    {
        $campaign = Campaign::find($id);
        if ($campaign == null) {
            return redirect('dashboard');
        }

        $user = \Auth::user();

        if ($campaign->user_id != $user->id) {
            return redirect('/dashboard');
        }


        $curStartDate = $campaign->getDateOfFirstReview()->startOfWeek();
        $curEndDate = $curStartDate->copy()->endOfWeek();

        $endDate = $campaign->getDateOfLastReviewStored();

        $allKeywords = $campaign->getKeywords();

        $keywordMap = [];

        //Index for each keyword so that it can be places into the JS array in the same position
        $i = 1;
        foreach ($allKeywords as $keyword){
            $keywordMap[strtolower($keyword->word)] = $i++;
        }

        $chartData = "[";

        while($curStartDate->lt($endDate)){

            $keywordsWithSentiment = $campaign->getKeywordsWithSentimentForPeriod($curStartDate, $curEndDate);
            $midPoint = $curStartDate->copy()->average($curEndDate)->hour(0)->minute(0)->second(0);
            $curEndDate->addWeeks(2);
            $curStartDate->addWeeks(2);
            $line = $this->createChartEntry($midPoint, $keywordMap,$keywordsWithSentiment);
            $chartData .= $line.',';
        }

        //Removing trailing ,
        $chartData = rtrim($chartData,',');
        $chartData .= ']';
        $columns = $this->createChartColumns($allKeywords);

        return view('campaign.insight', compact('campaign','chartData', 'columns'));
    }


    private function createChartColumns($allKeywords){
        $string = "[['date','Date'],";
        foreach ($allKeywords as $keyword){
            $string .= "['number','".$keyword->word."'],";
        }

        //Removing trailing ,
        $chartData = rtrim($string,',');
        $chartData .=']';

        return $chartData;
    }


    //Takes an array of keywords and aligns them correctly in a JS entry for the chart
    private function createChartEntry($date,$allKeywords, $currentKeywords){
        $line = array(sizeof($allKeywords));
        $string = "";
        $line = array_fill(0, sizeof($allKeywords) +1,null);
        $line[0] = $date;

        foreach ($currentKeywords as $keyword){
            $wordLower = strtolower($keyword->word);
            if(isset($allKeywords[$wordLower])) {
                $index = $allKeywords[strtolower($keyword->word)];
                $line[$index] = $keyword->average_sentiment;
            }
        }

        $string .= '[new Date('.$date->year.', '.($date->month -1).', '.$date->day.'),';
        for ($i = 1; $i < sizeof($line); $i++){
            $cur = $line[$i];
            if($cur == "" || $cur == null) {
                $cur = "null";
            }
            $string .= $cur.',';
        }
        //Remove the trailing ,
        $string = rtrim($string,',');
        $string .= ']';
        return $string;

    }

}
