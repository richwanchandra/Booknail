<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Masukkan kode OTP 6-digit yang telah dikirim ke email Anda beserta kata sandi baru Anda.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.otp.update') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $email)" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- OTP Code -->
        <div class="mt-4">
            <x-input-label for="otp" :value="__('Kode OTP (6 Digit)')" />
            <x-text-input id="otp" class="block mt-1 w-full text-center tracking-widest font-mono text-lg" type="text" name="otp" :value="old('otp')" required maxlength="6" placeholder="123456" autocomplete="off" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500" href="{{ route('password.otp.request') }}">
                {{ __('Kirim Ulang OTP') }}
            </a>

            <x-primary-button>
                {{ __('Reset Kata Sandi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
