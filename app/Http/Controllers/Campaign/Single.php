<?php

namespace App\Http\Controllers\Campaign;


use Illuminate\Http\Request;
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

        $user = \Auth::user();

        if ($campaign->user_id != $user->id){
            return redirect('/dashboard');
        }

        return view('campaign.landing', compact('campaign'));
    }
}
