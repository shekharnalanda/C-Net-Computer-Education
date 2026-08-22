<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ProductionAuditService;

class ProductionAuditController extends Controller
{
    public function index()
    {
        return view('admin.audit.index',['audit'=>ProductionAuditService::run()]);
    }

    public function json()
    {
        return response()->json(ProductionAuditService::run())->header('Cache-Control','no-store, private');
    }
}
