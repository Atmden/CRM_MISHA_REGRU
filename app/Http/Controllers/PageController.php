<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class PageController extends Controller
{


    public function home()
    {
        return view('vue.auth');
    }
    public function login()
    {
        return view('vue.auth');
    }
}
