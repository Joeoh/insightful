<?php

namespace App\Http\Controllers\Campaign;


use App\Jobs\ScrapeAndParse;
use App\Jobs\ScrapeTripAdvisor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Insightful\Campaign;

class NewCampaign extends \App\Http\Controllers\Controller
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

    public function store(Request $request)
    {
        $this->validate($request, [
            'business_name' => 'required',
            'yelp_slug'  => 'required_without_all:tripadvisor_slug',
            'tripadvisor_slug'  => 'required_without_all:yelp_slug',
        ]);

        $user = \Auth::user();

        $businessName = $request->input('business_name');
        $yelpSlug = $request->input('yelp_slug');
        $tripadvisor_slug = $request->input('tripadvisor_slug');

        $campaign = new Campaign;
        $campaign->business_name = $businessName;
        $campaign->yelp_slug = $yelpSlug;
        $campaign->tripadvisor_slug = $tripadvisor_slug;
        $campaign->user_id = $user->id;
        $campaign->save();

        $this->dispatch(new ScrapeAndParse($campaign));

        //Create jobs to get previous months tripadvisor data two months at a time
        $tenYearsAgo = Carbon::now()->subYears(10);
        $curStartDate = Carbon::now()->subMonth(3);
        $curEndDate = Carbon::now()->subMonth(1);

        while($curEndDate->gt($tenYearsAgo)){
            $this->dispatch(new ScrapeTripAdvisor($campaign, $curStartDate, $curEndDate));
            $curStartDate->subMonths(2);
            $curEndDate->subMonths(2);
        }


        return redirect('/dashboard/');


    }
}
