<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy - {{ $platformName }}</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --paper:#F6F1E5; --paper2:#EDE4CE; --card:#FFFDF6;
  --ink:#0D3226; --ink-soft:rgba(13,50,38,.65);
  --green:#0A7A4C; --green-deep:#073526; --gold:#FFC42E; --gold-deep:#E8A400;
  --display:"Bricolage Grotesque",sans-serif;
  --body:"Instrument Sans",sans-serif;
  --mono:"Space Mono",monospace;
  --hs:8px 8px 0 var(--ink);
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:var(--body);background:var(--paper);color:var(--ink);line-height:1.7;overflow-x:hidden}
.nav{position:sticky;top:0;z-index:100;background:var(--paper);border-bottom:2px solid var(--ink)}
.nav-in{display:flex;align-items:center;justify-content:space-between;height:72px;width:min(1100px,92%);margin-inline:auto}
.brand{display:flex;align-items:center;gap:10px;font-family:var(--display);font-weight:800;font-size:1.3rem;text-decoration:none;color:var(--ink)}
.brand .mark{width:36px;height:36px;background:var(--gold);border:2px solid var(--ink);border-radius:10px;display:grid;place-items:center;box-shadow:3px 3px 0 var(--ink)}
.btn{display:inline-flex;align-items:center;gap:8px;font-weight:600;font-size:.9rem;padding:10px 20px;border:2px solid var(--ink);border-radius:12px;background:var(--card);color:var(--ink);box-shadow:4px 4px 0 var(--ink);text-decoration:none;transition:transform .2s,box-shadow .2s}
.btn:hover{transform:translate(-2px,-2px);box-shadow:var(--hs)}
.btn-gold{background:var(--gold)}

main{width:min(900px,92%);margin:48px auto 80px}
.doc-box{background:var(--card);border:2px solid var(--ink);border-radius:20px;padding:48px;box-shadow:10px 10px 0 var(--ink)}
.eyebrow{font-family:var(--mono);font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;color:var(--green);margin-bottom:12px;display:block}
h1{font-family:var(--display);font-size:2.8rem;font-weight:800;line-height:1.1;margin-bottom:12px}
.updated{font-family:var(--mono);font-size:.78rem;color:var(--ink-soft);margin-bottom:36px;padding-bottom:24px;border-bottom:2px dashed rgba(13,50,38,.2)}
h2{font-family:var(--display);font-size:1.35rem;font-weight:700;margin:32px 0 12px;color:var(--ink)}
p{color:var(--ink-soft);font-size:1.02rem;margin-bottom:18px}
ul{margin:0 0 20px 24px;color:var(--ink-soft)}
li{margin-bottom:8px}
a{color:var(--green);font-weight:600}

footer{background:var(--green-deep);color:#CFE3D8;border-top:2px solid var(--ink);padding:48px 0;margin-top:auto}
.f-in{width:min(1100px,92%);margin-inline:auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;font-family:var(--mono);font-size:.72rem}
.f-links{display:flex;gap:20px}
.f-links a{color:#CFE3D8;text-decoration:none}
.f-links a:hover{color:var(--gold)}

@media(max-width:640px){
  .doc-box{padding:28px 20px}
  h1{font-size:2.1rem}
}
</style>
</head>
<body>

<header class="nav">
  <div class="nav-in">
    <a class="brand" href="{{ route('home') }}">
      <span class="mark">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D3226" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>
      </span>
      {{ $platformName }}
    </a>
    <a class="btn btn-gold" href="{{ route('home') }}">← Back to Home</a>
  </div>
</header>

<main>
  <div class="doc-box">
    <span class="eyebrow">PRIVACY & PROTECTION</span>
    <h1>Privacy Policy</h1>
    <div class="updated">EFFECTIVE DATE: {{ date('F d, Y') }}</div>

    <h2>1. Information We Collect</h2>
    <p>When you register on <strong>{{ $platformName }}</strong>, we collect essential account details including your name, email address, phone number, and transaction activity necessary to execute VTU recharges and bill payments.</p>

    <h2>2. Purpose of Data Usage</h2>
    <p>Your information is used strictly to process telecom top-ups, verify virtual bank transfer deposits, maintain wallet security, send transaction receipts, and assist support queries.</p>

    <h2>3. Data Protection & Cryptography</h2>
    <p>All user passwords and 4-digit transaction PINs are stored securely using industry-standard cryptographic hashing. Virtual account transactions are processed through CBN-licensed payment partners.</p>

    <h2>4. Data Sharing & Third Parties</h2>
    <p>We do not sell or rent your personal data to third parties. Data is shared only with partner telecom gateways and bill aggregators solely for executing your requested transactions.</p>

    <h2>5. Contact Us</h2>
    <p>For any privacy inquiries or account data requests, please email us at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>
  </div>
</main>

<footer>
  <div class="f-in">
    <span>&copy; {{ date('Y') }} {{ strtoupper($platformName) }} • ALL RIGHTS RESERVED</span>
    <div class="f-links">
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('terms') }}">Terms</a>
      <a href="{{ route('privacy') }}">Privacy</a>
    </div>
  </div>
</footer>

</body>
</html>
