<?php

namespace App\Modules\Marketplace\Controllers;

use Illuminate\Routing\Controller;

class FreelancerListingController extends Controller
{
    public function index()
    {
        return view('freelancer.index');
    }
}
