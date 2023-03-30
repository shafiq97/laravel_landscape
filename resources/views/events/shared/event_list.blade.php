@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $events */
    /** @var ?string $noEventsMessage */
    $showVisibility = $showVisibility ?? true;
@endphp

@if ($events->count() === 0)
    @isset($noEventsMessage)
        <p class="alert alert-danger">
            {{ $noEventsMessage }}
        </p>
    @endisset
@else
    <div class="list-group">
        @foreach ($events as $service)
            @can('view', $service)
                <a href="{{ route('events.show', $service->slug) }}" class="list-group-item list-group-item-action">
                    <strong>{{ $service->name }}</strong>
                    <div>
                        <img src="{{ asset('storage/'.$service->image) }}" width="200" alt="Image">

                    </div>
                    <div>
                        <i class="fa fa-fw fa-clock"></i>
                        @include('events.shared.event_dates')
                    </div>
                    <div>
                        <i class="fa fa-fw fa-location-pin"></i>
                        {{ $service->location->nameOrAddress }}
                    </div>
                    @if ($showVisibility)
                        <div>
                            <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                            <x-badge.visibility :visibility="$service->visibility"/>
                        </div>
                    @endif
                    <div class="text-muted">
                        {{ $service->description }}
                    </div>
                </a>
            @endcan
        @endforeach
    </div>
@endif
