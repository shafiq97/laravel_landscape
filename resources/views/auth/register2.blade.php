@extends('layouts.app')

@section('title')
    {{ __('Register') }}
@endsection

@section('main')
    <x-card.centered>
        <div style="text-align: center; margin-bottom: 5%;">
            <h1>Want to advertise your landsaping services?</h1>
            <h1>Register an account now!</h1>
        </div>
        <div class="alert alert-primary mb-3">
            <a class="alert-link" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>
        <div class="alert alert-primary mb-3">
            {{ 'Not a landscaper?' }}
            <a class="alert-link" href="{{ route('register') }}">
                {{ __(' REGISTER HERE!') }}
            </a>
        </div>

        <x-form method="POST" action="{{ route('register') }}">
            <x-form.row>
                <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                <x-form.input name="first_name" type="text" required autofocus pattern="[A-Za-z ]+" />
                {{-- <small style="color: red">Use letters only</small> --}}
            </x-form.row>
            <x-form.row>
                <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                <x-form.input name="last_name" type="text" required pattern="[A-Za-z ]+" />
                {{-- <small style="color: red">Use letters only</small> --}}
            </x-form.row>
            <x-form.row>
                <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                <x-form.input name="email" type="email" required  />
            </x-form.row>
            <x-form.row>
                <x-form.label for="password">{{ __('Password') }}</x-form.label>
                <x-form.input name="password" type="password" required autocomplete="current-password" />
            </x-form.row>
            <x-form.row>
                <x-form.label for="password_confirmation">{{ __('Confirm password') }}</x-form.label>
                <x-form.input name="password_confirmation" type="password" required />
            </x-form.row>
            <x-form.row>
                <x-form.input name="isLandscaper" value="true" type="hidden">{{ __('Register as Landscaper') }}
                </x-form.input>
            </x-form.row>
            <x-form.row>
                <x-form.input name="remember" type="checkbox">{{ __('Remember me') }}</x-form.input>
            </x-form.row>
            @php
                $termsAndConditions = config('app.urls.terms_and_conditions');
            @endphp
            @if ($termsAndConditions)
                <x-form.row>
                    <x-form.input name="terms_and_conditions" type="checkbox">
                        {!! __('With my registration I accept the :linkStart general terms and conditions:linkEnd.', [
                            'linkStart' => '<a class="alert-link" href="' . $termsAndConditions . '" target="_blank">',
                            'linkEnd' => '</a>',
                        ]) !!}
                    </x-form.input>
                </x-form.row>
            @endif

            <x-form.button class="w-100">{{ __('Register') }}</x-form.button>
        </x-form>
    </x-card.centered>
@endsection
