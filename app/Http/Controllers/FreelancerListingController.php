<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FreelancerListingController extends Controller
{
    public function index()
    {
        return view('freelancer.index');
    }
}
