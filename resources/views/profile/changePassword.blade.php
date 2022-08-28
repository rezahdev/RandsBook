<x-app-layout>
    @if(isset($status) && $status == 'PASSWORD_CHANGED')
        <script> logout_btn.click(); </script>
    @endif
    <div class="w-[calc(100%-2rem)] md:w-3/5 lg:1/2 ml-4 md:ml-1/5 lg:ml-1/4 bg-white rounded p-3 mt-5">
        <p class="text-center">Change password</p>
    </div>
    <div class="w-[calc(100%-2rem)] md:w-3/5 lg:1/2 ml-4 md:ml-1/5 lg:ml-1/4 bg-white rounded p-3 mt-2">
        @if($errors->any())
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        @endif
        <form method="POST" action="{{ route('profile.update_password') }}">
            @csrf
            @method('PUT')

            <!-- current password -->
            <div class="mt-4">
                <x-label for="current_password" :value="__(' Current Password')" />
                <x-input class="block mt-1 w-full" type="password" name="current_password" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />
                <x-input class="block mt-1 w-full" type="password" name="password" required />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-input class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Change Password') }}
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>