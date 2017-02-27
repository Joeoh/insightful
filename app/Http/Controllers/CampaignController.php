<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class CampaignController extends Controller
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
        $user = \Auth::user();

        $campaigns = $user->campaigns;
        $numCampaigns = sizeof($campaigns);


        if ($numCampaigns > 1) {
            return view('campaign.list')->with('campaigns', $campaigns);
        } else {
            if ($numCampaigns == 1) {
                $id = $campaigns[0]->id;

                return redirect('/campaign/' . $id);
            } else {
                return redirect('create');
            }
        }
    }
}
