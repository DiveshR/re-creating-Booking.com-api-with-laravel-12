<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('bookings-manage');

        return response()->json(['success' => true]);
    }
}
