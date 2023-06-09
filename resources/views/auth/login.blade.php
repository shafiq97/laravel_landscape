@extends('layouts.app')
<style>
    .login-page {
        background-image: url('https://t3.ftcdn.net/jpg/03/55/60/70/360_F_355607062_zYMS8jaz4SfoykpWz5oViRVKL32IabTP.jpg');
        background-size: cover;
        background-position: center;
    }
</style>
@section('title')
    {{ __('Landscape Service Application') }}
@endsection

@section('main')
    <div class="">

        <x-card.centered>
            <div style="text-align: center" class="pb-5">
                    <h1>Login to your account!</h1>
            </div>
            @if (config('app.features.registration'))
                <div class="alert alert-primary mb-3">
                    {{ __('No account?') }}
                    <a class="alert-link" href="{{ route('register') }}">
                        {{ __(' REGISTER HERE!') }}
                    </a>
                </div>
            @endif

            <x-form method="POST" action="{{ route('login') }}">
                <x-form.row>
                    <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                    <x-form.input name="email" type="email" required autofocus />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="password">{{ __('Password') }}</x-form.label>
                    <x-form.input name="password" type="password" required autocomplete="current-password" />
                </x-form.row>
                <x-form.row>
                    <x-form.input name="remember" type="checkbox">{{ __('Remember me') }}</x-form.input>
                </x-form.row>

                <x-form.button class="w-100">{{ __('Login') }}</x-form.button>

                @if (\Illuminate\Support\Facades\Route::has('password.request'))
                    <div class="small mt-5">
                        <a href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif
            </x-form>
        </x-card.centered>
    </div>
@endsection
