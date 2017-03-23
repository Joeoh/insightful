<?php

namespace App\Http\Controllers\Campaign;


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
            'yelp_slug'  => 'required',
        ]);

        $user = \Auth::user();

        $businessName = $request->input('business_name');
        $yelpSlug = $request->input('yelp_slug');

        $campaign = new Campaign;
        $campaign->business_name = $businessName;
        $campaign->yelp_slug = $yelpSlug;
        $campaign->user_id = $user->id;
        $campaign->save();


        //TODO: Make this methods concurrent
        Artisan::call('scrape');
        Artisan::call('parse');

        return redirect('/dashboard/');


    }
}
