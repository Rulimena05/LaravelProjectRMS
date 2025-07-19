<x-guest-layout>
    <div class="auth-container" id="container">
        <div class="form-container sign-up-container">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <h1>Create Account</h1>
                
                <input type="text" name="name" placeholder="Name" class="input input-bordered w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-error" />
                
                <input type="email" name="email" placeholder="Email" class="input input-bordered w-full" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-error" />
                
                <input type="password" name="password" placeholder="Password" class="input input-bordered w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-error" />
                
                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="input input-bordered w-full" required />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-error" />

                <button class="btn btn-primary mt-4">Sign Up</button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <h1>Sign in</h1>
                
                <input type="email" name="email" placeholder="Email" class="input input-bordered w-full" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-error" />
                
                <input type="password" name="password" placeholder="Password" class="input input-bordered w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-error" />
                
                <a href="{{ route('password.request') }}" class="link link-hover">Forgot your password?</a>
                <button class="btn btn-primary">Sign In</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost btn" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost btn" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk menangani error validasi register --}}
    @if ($errors->has('name') || $errors->has('password_confirmation'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('container').classList.add('right-panel-active');
        });
    </script>
    @endif
</x-guest-layout>