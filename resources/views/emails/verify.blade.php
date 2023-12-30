<x-mail::message>
    # Email Verification

    Thanks for creating an account with the {{ config('app.name') }}.
    This is your verification code: {{ $pin }}.

    If you did not create an account, no further action is required.

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
