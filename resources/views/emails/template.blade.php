<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sendpeak</title>
</head>
<body style="margin:0;padding:0;">
    <div>{!! $mailData['body'] !!}</div>

    @isset($unsubscribeUrl)
        @if($unsubscribeUrl)
            <div style="margin-top:32px;padding-top:16px;border-top:1px solid #e5e7eb;
                        font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#6b7280;line-height:1.6;">
                <p style="margin:0 0 6px;">
                    You are receiving this email because you subscribed via
                    <a href="https://sendpeak.in" style="color:#6b7280;">sendpeak.in</a>.
                </p>
                <p style="margin:0;">
                    <a href="{{ $unsubscribeUrl }}" style="color:#6b7280;text-decoration:underline;">Unsubscribe</a>
                    from these emails at any time.
                </p>
            </div>
        @endif
    @endisset
</body>
</html>
