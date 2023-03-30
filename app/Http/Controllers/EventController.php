<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Models\Service;
use App\Models\ServiceSeries;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(EventFilterRequest $request): View
    {
        $this->authorize('viewAny', Service::class);

        return view('events.event_index', $this->formValuesForFilter([
            'services' => Service::filter()
                ->with([
                    'bookingOptions' => static fn(HasMany $query) => $query->withCount([
                        'bookings',
                    ]),
                    'eventSeries',
                    'location',
                    'organizations',
                    'parentEvent',
                ])
                ->paginate(),

        ]));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        if ($service->delete()) {
            Session::flash('success', __('Deleted successfully.'));
        }

        return redirect()->route('events.index');
    }


    public function show(Service $service): View
    {
        $this->authorize('view', $service);

        return view('events.event_show', [
            'service' => $service->loadMissing([
                'bookingOptions' => static fn(HasMany $query) => $query->withCount([
                    'bookings',
                ]),
                'subEvents.location',
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        return view('events.event_form', $this->formValues());
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $this->authorize('create', Service::class);

        if ($request->hasFile('image') && !$request->file('image')->isValid()) {
            return back()->withErrors(['image' => __('Failed to upload image.')]);
        }

        $service = new Service();
        if ($service->fillAndSave($request->validated())) {
            Session::flash('success', __('Created successfully.'));
            return redirect(route('events.edit', $service));
        }

        return back();
    }


    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('events.event_form', $this->formValues([
            'service' => $service,
        ]));
    }

    public function update(Service $service, EventRequest $request): RedirectResponse
    {
        $this->authorize('update', $service);

        if ($service->fillAndSave($request->validated())) {
            Session::flash('success', __('Saved successfully.'));
            // Slug may have changed, so we need to generate the URL here!
            return redirect(route('events.edit', $service));
        }

        return back();
    }

    private function formValues(array $values = []): array
    {
        return array_replace([
            'services' => Service::query()
                ->whereNull('parent_event_id')
                ->orderBy('name')
                ->get(),
        ], $this->formValuesForFilter($values));
    }

    private function formValuesForFilter(array $values = []): array
    {
        return array_replace([
            'eventSeries' => ServiceSeries::query()
                ->orderBy('name')
                ->get(),
            'locations' => Location::query()
                ->orderBy('name')
                ->get(),
            'organizations' => Organization::query()
                ->orderBy('name')
                ->get(),
        ], $values);
    }
}