<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AIModalTestController extends Controller
{
    public function index()
    {
        return view('admin.ai-modal-test.index');
    }
}