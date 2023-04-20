@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $events /
 /* @var ?string $noEventsMessage */
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
                <div class="row mb-1">
                    <span>{{ $service->name }} by Landscaper
                        <a
                            href="{{ route('landscaper_profile.index', ['user_id' => $service->user_id, 'user_name' => $service->user_name]) }}">{{ $service->user_name }}</a></span>
                </div>
                <a href="{{ route('events.show', $service->slug) }}" class="list-group-item list-group-item-action"
                    style="margin-bottom: 20px;">
                    <div class="row">
                        {{-- <div class="col">
                            <strong>{{ $service->name }}</strong><span> by Landscaper</span>
                            {{ $service->user_name }}
                        </div> --}}
                        <div style="text-align: right" class="col">
                            <div>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $service->service_rating)
                                        <i style="color:#FFD700;" class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="" style="display: flex; gap: 20px;">
                        <div class="left mr-3">
                            <div class="mr-3">
                                <img src="{{ asset('storage/' . $service->image) }}" width="200" alt="Image">
                            </div>
                        </div>

                        <div class="right">
                            <div>
                                <i class="fa fa-fw fa-location-pin"></i>
                                {{ $service->location->nameOrAddress }}
                            </div>
                            @if ($showVisibility)
                                <div>
                                    <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                                    <x-badge.visibility :visibility="$service->visibility" />
                                </div>
                            @endif
                            <div class="text-muted">
                                {{ $service->description }}
                            </div>
                            <div class="text-muted">
                                Price from RM{{ $service->min_price }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- <div>
                    <i class="fa fa-fw fa-clock"></i>
                    @include('events.shared.event_dates')
                    </div> --}}
                    


                    {{-- <a href="{{ route('chats.index', $service->id) }}" class="btn btn-success">{{ __('Chat') }}</a> --}}
                </a>
            @endcan
        @endforeach
    </div>
@endif
