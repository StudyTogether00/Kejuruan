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
        if ($this->checksession($request)) {
            return redirect("/");
        }
        $this->data["title"] = "Login";
        return view("pages.Login", $this->data);
    }

    public function CheckSession(Request $request)
    {
        $token = !empty($request->session()->get("data.token")) ? $request->session()->get("data.token") : "";
        if (empty($token)) {
            return false;
        }
        return true;
    }
}
