<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use App\Options\Visibility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // $events = Service::query()
        //     ->where('started_at', '>=', Carbon::now())
        //     ->where('visibility', '=', Visibility::Public->value)
        //     ->orderBy('started_at')
        //     ->limit(10)
        //     ->get();
        $services = Service::query();

        if ($request->has('q')) {
            $q = $request->input('q');
            $services->where('name', 'like', "%$q%")
                ->orWhere('description', 'like', "%$q%");
                $events = $services->get();
        }else{
            $events = Service::query()
            ->where('visibility', '=', Visibility::Public ->value)
            ->get();
        }


        /** @var ?User $user */
        $user = Auth::user();
        if (isset($user)) {
            $bookings = $user->bookings()
                ->with([
                    'bookingOption.event',
                ])
                ->orderByDesc('booked_at')
                ->limit(10)
                ->get();
        }

        return view('dashboard.dashboard', [
            'bookings' => $bookings ?? null,
            'events' => $events,
        ]);
    }
}