<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->headline ?: $form->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #17171c;
            color: #ececef;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #1c1c22;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 36px 32px;
        }
        .mark {
            width: 34px; height: 34px;
            background: linear-gradient(150deg, #a78bfa, #2dd4bf);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        h1 { font-size: 21px; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 6px; color: #ececef; }
        p.desc { font-size: 13.5px; color: #8f8e98; line-height: 1.6; margin-bottom: 24px; }
        label { display: block; font-size: 13px; font-weight: 500; color: #8f8e98; margin-bottom: 6px; }
        input[type="text"], input[type="email"] {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #ececef;
            font-size: 14px;
            font-family: inherit;
            padding: 10px 13px;
            margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.15); }
        button {
            width: 100%;
            background: #8b5cf6;
            border: none;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
        }
        button:hover { background: #7c3aed; }
        .error { color: #f87171; font-size: 12px; margin: -12px 0 14px; }
        .success-icon {
            width: 48px; height: 48px;
            background: rgba(52,211,153,0.12);
            border: 1px solid rgba(52,211,153,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
            font-size: 22px;
        }
        .footer-note { text-align: center; font-size: 11px; color: #55545c; margin-top: 22px; }
        .hp { position: absolute; left: -9999px; }
    </style>
</head>
<body>
    <div class="card">
        @if($submitted)
            <div class="success-icon">✓</div>
            <h1>{{ $form->success_message }}</h1>
        @else
            <a href="https://sendpeak.in/" target="_blank" rel="noopener" style="display:inline-block;">
                <div class="mark"></div>
            </a>
            <h1>{{ $form->headline ?: $form->name }}</h1>
            @if($form->description)
                <p class="desc">{{ $form->description }}</p>
            @endif

            <form method="POST" action="{{ route('lead-forms.public.submit', $form->slug) }}">
                @csrf
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">

                @if($form->hasField('first_name'))
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ $old['first_name'] ?? '' }}">
                @endif

                @if($form->hasField('last_name'))
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ $old['last_name'] ?? '' }}">
                @endif

                <label for="email">Email <span style="color:#f87171;">*</span></label>
                <input type="email" id="email" name="email" required value="{{ $old['email'] ?? '' }}">
                @if(isset($errors) && $errors->has('email'))
                    <div class="error">{{ $errors->first('email') }}</div>
                @endif

                @if($form->hasField('phone'))
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ $old['phone'] ?? '' }}">
                @endif

                @if($form->hasField('company'))
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" value="{{ $old['company'] ?? '' }}">
                @endif

                <button type="submit">Submit</button>
            </form>
        @endif

        <div class="footer-note">Designed &amp; Developed By <a href="https://kaxon.in/" target="_blank" rel="noopener" style="color:inherit;">Kaxon</a></div>
    </div>
</body>
</html>
