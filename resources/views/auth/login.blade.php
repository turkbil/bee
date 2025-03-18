<!-- resources/views/auth/login.blade.php -->
<x-guest-layout>
    <div style="min-height: 100vh; background-color: #1a1a1a; display: flex; align-items: center; justify-content: center;">
        <div style="background-color: #2d2d2d; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
            <div style="margin-bottom: 24px; text-align: center; color: #e0e0e0;">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;">{{ tenant('id') }}</h2>
                <p style="font-size: 14px; opacity: 0.75;">{{ request()->getHost() }}</p>
            </div>

            @if (session('status'))
                <div style="margin-bottom: 16px; font-size: 14px; color: #e0e0e0;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="width: 100%;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            placeholder="Email"
                            style="width: 100%; padding: 8px 16px; background-color: #404040; border: 1px solid #606060; border-radius: 6px; color: #e0e0e0; outline: none;"
                        />
                        @error('email')
                            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Password"
                            style="width: 100%; padding: 8px 16px; background-color: #404040; border: 1px solid #606060; border-radius: 6px; color: #e0e0e0; outline: none;"
                        />
                        @error('password')
                            <p style="margin-top: 4px; font-size: 14px; color: #f87171;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center;">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            style="margin-right: 8px; background-color: #404040; border: 1px solid #606060;"
                        />
                        <label for="remember" style="font-size: 14px; color: #e0e0e0;">Remember me</label>
                    </div>

                    <button 
                        type="submit"
                        style="width: 100%; padding: 8px 16px; background-color: #2563eb; border: none; border-radius: 6px; color: white; font-weight: 600; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'"
                    >
                        Login
                    </button>

                    <button 
                        type="button"
                        onclick="fillDemoUser()"
                        style="width: 100%; margin-top: 8px; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 600; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        Demo Kullanıcı ile Giriş
                    </button>
                </div>
            </form>

            <script>
                function fillDemoUser() {
                    const host = window.location.host;
                    let email = '';
                    let password = '';
                    
                    switch(host) {
                        case 'a.test':
                            email = 'a@test';
                            password = 'test';
                            break;
                        case 'b.test':
                            email = 'b@test';
                            password = 'test';
                            break;
                        case 'c.test':
                            email = 'c@test';
                            password = 'test';
                            break;
                        case 'laravel.test':
                            email = 'nurullah@nurullah.net';
                            password = 'nurullah';
                            break;
                    }
                    
                    document.querySelector('input[name="email"]').value = email;
                    document.querySelector('input[name="password"]').value = password;
                }
            </script>
        </div>
    </div>
</x-guest-layout>