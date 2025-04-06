<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use DB;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::whereHas('tickets')
            ->withCount('tickets')
            ->with(['tickets' => function($query) {
                $query->select('id', 'title', 'status_id', 'updated_at');
            }])
            ->get();

        return view('statuses.index', compact('statuses'));
    }
}
