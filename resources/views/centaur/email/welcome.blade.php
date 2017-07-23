<!DOCTYPE html>
<html lang="fa">
	<head>
		<meta charset="utf-8">
	</head>
	<body style="direction: rtl; text-align: right">
		<h2 style="text-align: right">@lang("user.Welcome_To_Javosh")</h2>

		<p style="direction: rtl; text-align: right">@lang("user.Account"): {{ $email }}</p>
		<p style="direction: rtl; text-align: right">@lang("user.To_activate_your_account") <a href="{{ route('auth.activation.attempt', urlencode($code)) }}">@lang("user.this_link")</a> @lang("user.click")</p>
		<p style="direction: rtl; text-align: right">@lang("user.point_your_browser_to_this_address")
		</p>
		<p style="direction: ltr">{!! route('auth.activation.attempt', urlencode($code)) !!}</p>
		<p style="direction: rtl; text-align: right">@lang("user.Thank_you!")</p>
	</body>
</html>