<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- User Selection -->
        <div class="mt-4">
            <button type="button" id="user1" class="user-button">User 1</button>
            <button type="button" id="user2" class="user-button">User 2</button>
            <button type="button" id="user3" class="user-button">User 3</button>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript Code -->
    <script>
        // Kullanıcı bilgilerini tıklama ile dolduran fonksiyonlar
        document.getElementById('user1').addEventListener('click', function() {
            document.getElementById('email').value = 'nurullah@nurullah.net';
            document.getElementById('password').value = 'nurullah';
        });

        document.getElementById('user2').addEventListener('click', function() {
            document.getElementById('email').value = 'nurullah2@nurullah.net';
            document.getElementById('password').value = 'nurullah2';
        });

        document.getElementById('user3').addEventListener('click', function() {
            document.getElementById('email').value = 'nurullah3@nurullah.net';
            document.getElementById('password').value = 'nurullah3';
        });
    </script>
</x-guest-layout>
<style>
    .user-button {
        background-color: #4CAF50; /* Yeşil arka plan */
        color: white;
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .user-button:hover {
        background-color: #45a049; /* Hover efekti */
    }
</style>
