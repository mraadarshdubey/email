<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $done ? 'Unsubscribed' : 'Unsubscribe' }} — Sendpeak</title>
    <style>
        body{margin:0;background:#07070c;color:#e8e8f2;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
             display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .card{background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.1);border-radius:20px;
              padding:40px 36px;max-width:460px;width:100%;text-align:center}
        .mark{width:52px;height:52px;border-radius:14px;margin:0 auto 20px;display:grid;place-items:center;
              background:linear-gradient(135deg,#a78bfa,#2dd4bf)}
        h1{font-size:22px;margin:0 0 10px;letter-spacing:-.02em}
        p{color:#9a9ab0;font-size:15px;line-height:1.6;margin:0 0 22px}
        .email{color:#e8e8f2;font-weight:600;word-break:break-all}
        button{background:linear-gradient(120deg,#a78bfa,#8b5cf6);color:#0b0714;border:0;border-radius:999px;
               padding:13px 28px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit}
        button:hover{opacity:.9}
        .ok{width:52px;height:52px;border-radius:50%;margin:0 auto 20px;display:grid;place-items:center;
            background:rgba(45,212,191,.14);border:1px solid rgba(45,212,191,.4)}
        .foot{margin-top:24px;font-size:12.5px;color:#63637a}
        .foot a{color:#a78bfa;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        @if($done)
            <div class="ok">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M20 6L9 17l-5-5" stroke="#2dd4bf" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>You've been unsubscribed</h1>
            <p><span class="email">{{ $email }}</span> has been removed from our mailing list. You will not receive further marketing emails from us.</p>
        @else
            <div class="mark">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="5" width="18" height="14" rx="3" stroke="#0b0714" stroke-width="2.2"/>
                    <path d="M3 8l9 6 9-6" stroke="#0b0714" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Unsubscribe from our emails?</h1>
            <p>Confirm that you no longer want to receive marketing emails at <span class="email">{{ $email }}</span>.</p>
            <form method="POST" action="{{ $postUrl }}">
                @csrf
                <button type="submit">Yes, unsubscribe me</button>
            </form>
        @endif
        <p class="foot">Sent by <a href="https://sendpeak.in">Sendpeak</a></p>
    </div>
</body>
</html>
