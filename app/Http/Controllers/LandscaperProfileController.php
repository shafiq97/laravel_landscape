<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Organization;
use App\Models\Service;
use App\Models\ServiceSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LandscaperProfileController extends Controller
{
    //
    public function index(Request $request): View
    {
        // $this->authorize('viewAny', Service::class);

        $services = Service::filter()
            ->select('services.*', 'locations.name as location_name')
            ->with([
                'bookingOptions' => static fn(HasMany $query) => $query->withCount([
                    'bookings',
                ]),
                'eventSeries',
                'location',
                'organizations',
                'parentEvent',
                'user' => static fn(BelongsTo $query) => $query->select('id', 'first_name'),
                'reviews' => static fn(HasMany $query) => $query->with([
                    'user' => static fn(BelongsTo $query) => $query->select('id', 'first_name', 'phone'),
                ]),
            ])
            ->join('locations', 'services.location_id', '=', 'locations.id')
            ->distinct();


        /** @var ?\App\Models\User $user */
        $user = Auth::user();
        if ($user !== null) {
            $services = $services->where('user_id', $request->user_id);
        }

        $services = $services->paginate();

        // TODO:
        // find contact number by $reurst->user_id
        $contact_number = '';
        $user           = User::find($request->user_id);
        if ($user) {
            $contact_number = $user->phone;
        }

        return view('landscaper_profile.landscaper_profile', $this->formValuesForFilter([
            'services' => $services,
            'services_user_name' => $request->user_name,
            'user_contact_number' => $contact_number
        ]));
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