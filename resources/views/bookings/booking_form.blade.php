@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $service = $bookingOption->event->with('user')->first();
@endphp

@section('title')
    {{ __('Edit booking no. :id', [
        'id' => $booking->id,
    ]) }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $service) }}">{{ $service->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('booking-options.show', [$service, $bookingOption]) }}">{{ $bookingOption->name }}
    </x-nav.breadcrumb>
    @can('viewAny', \App\Models\Booking::class)
        <x-nav.breadcrumb href="{{ route('bookings.index', [$service, $bookingOption]) }}">{{ __('Bookings') }}
        </x-nav.breadcrumb>
    @endcan
@endsection

@section('headline')
    <h1>{{ $service->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            {{-- {{ dd($service) }} --}}
            {{-- @include('events.shared.event_details') --}}
            @php
                /** @var \App\Models\Service $service */
            @endphp

            <x-list.group>
                @isset($service->description)
                    <li class="list-group-item">
                        {{ $service->description }}
                    </li>
                @endisset
                {{-- @isset($service->website_url)
        <li class="list-group-item">
            <a href="{{ $service->website_url }}" target="_blank">{{ __('Website') }}</a>
        </li>
    @endisset --}}
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                    </span>
                    <div>
                        <x-badge.visibility :visibility="$service->visibility" />
                    </div>
                </li>
                {{-- <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-clock" title="{{ __('Date') }}"></i>
                    </span>
        <div>@include('events.shared.event_dates')</div>
    </li> --}}
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i>
                    </span>
                    <div>
                        @foreach ($service->location->fullAddressBlock as $line)
                            {{ $line }}@if (!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-sitemap" title="{{ __('Organizations') }}"></i>
                    </span>
                    <div>
                        @if ($service->organizations->count() === 0)
                            {{ __('none') }}
                        @else
                            <ul class="list-unstyled">
                                @foreach ($service->organizations as $organization)
                                    <li>{{ $organization->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-user" title="{{ __('Landscaper') }}"></i>
                    </span>
                    <div>
                        {{-- {{ dd($service) }} --}}
                        @if ($service->user_id)
                            {{ $service->user->first_name }}
                        @endif
                    </div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-phone" title="{{ __('Contact') }}"></i>
                    </span>
                    <div>
                        {{-- {{ dd($service) }} --}}

                        @if ($service->user_id)
                            {{ $service->user->phone }}
                        @endif
                    </div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-envelope" title="{{ __('Email') }}"></i>
                    </span>
                    <div>
                        {{-- {{ dd($service) }} --}}
                        @if ($service->user_id)
                            {{ $service->user->email }}
                        @endif
                    </div>
                </li>
            </x-list.group>
        </div>
        <div class="col-12 col-md-8">
            @include('bookings.shared.booking_details')

            <x-form method="PUT" action="{{ route('bookings.update', $booking) }}">
                @canany(['updateBookingComment', 'updatePaymentStatus'], $booking)
                    <div class="row">
                        @can('updateBookingComment', $booking)
                            <div class="col-12 col-md-6">
                                <x-form.row>
                                    <x-form.label for="comment">{{ __('Comment') }}</x-form.label>
                                    <x-form.input name="comment" type="textarea" :value="$booking->comment ?? null" />
                                </x-form.row>
                            </div>
                        @endcan
                        @can('updatePaymentStatus', $booking)
                            <div class="col-12 col-md-6">
                                <x-form.row>
                                    <x-form.label for="paid_at">{{ __('Paid at') }}</x-form.label>
                                    <x-form.input name="paid_at" type="datetime-local" :value="$booking->paid_at ?? null" />
                                </x-form.row>
                            </div>
                        @endcan
                    </div>
                @endcanany

                @include('bookings.booking_form_fields', [
                    'booking' => $booking,
                    'bookingOption' => $bookingOption,
                ])

                <x-button.group>
                    <x-button.save>
                        @isset($booking)
                            {{ __('Save') }}
                        @else
                            {{ __('Create') }}
                        @endisset
                    </x-button.save>
                    <x-button.cancel href="{{ route('bookings.index', [$service, $bookingOption]) }}" />
                </x-button.group>
            </x-form>
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null" />
@endsection
