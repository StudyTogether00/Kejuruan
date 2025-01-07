<?php

namespace App\Http\Controllers\FE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Dashboard(Request $request)
    {
        $this->data["title"] = "Dashboard";
        return view("pages.dashboard", $this->data);
    }
    public function Login(Request $request)
    {
        $this->data["title"] = "Login";
        return view("pages.Login", $this->data);
    }

}
