<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function user() {
        $page_title = 'User';
        $page_description = 'This is user page';
        return view('user.index', compact('page_title', 'page_description'));
    }
    
    public function userSchedule() {
        $page_title = 'User Schedule';
        $page_description = 'This is user schedule page';
        return view('user.schedule.index', compact('page_title', 'page_description'));
    }

    public function streaming() {
        $page_title = 'Streaming';
        $page_description = 'This is streaming page';
        return view('streaming.index', compact('page_title', 'page_description'));
    }

    public function drone() {
        $page_title = 'Drone';
        $page_description = 'This is drone page';
        return view('drone.index', compact('page_title', 'page_description'));
    }
    
    public function droneSchedule() {
        $page_title = 'Drone Schedule';
        $page_description = 'This is drone schedule page';
        return view('drone.schedule.index', compact('page_title', 'page_description'));
    }

    public function incident() {
        $page_title = 'Incident';
        $page_description = 'This is incident page';
        return view('incident.index', compact('page_title', 'page_description'));
    }

    public function image() {
        $page_title = 'Image';
        $page_description = 'This is image page';
        return view('image.index', compact('page_title', 'page_description'));
    }

    public function video() {
        $page_title = 'Video';
        $page_description = 'This is video page';
        return view('video.index', compact('page_title', 'page_description'));
    }

    public function statistic() {
        $page_title = 'Statistic';
        $page_description = 'Statistic page';
        return view('statistic.index', compact('page_title', 'page_description'));
    }

    public function area() {
        $page_title = 'Area';
        $page_description = 'Area page';
        return view('area.index', compact('page_title', 'page_description'));
    }

    public function path() {
        $page_title = 'Path';
        $page_description = 'Path page';
        return view('area.path.index', compact('page_title', 'page_description'));
    }
}
