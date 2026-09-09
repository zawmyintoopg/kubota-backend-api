<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Langcontroller extends Controller
{
     public function switch($lang)
    {
        //dd('heelo');
        if (in_array($lang, ['en','mm'])) {
            session(['locale'=>$lang]);
        }
        //return redirect()->back();
    }
}
