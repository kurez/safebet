@extends('layouts.appheader')

@section('content')
            <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('Reset your password') }}
                </h2>
                <!-- <p class="mt-2 text-center text-sm text-gray-600">
                    Or
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                    {{ __('start your 14-day free trial') }}
                    </a>
                </p> -->
                </div>

                

                    

                    <form method="POST"  class="mt-8 space-y-6"  action="{{ route('password.email') }}">
                        @csrf

                        @if (session('status'))
                        <span class="alert alert-success text-green-500">
                            {{ session('status') }}
                        </span>
                         @endif

                        <input type="hidden" name="remember" value="true">
                            <div class="rounded-md shadow-sm -space-y-px">
                                <div>
                                <label for="email-address" class="sr-only">{{ __('E-Mail Address') }}</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus required class="appearance-none form-control @error('email') is-invalid @enderror rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"  placeholder="Email address">
                                @error('email')
                                                            <span class="invalid-feedback text-red-500" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                </div>

                                <br>
                                
                                <div>

                                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-black-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black-50">
                                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                        <!-- Heroicon name: solid/lock-closed -->
                                        <svg class="h-5 w-5 text-white-500 group-hover:text-white-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>

                    </form>
                
            </div>
        </div>
    </div>
</div>
@endsection
