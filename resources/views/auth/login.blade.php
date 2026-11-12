<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Sign in | CvSU Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'DM Sans', sans-serif; color: #23352d; background: #f5f7f6; }
        .login-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(280px, .9fr) minmax(380px, 1.1fr); }
        .brand-panel { position: relative; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: clamp(32px, 6vw, 76px); color: white; background: #174b38; }
        .brand-panel::before, .brand-panel::after { position: absolute; content: ''; border: 1px solid rgba(255,255,255,.09); border-radius: 50%; pointer-events: none; }
        .brand-panel::before { width: 480px; height: 480px; right: -220px; top: 14%; }
        .brand-panel::after { width: 340px; height: 340px; right: -150px; top: 24%; }
        .brand-head, .brand-copy, .brand-foot { position: relative; z-index: 1; }
        .brand-mark { width: 54px; height: 54px; display: grid; place-items: center; padding: 3px; border: 2px solid #d7b65d; border-radius: 50%; background: white; }
        .brand-mark img { width: 100%; height: 100%; object-fit: contain; }
        .brand-title { margin: 0; font-size: 14px; font-weight: 800; letter-spacing: .07em; }
        .brand-subtitle { margin: 5px 0 0; color: #d5e7dc; font-size: 12px; }
        .brand-copy { max-width: 470px; padding: 70px 0; }
        .eyebrow { color: #e2c66f; font-size: 11px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
        .brand-copy h1 { max-width: 430px; margin: 20px 0 16px; font-size: clamp(36px, 5vw, 58px); line-height: 1.06; letter-spacing: -.04em; }
        .brand-copy p { max-width: 390px; margin: 0; color: #d1e0d7; font-size: 15px; line-height: 1.8; }
        .brand-foot { color: #c7d8cd; font-size: 12px; }
        .form-panel { display: grid; place-items: center; padding: 40px 24px; }
        .form-wrap { width: min(100%, 430px); }
        .mobile-brand { display: none; }
        .form-wrap h2 { margin: 0; color: #183e30; font-size: 30px; letter-spacing: -.03em; }
        .form-intro { margin: 9px 0 30px; color: #718078; font-size: 14px; line-height: 1.6; }
        .field { margin-bottom: 19px; }
        .field label { display: block; margin-bottom: 8px; color: #31453b; font-size: 13px; font-weight: 700; }
        .field input { width: 100%; height: 50px; padding: 0 14px; border: 1px solid #d7e0da; border-radius: 9px; outline: none; background: white; color: #263b30; font: inherit; font-size: 14px; transition: border-color .15s, box-shadow .15s; }
        .field input:focus { border-color: #28634c; box-shadow: 0 0 0 3px rgba(40,99,76,.12); }
        .field input::placeholder { color: #a4afa8; }
        .field-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 3px 0 23px; font-size: 13px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; color: #68776f; }
        .remember input { accent-color: #174b38; }
        a { color: #28634c; font-weight: 700; text-decoration: none; }
        a:hover { color: #174b38; text-decoration: underline; }
        .submit { width: 100%; min-height: 50px; border: 0; border-radius: 9px; background: #174b38; color: white; font: inherit; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .15s, transform .15s; }
        .submit:hover { transform: translateY(-1px); background: #28634c; }
        .notice { margin-bottom: 20px; padding: 12px 14px; border: 1px solid #d5e6dc; border-radius: 9px; background: #edf5f0; color: #24583f; font-size: 13px; line-height: 1.5; }
        .error { margin: 7px 0 0; color: #b42318; font-size: 12px; }
        .secure-note { display: flex; align-items: center; justify-content: center; gap: 7px; margin: 24px 0 0; color: #8a978f; font-size: 12px; }
        .account-note { margin: 18px 0 0; color: #718078; text-align: center; font-size: 12px; line-height: 1.6; }
        @media (max-width: 760px) { .login-shell { grid-template-columns: 1fr; } .brand-panel { display: none; } .form-panel { min-height: 100vh; padding: 30px 22px; } .mobile-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 50px; } .mobile-brand .brand-mark { width: 46px; height: 46px; } .mobile-brand strong { display: block; color: #174b38; font-size: 12px; letter-spacing: .04em; } .mobile-brand span { color: #75847b; font-size: 11px; } }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel" aria-label="CvSU Lost and Found">
            <div class="brand-head"><div class="brand-mark"><img src="{{ asset('cvsu-logo.png') }}" alt="Cavite State University seal"></div></div>
            <div class="brand-copy">
                <div class="eyebrow">Cavite State University</div>
                <h1>Good things find their way back.</h1>
                <p>A secure place for the CvSU community to report, find, and reclaim lost belongings on campus.</p>
            </div>
            <div class="brand-foot">Student Lost &amp; Found Management System</div>
        </section>

        <section class="form-panel">
            <div class="form-wrap">
                <div class="mobile-brand"><div class="brand-mark"><img src="{{ asset('cvsu-logo.png') }}" alt="Cavite State University seal"></div><div><strong>CAVITE STATE UNIVERSITY</strong><span>Student Lost &amp; Found</span></div></div>
                <h2>Welcome back</h2>
                <p class="form-intro">Sign in with your assigned account to continue.</p>

                @if (session('status'))
                    <div class="notice" role="status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@cvsu.edu.ph" autocomplete="username" required autofocus>
                        @error('email')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                        @error('password')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field-row">
                        <label class="remember"><input type="checkbox" name="remember" value="1"> Remember me</label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button class="submit" type="submit">Sign in to your account</button>
                </form>
                <p class="account-note">Students and administrators sign in with their assigned accounts. Visitors can <a href="{{ route('visitor.register') }}">create an account with a Gmail address</a> to browse verified items.</p>
                <p class="secure-note"><span aria-hidden="true">◆</span> Your account and information are protected.</p>
            </div>
        </section>
    </main>
</body>
</html>
