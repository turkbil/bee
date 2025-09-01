<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModalTestController extends Controller
{
    /**
     * Test sayfaları için controller
     * Page manage ile birebir aynı layout kullanır
     */
    
    public function test1()
    {
        return view('admin.modal-tests.page-test1', [
            'pageTitle' => 'Modal Test 1 - Modern Premium (Page Layout)',
            'testId' => 1,
            'testName' => 'Modern Premium Design - Real Page Layout'
        ]);
    }
    
    public function test2()
    {
        return view('admin.modal-tests.page-test2', [
            'pageTitle' => 'Modal Test 2 - Interactive Component System (Page Layout)',
            'testId' => 2,
            'testName' => 'Interactive Component System - Real Page Layout'
        ]);
    }
    
    public function test3()
    {
        return view('admin.modal-tests.page-test3', [
            'pageTitle' => 'Modal Test 3 - Analytics Dashboard (Page Layout)',
            'testId' => 3,
            'testName' => 'Analytics Dashboard - Real Page Layout'
        ]);
    }
    
    public function test4()
    {
        return view('admin.modal-tests.page-test4', [
            'pageTitle' => 'Modal Test 4 - Integration & Automation (Page Layout)',
            'testId' => 4,
            'testName' => 'Integration & Automation - Real Page Layout'
        ]);
    }
    
    public function test5()
    {
        return view('admin.modal-tests.page-test5', [
            'pageTitle' => 'Modal Test 5 - Hybrid Approach (Page Layout)',
            'testId' => 5,
            'testName' => 'Hybrid Approach - Real Page Layout'
        ]);
    }
}