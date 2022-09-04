<x-guest-layout>
    <x-auth-card>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" id="registration_form">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Name')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="flex items-center justify-center mt-4">
                <x-button onclick="openRegistrationConfirmationBox(event)">
                    {{ __('Register') }}
                </x-button>
            </div>

            <div class="flex items-center justify-center mt-4">
                <a class="underline text-m text-blue-600 hover:text-blue-900" href="{{ route('login') }}">
                    {{ __('Already have an account? Sign in') }}
                </a>
            </div>
        </form>
    </x-auth-card>
    <div id="registration_confirmation_box">
        <p>This app is a demo version of RandsBook. The app was developed by Reza Saker and it is currently provided under MIT LIcense.
           Registration is solely for the purpose of exploring various features of RandsBook. This app uses cookies. 
        </p>
        <div class="flex flex flex-row flex-wrap justify-center items-center mt-2 ">
            <button class="bg-green-700 text-white px-2 py-0.5 rounded mr-2 border border-green-700 hover:bg-blue-700"
                    onclick="register()">
                    I understand, Register
            </button>
            <button class="bg-white border border-green-700 text-green-500 px-2 py-0.5 rounded hover:bg-blue-700 hover:text-white"
                    onclick="closeRegistrationConfirmationBox()">
                    Cancel
            </button>
        </div>
    </div>
</x-guest-layout>

<style>
#registration_confirmation_box {
    text-align: center;
    background-color: white;
    padding: 20px 10px 20px 10px;
    border-radius: 2px;
    width: 50%;
    margin-left: 25%;
    position:fixed;
    top:200px;
    display:none;
    border:solid 1px dimgray
}

@media(max-width: 680px)
{
    #registration_confirmation_box {
        width: 92%;
        margin-left: 4%;
    }
}
</style>

<script>
function openRegistrationConfirmationBox(e)
{
    e.preventDefault();
    registration_confirmation_box.style.display = "block";
}

function closeRegistrationConfirmationBox()
{
    registration_confirmation_box.style.display = "none";
}

function register()
{
    closeRegistrationConfirmationBox();
    registration_form.submit();
}
</script>
