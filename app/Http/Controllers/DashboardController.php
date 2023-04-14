<?php

namespace App\Http\Controllers;

use App\Exports\BookingsExportSpreadsheet;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Service;
use App\Models\User;
use App\Options\Visibility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $services = Service::query()
            ->leftJoin('reviews', 'services.id', '=', 'reviews.service_id')
            ->leftJoin(DB::raw('(SELECT event_id, MIN(price) AS min_price FROM booking_options GROUP BY event_id) AS bo'), 'services.id', '=', 'bo.event_id')
            ->leftJoin('users', 'services.user_id', '=', 'users.id')
            ->where('services.visibility', '=', Visibility::Public ->value)
            ->select('services.*', 'users.first_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price')
            ->groupBy('services.id');


        $services->when($request->has('q'), function ($query) use ($request) {
            $q = $request->input('q');
            $query
                // ->join('users', 'services.user_id', '=', 'users.id')
                ->where('users.first_name', 'like', "%$q%")
                ->orWhere('services.name', 'like', "%$q%")
                ->orWhere('services.description', 'like', "%$q%")
                ->select('services.*', 'users.first_name as user_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'));
        })

            ->select('services.*', 'users.first_name as user_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price')
            ->groupBy('services.id');


        $events = $services->get();


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

    public function booking_index(Request $request): View
    {
        // $events = Service::query()
        //     ->where('started_at', '>=', Carbon::now())
        //     ->where('visibility', '=', Visibility::Public->value)
        //     ->orderBy('started_at')
        //     ->limit(10)
        //     ->get();
        $services = Service::query()
            ->leftJoin('reviews', 'services.id', '=', 'reviews.service_id')
            ->leftJoin(DB::raw('(SELECT event_id, MIN(price) AS min_price FROM booking_options GROUP BY event_id) AS bo'), 'services.id', '=', 'bo.event_id')
            ->leftJoin('users', 'services.user_id', '=', 'users.id')
            ->where('services.visibility', '=', Visibility::Public ->value)
            ->select('services.*', 'users.first_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price')
            ->groupBy('services.id');


        $services->when($request->has('q'), function ($query) use ($request) {
            $q = $request->input('q');
            $query
                // ->join('users', 'services.user_id', '=', 'users.id')
                ->where('users.first_name', 'like', "%$q%")
                ->orWhere('services.name', 'like', "%$q%")
                ->orWhere('services.description', 'like', "%$q%")
                ->select('services.*', 'users.first_name as user_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'));
        })

            ->select('services.*', 'users.first_name as user_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price')
            ->groupBy('services.id');


        $events = $services->get();



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

        // dd($bookings);


        return view('dashboard.bookings', [
            'bookings' => $bookings ?? null,
            'service' => $events,
        ]);
    }

    public function landscaper_booking(
        Service $service,
        BookingOption $bookingOption,
        BookingFilterRequest $request
    ): StreamedResponse|View {
        $bookingOption->load([
            'form.formFieldGroups.formFields',
        ]);

        $bookingsQuery = Booking::filter()
            ->with([
                'bookedByUser',
            ]);

        if ($request->query('output') === 'export') {
            $this->authorize('exportAny', Booking::class);

            $fileName = $service->slug . '-' . $bookingOption->slug;
            return $this->streamExcelExport(
                new BookingsExportSpreadsheet($service, $bookingOption, $bookingsQuery->get()),
                str_replace(' ', '-', $fileName) . '.xlsx',
            );
        }

        $this->authorize('viewAny', Booking::class);
        return view('dashboard.landscaper_booking', [
            'service' => $service,
            'bookingOption' => $bookingOption,
            'bookings' => $bookingsQuery->paginate(),
        ]);
    }
}