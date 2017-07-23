<?php

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function viewHelp()
    {

        $admin = Sentinel::findById(1);
        $user = Sentinel::getUser();
        Mail::to($user)->send($admin);
        return response()->json(['message' => 'Request completed']);

        if (Sentinel::check()) {
            $layout = "layouts.master-admin";
        } else {
            $layout = "Centaur::layout";
        }
        return view('help', compact('layout'));
    }
}
