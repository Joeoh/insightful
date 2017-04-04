<?php

namespace App\Http\Controllers\Campaign;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Insightful\Campaign;

class ApiController extends \App\Http\Controllers\Controller
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


    public function reviewsWithKeyword($campaignId,$aroundDate, $keyword)
    {
        $campaign = Campaign::find($campaignId);
        if ($campaign == null){
            return ["error" => "not_authorized"];
        }

        $user = \Auth::user();

        if ($campaign->user_id != $user->id){
            return ["error" => "not_authorized"];
        }

        $midWeek = Carbon::parse($aroundDate);

        $start = $midWeek->copy()->startOfWeek();
        $end = $midWeek->copy()->endOfWeek();

        return $campaign->getReviewsWithKeywordInPeriod($keyword, $start, $end);

        
    }
}
