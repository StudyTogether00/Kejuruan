<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $option_action = "Add,Edit";
    protected $error = "";
    protected $error1 = "";
    public function __construct()
    {
        parent::__construct();
    }

}
