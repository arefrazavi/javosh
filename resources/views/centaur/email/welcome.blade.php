<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
</head>
<body style="direction: rtl; text-align: right">
    <div>
        <h2 style="text-align: right">@lang("user.Welcome_To_Javosh")</h2>
        <p style="direction: rtl; text-align: right">@lang("user.Account"): {{ $email }}</p>
        <p style="direction: rtl; text-align: right">@lang("user.login_to_your_account_with_following_link"): </p>
        <p><a href="{!! route('auth.login.form') !!}">@lang("user.login_link")</a></p>
        <p style="direction: rtl; text-align: right">@lang("user.Thank_you!")</p>
    </div>
</body>
</html>