<?php

namespace App\Http\Controllers;

use App\Events\BookingCompleted;
use App\Exports\BookingsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    use StreamsExport;

    public function index(
        Service $service,
        BookingOption $bookingOption,
        BookingFilterRequest $request
    ): StreamedResponse|View {
        $bookingsQuery = Booking::filter($bookingOption->bookings())
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

        // $serviceWithUser = Service::select('services.*', 'users.first_name', 'users.email', 'users.phone')
        //     ->leftJoin('users', 'users.id', '=', 'services.user_id')
        //     ->with([
        //         'bookingOptions' => static fn(HasMany $query) => $query->withCount(['bookings']),
        //         'subEvents.location',
        //     ])
        //     ->findOrFail($service->id);

        $this->authorize('viewAny', Booking::class);

        return view('bookings.booking_index', [
            'service' => $service,
            'bookingOption' => $bookingOption,
            'bookings' => $bookingsQuery->paginate(),
        ]);
    }


    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        
        return view('bookings.booking_show', [
            'booking' => $booking,
        ]);
    }


    public function store(Service $service, BookingOption $bookingOption, BookingRequest $request): RedirectResponse
    {
        // $this->authorize('book', $bookingOption);

        $booking = new Booking();
        $booking->bookingOption()->associate($bookingOption);
        $days = $request->input('number_of_days');
        // dd($day);
        $booking->price        = $bookingOption->price * $days;
        $booking->booking_days = $days;
        $booking->bookedByUser()->associate(Auth::user());
        $booking->booked_at    = Carbon::now();
        $booking->booking_date = $request->input('booking_date');
        $booking->service_id   = $service->id;


        if ($bookingOption->hasReachedMaximumBookings()) {
            $message = __('The maximum booking has been reached.');
            Session::flash('error', $message);
        } elseif ($booking->fillAndSave($request->validated())) {
            $message = __('Your booking has been saved successfully.')
                . ' ' . __('We will send you a confirmation by e-mail shortly.');
            Session::flash('success', $message);

            event(new BookingCompleted($booking));

            if (Auth::user()?->can('update', $booking)) {
                return redirect(route('bookings.edit', $booking));
            }

            if (Auth::user()?->can('view', $booking)) {
                return redirect(route('bookings.show', $booking));
            }
        }

        return back();
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);

        return view('bookings.booking_form', [
            'booking' => $booking,
        ]);
    }


    public function update(Booking $booking, BookingRequest $request): RedirectResponse
    {
        $this->authorize('update', $booking);

        if ($booking->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            return redirect(route('bookings.edit', $booking));
        }

        return back();
    }
}