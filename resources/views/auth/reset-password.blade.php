<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose a new password | CvSU Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#f5f7f6;color:#23352d;font-family:'DM Sans',sans-serif}.card{width:min(100%,470px);padding:42px;background:#fff;border:1px solid #e4eae6;border-radius:16px;box-shadow:0 18px 55px rgba(23,75,56,.08)}.mark{width:52px;height:52px;display:grid;place-items:center;margin-bottom:25px;border:2px solid #d7b65d;border-radius:50%;background:#174b38;color:#fff;font-size:13px;font-weight:800}.eyebrow{color:#8b9a91;font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase}.card h1{margin:9px 0;color:#183e30;font-size:28px;letter-spacing:-.03em}.intro{margin:0 0 25px;color:#718078;font-size:14px;line-height:1.7}.field{margin-bottom:17px}.field label{display:block;margin-bottom:8px;color:#31453b;font-size:13px;font-weight:700}.field input{width:100%;height:50px;padding:0 14px;border:1px solid #d7e0da;border-radius:9px;outline:none;font:inherit;font-size:14px}.field input:focus{border-color:#28634c;box-shadow:0 0 0 3px rgba(40,99,76,.12)}.button{width:100%;height:50px;margin-top:5px;border:0;border-radius:9px;background:#174b38;color:#fff;font:inherit;font-weight:700;cursor:pointer}.button:hover{background:#28634c}.error{margin:7px 0;color:#b42318;font-size:12px}.back{display:block;margin-top:22px;text-align:center;color:#28634c;font-size:13px;font-weight:700;text-decoration:none}.back:hover{text-decoration:underline}@media(max-width:480px){.card{padding:30px 23px}}
    </style>
</head>
<body>
    <main class="card">
        <div class="mark">CvSU</div>
        <div class="eyebrow">Account recovery</div>
        <h1>Choose a new password</h1>
        <p class="intro">Create a new password for your CvSU Lost &amp; Found account.</p>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="field"><label for="password">New password</label><input id="password" name="password" type="password" autocomplete="new-password" required>@error('password')<p class="error">{{ $message }}</p>@enderror</div>
            <div class="field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
            <button class="button" type="submit">Save new password</button>
        </form>
        <a class="back" href="{{ route('login') }}">← Back to sign in</a>
    </main>
</body>
</html>
