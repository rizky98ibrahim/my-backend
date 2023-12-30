<x-mail::message>
    # Reset Password

    You are receiving this email because we received a password reset request for your account.
    This is your verification code: {{ $pin }}.

    If you did not request a password reset, no further action is required.

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
