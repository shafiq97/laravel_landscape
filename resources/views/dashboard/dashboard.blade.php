@extends('layouts.app')

@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $events */
@endphp

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="q" placeholder="{{ __('Search services...') }}">
                    <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <h2>{{ __('Landscape Service List') }}</h2>
            @include('events.shared.event_list', [
                'events' => $events,
                'showVisibility' => false,
            ])
        </div>
        @if (Auth::user() &&
                null !== Auth::user()->userRoles &&
                Auth::user()->userRoles[0]->name == 'User' &&
                $bookings !== null)
            <div class="col-12 col-md-6">
                <h2>{{ __('My bookings') }}</h2>
                @foreach ($bookings as $booking)
                    @php
                        $service = $booking->bookingOption->event;
                    @endphp
                    <div class="list-group">
                        <a href="{{ route('bookings.show', $booking) }}" class="list-group-item list-group-item-action">
                            <strong>{{ $service->name }}</strong>
                            <div>
                                <i class="fa fa-fw fa-clock"></i>
                                @include('events.shared.event_dates')
                            </div>
                            <div>
                                <i class="fa fa-fw fa-location-pin"></i>
                                {{ $service->location->nameOrAddress }}
                            </div>
                            <div>
                                <i class="fa fa-fw fa-user-alt"></i>
                                {{ $booking->first_name }} {{ $booking->last_name }}
                            </div>
                            <div>
                                @isset($booking->price)
                                    <span class="badge bg-primary">{{ formatDecimal($booking->price) }}&nbsp;</span>
                                    @isset($booking->paid_at)
                                        <span class="badge bg-primary">{{ __('paid') }}
                                            ({{ $booking->paid_at->isMidnight() ? formatDate($booking->paid_at) : formatDateTime($booking->paid_at) }})
                                        </span>
                                    @else
                                        <span class="badge bg-danger">{{ __('not paid yet') }}</span>
                                    @endisset
                                @else
                                    <span class="badge bg-primary">{{ __('free of charge') }}</span>
                                @endisset
                                @isset($booking->booked_at)
                                    <span class="badge bg-primary">{{ formatDateTime($booking->booked_at) }}</span>
                                @endisset
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
