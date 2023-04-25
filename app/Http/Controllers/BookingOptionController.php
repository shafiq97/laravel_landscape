<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingOptionRequest;
use App\Models\BookingOption;
use App\Models\Service;
use App\Models\Form;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class BookingOptionController extends Controller
{
    public function show(Service $service, BookingOption $bookingOption): View
    {
        $this->authorize('view', $bookingOption);
        $serviceWithUser = Service::select('services.*', 'users.first_name', 'users.email', 'users.phone')
        ->leftJoin('users', 'users.id', '=', 'services.user_id')
        ->with([
            'bookingOptions' => static fn(HasMany $query) => $query->withCount(['bookings']),
            'subEvents.location',
        ])
        ->findOrFail($service->id);
        return view('booking_options.booking_option_show', [
            'service' => $serviceWithUser,
            'bookingOption' => $bookingOption,
        ]);
    }

    public function create(Service $service): View
    {
        $this->authorize('create', BookingOption::class);

        return view('booking_options.booking_option_form', [
            'service' => $service,
        ]);
    }


    public function store(Service $service, BookingOptionRequest $request): RedirectResponse
    {
        $this->authorize('create', BookingOption::class);

        $bookingOption = new BookingOption();
        $bookingOption->event()->associate($service);
        if ($bookingOption->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('booking-options.edit', [$service, $bookingOption]));
        }

        return back();
    }

    public function edit(Service $service, BookingOption $bookingOption): View
    {
        $this->authorize('update', $bookingOption);

        return view('booking_options.booking_option_form', [
            'bookingOption' => $bookingOption,
            'service' => $service,
        ]);
    }


    public function update(Service $service, BookingOption $bookingOption, BookingOptionRequest $request): RedirectResponse
    {
        $this->authorize('update', $bookingOption);

        if ($bookingOption->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            // Slug may have changed, so we need to generate the URL here!
            return redirect(route('booking-options.edit', [$service, $bookingOption]));
        }

        return back();
    }

    // private function formValues(array $values = []): array
    // {
    //     return array_replace([
    //         'forms' => Form::query()
    //             ->orderBy('name')
    //             ->get(),
    //     ], $values);
    // }
}