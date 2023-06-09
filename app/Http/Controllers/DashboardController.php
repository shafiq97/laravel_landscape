<?php

namespace App\Http\Controllers;

use App\Exports\BookingsExportSpreadsheet;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Chat;
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
        $user = Auth::user();
        if (!$user) {
            return view('home');
        }

        $services = Service::query()
            ->leftJoin('reviews', 'services.id', '=', 'reviews.service_id')
            ->leftJoin(DB::raw('(SELECT event_id, MIN(price) AS min_price FROM booking_options GROUP BY event_id) AS bo'), 'services.id', '=', 'bo.event_id')
            ->leftJoin('users', 'services.user_id', '=', 'users.id')
            ->where('services.visibility', '=', Visibility::Public ->value)
            ->select('services.*', 'users.first_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price')
            ->groupBy('services.id');

        /** @var ?\App\Models\User $user */
        $user = Auth::user();
        if ($user) {
            if ($user->userRoles()->pluck('name')->contains('Landscaper')) {
                $services = $services->where('services.user_id', $user->id);
            }
        }

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
            // 'form.formFieldGroups.formFields',
        ]);


        $loggedInUserId = auth()->user()->id;

        $bookingsQuery = Booking::filter()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.user_id', $loggedInUserId)
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


    public function landscaper_report(
        Service $service,
        BookingOption $bookingOption,
        BookingFilterRequest $request
    ): StreamedResponse|View {
        $bookingOption->load([
            // 'form.formFieldGroups.formFields',
        ]);


        $loggedInUserId = auth()->user()->id;

        $total_sales = Booking::filter()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.user_id', $loggedInUserId)
            ->with([
                'bookedByUser',
            ])
            ->selectRaw('SUM(price) as total_sales')
            ->first();

        $total_accepted = Booking::filter()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.user_id', $loggedInUserId)
            ->whereNotNull('paid_at')
            ->with([
                'bookedByUser',
            ])
            ->selectRaw('COUNT(*) as total_accepted')
            ->first();

        $total_decline = Booking::filter()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.user_id', $loggedInUserId)
            ->whereNull('paid_at')
            ->with([
                'bookedByUser',
            ])
            ->selectRaw('COUNT(*) as total_decline')
            ->first();


        $services = Service::query()
            ->leftJoin('reviews', 'services.id', '=', 'reviews.service_id')
            ->leftJoin(DB::raw('(SELECT event_id, MIN(price) AS min_price FROM booking_options GROUP BY event_id) AS bo'), 'services.id', '=', 'bo.event_id')
            ->leftJoin('users', 'services.user_id', '=', 'users.id')
            ->leftJoin('users as reviewer', 'reviews.user_id', '=', 'reviewer.id')
            ->where('services.visibility', '=', Visibility::Public ->value)
            ->select('services.*', 'users.first_name', DB::raw('COALESCE(AVG(reviews.rating), 0) as service_rating'), 'bo.min_price', DB::raw('reviewer.first_name as reviewer_first_name'))
            ->whereNotNull('reviewer.first_name')
            ->where('services.user_id', '=', auth()->user()->id)
            ->groupBy('services.id')
            ->get();


        // dd($service->service_rating);
        $total = 0;
        foreach ($services as $service) {
            $total = $total + $service->service_rating;
        }
        $count = $services->count();
        if ($count == 0) {
            $avg_rating = 0;
        } else {
            $avg_rating = $total / $count;
        }

        // dd($avg_rating);

        $chats = Chat::select('chats.*', 'users.first_name')
            ->join('users', 'chats.user_id', '=', 'users.id')
            ->where('chats.landscaper_id', auth()->user()->id)
            ->groupBy('chats.user_id')
            ->get();

        $bookings = Booking::filter()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('services.user_id', $loggedInUserId)
            ->with([
                'bookedByUser',
            ])
            ->get();



        $this->authorize('viewAny', Booking::class);
        return view('dashboard.landscaper_report', [
            'total_decline' => $total_decline,
            'total_accepted' => $total_accepted,
            'total_sales' => $total_sales,
            'avg_rating' => $avg_rating,
            'chats' => $chats,
            'services' => $services,
            'bookings' => $bookings
        ]);
    }
}