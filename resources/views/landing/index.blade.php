<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $seo['meta_title'] ?? ($platformName . ' — Airtime, Data & Bills. Sharp sharp.') }}</title>
<meta name="description" content="{{ $seo['meta_description'] ?? ($platformName . ' is Nigeria\'s fastest VTU wallet. Buy discounted airtime, SME data, pay electricity and cable bills, and get exam pins instantly.') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{ !empty($favicon) ? (str_starts_with($favicon, 'http') ? $favicon : asset('storage/' . $favicon)) : asset('favicon.ico') }}" />

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ============ TOKENS & RESET ============ */
:root{
  --paper:#F6F1E5; --paper2:#EDE4CE; --card:#FFFDF6;
  --ink:#0D3226; --ink-soft:rgba(13,50,38,.65);
  --green:#0A7A4C; --green-deep:#073526; --gold:#FFC42E; --gold-deep:#E8A400;
  --red:#E4572E;
  --mtn:#FFCB05; --glo:#3DA639; --airtel:#E60000; --9m:#0E8A5F;
  --display:"Bricolage Grotesque",sans-serif;
  --body:"Instrument Sans",sans-serif;
  --mono:"Space Mono",monospace;
  --hs:8px 8px 0 var(--ink);
}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;scroll-padding-top:96px}
body{
  font-family:var(--body);background:var(--paper);color:var(--ink);
  font-size:16.5px;line-height:1.6;overflow-x:hidden;
}
body::after{ /* grain overlay */
  content:"";position:fixed;inset:0;pointer-events:none;z-index:90;opacity:.05;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2'/%3E%3C/filter%3E%3Crect width='120' height='120' filter='url(%23n)'/%3E%3C/svg%3E");
}
img{display:block;max-width:100%}
a{color:inherit;text-decoration:none}
ul{list-style:none}
::selection{background:var(--gold);color:var(--ink)}
:focus-visible{outline:3px solid var(--gold-deep);outline-offset:3px;border-radius:4px}
.container{width:min(1180px,92%);margin-inline:auto}
.mono{font-family:var(--mono)}
.skip{position:absolute;left:-999px;top:8px;background:var(--ink);color:var(--gold);padding:10px 16px;z-index:200}
.skip:focus{left:8px}

h1,h2,h3{font-family:var(--display);line-height:1.05;letter-spacing:-.01em}
h2{font-size:clamp(2rem,4.4vw,3.2rem);font-weight:800}
mark{background:linear-gradient(transparent 52%,var(--gold) 52%);color:inherit;padding:0 .08em}

.eyebrow{
  font-family:var(--mono);font-size:.72rem;letter-spacing:.22em;text-transform:uppercase;
  color:var(--green);display:inline-flex;align-items:center;gap:10px;margin-bottom:16px;
}
.eyebrow::before{content:"";width:26px;height:2px;background:var(--green)}
.sec-head{display:flex;justify-content:space-between;align-items:flex-end;gap:32px;flex-wrap:wrap;margin-bottom:52px}
.sec-head p{max-width:380px;color:var(--ink-soft)}

/* ============ BUTTONS & CHIPS ============ */
.btn{
  display:inline-flex;align-items:center;gap:10px;font-weight:600;font-size:.95rem;
  padding:14px 24px;border:2px solid var(--ink);border-radius:12px;cursor:pointer;
  transition:transform .2s,box-shadow .2s;background:var(--card);color:var(--ink);
  box-shadow:4px 4px 0 var(--ink);font-family:var(--body);
}
.btn:hover{transform:translate(-3px,-3px);box-shadow:var(--hs)}
.btn:active{transform:translate(0,0);box-shadow:2px 2px 0 var(--ink)}
.btn-ink{background:var(--ink);color:var(--paper)}
.btn-gold{background:var(--gold)}
.btn-sm{padding:10px 18px;font-size:.85rem;border-radius:10px}
.arrow{display:inline-block;transition:transform .25s}
.btn:hover .arrow,.lnk:hover .arrow{transform:translateX(5px)}
.lnk{font-weight:600;border-bottom:2px solid var(--gold);padding-bottom:2px}
.chip{display:inline-flex;align-items:center;gap:8px;font-family:var(--mono);font-size:.72rem;letter-spacing:.08em;
  border:1.5px solid var(--ink);border-radius:999px;padding:6px 14px;background:var(--card)}

/* pulsing live dot */
.pulse{width:9px;height:9px;border-radius:50%;background:var(--green);position:relative;flex:none}
.pulse::after{content:"";position:absolute;inset:-5px;border-radius:50%;border:2px solid var(--green);
  animation:pulse 1.8s ease-out infinite}
@keyframes pulse{from{transform:scale(.4);opacity:1}to{transform:scale(1.2);opacity:0}}

/* ============ NAV ============ */
.nav{position:sticky;top:0;z-index:100;background:var(--paper);border-bottom:2px solid var(--ink)}
.nav input{position:absolute;opacity:0;pointer-events:none}
.nav-in{display:flex;align-items:center;gap:28px;height:72px}
.brand{display:flex;align-items:center;gap:10px;font-family:var(--display);font-weight:800;font-size:1.3rem;letter-spacing:-.02em}
.brand .mark{width:36px;height:36px;background:var(--gold);border:2px solid var(--ink);border-radius:10px;
  display:grid;place-items:center;box-shadow:3px 3px 0 var(--ink)}
.brand small{font-family:var(--mono);font-size:.6rem;letter-spacing:.2em;background:var(--ink);color:var(--gold);
  padding:3px 7px;border-radius:5px;transform:translateY(-8px)}
.links{display:flex;gap:26px;margin-left:auto;font-weight:500;font-size:.92rem}
.links a{position:relative;padding:4px 0}
.links a::after{content:"";position:absolute;left:0;bottom:0;width:100%;height:2px;background:var(--gold);
  transform:scaleX(0);transform-origin:right;transition:transform .3s}
.links a:hover::after{transform:scaleX(1);transform-origin:left}
.burger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;margin-left:auto}
.burger span{width:24px;height:2.5px;background:var(--ink);border-radius:2px}

/* ============ HERO ============ */
.hero{display:grid;grid-template-columns:1.05fr .95fr;gap:48px;align-items:center;padding:64px 0 88px}
.live-pill{display:inline-flex;align-items:center;gap:10px;font-family:var(--mono);font-size:.72rem;
  letter-spacing:.12em;border:1.5px solid var(--ink);border-radius:999px;padding:8px 16px;background:var(--card);margin-bottom:26px}
.hero h1{font-size:clamp(2.6rem,6vw,4.7rem);font-weight:800}
.mask{display:block;overflow:hidden;padding-bottom:.06em}
.mask>span{display:block;animation:riseUp .9s cubic-bezier(.2,.7,.2,1) both;animation-delay:var(--i)}
@keyframes riseUp{from{transform:translateY(112%)}}
.hero .lede{margin:24px 0 30px;max-width:480px;color:var(--ink-soft);font-size:1.05rem}
.hero-cta{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:44px}

/* odometer stats */
.stats{display:flex;gap:36px;flex-wrap:wrap;border-top:2px dashed rgba(13,50,38,.3);padding-top:28px}
.stat .num{font-family:var(--mono);font-weight:700;font-size:1.55rem;display:flex;align-items:center}
.stat .lbl{font-size:.78rem;color:var(--ink-soft);letter-spacing:.04em}
.odo{display:inline-flex;height:1em;overflow:hidden;line-height:1}
.roll{display:flex;flex-direction:column;line-height:1;transform:translateY(calc(var(--d) * -1em));
  animation:roll 1.6s cubic-bezier(.16,.8,.24,1) both;animation-delay:var(--dl,.4s)}
@keyframes roll{from{transform:translateY(0)}}

/* hero stage: phone + decorations */
.stage{position:relative;display:flex;justify-content:center;min-height:600px}
.stage::before{content:"₦";position:absolute;right:-6%;top:-8%;font-family:var(--display);font-weight:800;
  font-size:26rem;line-height:1;color:transparent;-webkit-text-stroke:2px rgba(13,50,38,.12);pointer-events:none}
.stamp{position:absolute;top:-14px;right:2%;width:128px;height:128px;animation:spin 18s linear infinite;z-index:3}
@keyframes spin{to{transform:rotate(360deg)}}
.stamp text{font-family:var(--mono);font-size:11.5px;letter-spacing:.28em;fill:var(--ink)}
.stamp circle.c{fill:var(--gold);stroke:var(--ink);stroke-width:2}

.device{width:302px;background:#0F1D17;border:3px solid var(--ink);border-radius:42px;padding:11px;
  box-shadow:14px 14px 0 rgba(13,50,38,.85);position:relative;z-index:2;animation:deviceIn 1s cubic-bezier(.2,.8,.2,1) .2s both}
@keyframes deviceIn{from{opacity:0;transform:translateY(40px) rotate(2deg)}}
.screen{background:#FBF7EC;border-radius:30px;overflow:hidden;display:flex;flex-direction:column}
.s-status{display:flex;justify-content:space-between;padding:12px 18px 6px;font-family:var(--mono);font-size:.66rem;letter-spacing:.05em}
.s-hello{padding:8px 18px 4px;font-size:.78rem;color:var(--ink-soft)}
.s-hello b{display:block;font-family:var(--display);font-size:1.05rem;color:var(--ink)}
.s-bal{margin:10px 14px;background:var(--green-deep);color:#EAF4EE;border-radius:16px;padding:16px;position:relative;overflow:hidden}
.s-bal::before{content:"";position:absolute;inset:0;background:repeating-linear-gradient(-45deg,transparent 0 12px,rgba(255,255,255,.045) 12px 24px)}
.s-bal .lbl{font-family:var(--mono);font-size:.58rem;letter-spacing:.22em;opacity:.75}
.s-bal .amt{font-family:var(--display);font-weight:800;font-size:1.7rem;margin:4px 0 10px}
.s-bal .fund{display:inline-block;background:var(--gold);color:var(--ink);font-family:var(--mono);font-size:.6rem;
  letter-spacing:.1em;padding:6px 12px;border-radius:999px;font-weight:700}
.s-actions{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;padding:6px 14px 12px}
.s-act{background:var(--card);border:1.5px solid var(--ink);border-radius:12px;padding:10px 4px 8px;
  display:flex;flex-direction:column;align-items:center;gap:5px;font-size:.58rem;font-weight:600}
.s-act svg{width:18px;height:18px}
.s-act:first-child{background:var(--gold)}
.s-recent{padding:2px 14px 14px;font-size:.66rem}
.s-recent h4{font-family:var(--mono);font-size:.56rem;letter-spacing:.22em;color:var(--ink-soft);margin-bottom:8px}
.s-row{display:flex;align-items:center;gap:8px;padding:7px 0;border-top:1px dashed rgba(13,50,38,.25)}
.s-row .dot{width:7px;height:7px;border-radius:50%;background:var(--green);flex:none}
.s-row b{flex:1;font-weight:600}
.s-row span{font-family:var(--mono);color:var(--ink-soft)}
.s-nav{margin-top:auto;display:flex;justify-content:space-around;padding:10px 10px 14px;border-top:2px solid var(--ink);background:var(--card)}
.s-nav svg{width:17px;height:17px;opacity:.35}
.s-nav svg.on{opacity:1}

/* floating toasts */
.toast{position:absolute;z-index:5;display:flex;align-items:center;gap:10px;background:var(--card);
  border:2px solid var(--ink);border-radius:12px;padding:10px 14px;font-size:.78rem;font-weight:600;
  box-shadow:5px 5px 0 var(--ink);animation:toastLoop 12s ease-in-out infinite;animation-delay:var(--td)}
.toast .tick{width:22px;height:22px;border-radius:50%;background:var(--green);display:grid;place-items:center;flex:none}
.toast .tick svg{width:12px;height:12px;stroke:#fff}
.toast small{display:block;font-weight:400;color:var(--ink-soft);font-family:var(--mono);font-size:.62rem}
.toast.t1{left:-6%;top:24%;--td:0s}
.toast.t2{right:-4%;top:48%;--td:4s}
.toast.t3{left:-2%;bottom:8%;--td:8s}
@keyframes toastLoop{0%,100%{opacity:0;transform:translateY(14px)}5%,30%{opacity:1;transform:none}36%,99%{opacity:0;transform:translateY(-10px)}}
.fl-chip{position:absolute;z-index:4;font-family:var(--mono);font-size:.66rem;letter-spacing:.08em;font-weight:700;
  background:var(--gold);border:2px solid var(--ink);border-radius:999px;padding:8px 14px;box-shadow:4px 4px 0 var(--ink);
  animation:float 6s ease-in-out infinite}
.fl-chip.f1{right:0;top:12%}
.fl-chip.f2{left:4%;bottom:24%;background:var(--card);animation-delay:-3s}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}

/* ============ TICKER ============ */
.ticker{background:var(--gold);border-block:2px solid var(--ink);overflow:hidden;padding:13px 0}
.ticker-track{display:flex;width:max-content;animation:marquee 26s linear infinite}
.ticker-track ul{display:flex;align-items:center;gap:34px;padding-right:34px;
  font-family:var(--mono);font-weight:700;font-size:.82rem;letter-spacing:.14em;white-space:nowrap}
.ticker-track li{display:flex;align-items:center;gap:34px}
.ticker-track li::after{content:"";width:9px;height:9px;background:var(--ink);transform:rotate(45deg);flex:none}
@keyframes marquee{to{transform:translateX(-50%)}}

/* ============ NETWORKS ============ */
section{padding:100px 0}
.nets-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.net{border:2px solid var(--ink);border-radius:16px;padding:28px 24px;background:var(--c);color:var(--tc);
  box-shadow:5px 5px 0 var(--ink);transition:transform .25s,box-shadow .25s;position:relative;overflow:hidden}
.net:hover{transform:translate(-4px,-4px);box-shadow:9px 9px 0 var(--ink)}
.net .nn{font-family:var(--display);font-weight:800;font-size:1.9rem;letter-spacing:-.02em}
.net .tag{font-family:var(--mono);font-size:.64rem;letter-spacing:.12em;display:inline-block;margin-top:12px;
  border:1.5px solid currentColor;border-radius:999px;padding:5px 12px}
.net .sig{position:absolute;right:16px;top:16px;display:flex;gap:3px;align-items:flex-end}
.net .sig i{width:4px;background:currentColor;border-radius:1px;display:block}
.net .sig i:nth-child(1){height:6px}.net .sig i:nth-child(2){height:10px}
.net .sig i:nth-child(3){height:14px}.net .sig i:nth-child(4){height:18px}

/* ============ SERVICES (tickets) ============ */
.services{background:var(--paper2);border-block:2px solid var(--ink)}
.tix{display:grid;grid-template-columns:repeat(auto-fit,minmax(310px,1fr));gap:24px}
.ticket{position:relative;background:var(--card);border:2px solid var(--ink);border-radius:16px;
  transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column}
.ticket:hover{transform:translate(-4px,-4px);box-shadow:var(--hs)}
.ticket::before,.ticket::after{content:"";position:absolute;width:20px;height:20px;border-radius:50%;
  background:var(--paper2);border:2px solid var(--ink);bottom:49px;z-index:1}
.ticket::before{left:-12px}.ticket::after{right:-12px}
.ticket .body{padding:26px 26px 20px;flex:1}
.t-ico{width:50px;height:50px;border:2px solid var(--ink);border-radius:13px;background:var(--paper);
  display:grid;place-items:center;margin-bottom:18px}
.t-ico svg{width:24px;height:24px}
.ticket h3{font-size:1.35rem;margin-bottom:8px}
.ticket .body p{font-size:.9rem;color:var(--ink-soft)}
.t-tag{position:absolute;top:18px;right:18px;font-family:var(--mono);font-size:.6rem;letter-spacing:.12em;
  background:var(--gold);border:1.5px solid var(--ink);padding:4px 10px;border-radius:999px;font-weight:700}
.ticket .foot{height:60px;border-top:2px dashed rgba(13,50,38,.35);display:flex;align-items:center;
  justify-content:space-between;padding:0 26px;border-radius:0 0 14px 14px}
.ticket .price{font-family:var(--mono);font-weight:700;font-size:.95rem}
.ticket .price small{color:var(--ink-soft);font-weight:400;font-size:.7rem;display:block}

/* ============ RATES (CSS tabs) ============ */
.rates-box{position:relative;border:2px solid var(--ink);border-radius:20px;background:var(--card);
  padding:36px;box-shadow:10px 10px 0 var(--ink)}
.rate-radio{position:absolute;opacity:0;pointer-events:none}
.rtabs{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:30px}
.rate-tab{font-family:var(--mono);font-weight:700;font-size:.82rem;letter-spacing:.1em;border:2px solid var(--ink);
  border-radius:999px;padding:10px 22px;cursor:pointer;background:var(--paper);transition:all .2s;user-select:none}
.rate-tab:hover{transform:translateY(-2px)}
#r-mtn:checked~.rtabs [for="r-mtn"]{background:var(--mtn);color:#111;box-shadow:3px 3px 0 var(--ink)}
#r-glo:checked~.rtabs [for="r-glo"]{background:var(--glo);color:#fff;box-shadow:3px 3px 0 var(--ink)}
#r-airtel:checked~.rtabs [for="r-airtel"]{background:var(--airtel);color:#fff;box-shadow:3px 3px 0 var(--ink)}
#r-9m:checked~.rtabs [for="r-9m"]{background:var(--9m);color:#fff;box-shadow:3px 3px 0 var(--ink)}
.rpanel{display:none;grid-template-columns:300px 1fr;gap:32px;align-items:start}
#r-mtn:checked~.rpanels .p-mtn{display:grid;animation:panelIn .4s ease both}
#r-glo:checked~.rpanels .p-glo{display:grid;animation:panelIn .4s ease both}
#r-airtel:checked~.rpanels .p-airtel{display:grid;animation:panelIn .4s ease both}
#r-9m:checked~.rpanels .p-9m{display:grid;animation:panelIn .4s ease both}
@keyframes panelIn{from{opacity:0;transform:translateY(12px)}}
.deal{border:2px solid var(--ink);border-radius:16px;padding:26px;background:var(--nc);color:var(--nt);box-shadow:5px 5px 0 var(--ink)}
.deal .k{font-family:var(--mono);font-size:.62rem;letter-spacing:.2em;border:1.5px solid currentColor;border-radius:999px;padding:4px 10px}
.deal .big{font-family:var(--display);font-weight:800;font-size:2.6rem;margin:16px 0 4px;letter-spacing:-.02em}
.deal .big b{font-size:1.4rem;vertical-align:top}
.deal p{font-size:.85rem;opacity:.9}
.deal .note{display:block;margin-top:16px;font-family:var(--mono);font-size:.66rem;letter-spacing:.05em;
  border-top:1.5px dashed currentColor;padding-top:12px}
.rows{border:2px solid var(--ink);border-radius:16px;overflow:hidden;background:#fff}
.rows .rh,.rows .rrow{display:grid;grid-template-columns:1.1fr 1fr 1fr 40px;align-items:center;padding:13px 20px;gap:10px}
.rows .rh{font-family:var(--mono);font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;
  background:var(--ink);color:var(--paper);padding:11px 20px}
.rows .rrow{border-top:1.5px dashed rgba(13,50,38,.2);transition:background .2s}
.rows .rrow:hover{background:var(--paper)}
.rows .plan{font-weight:700;font-family:var(--display);font-size:1.05rem}
.rows .val{font-size:.82rem;color:var(--ink-soft)}
.rows .prc{font-family:var(--mono);font-weight:700}
.rows .go{width:26px;height:26px;border:1.5px solid var(--ink);border-radius:50%;display:grid;place-items:center;font-size:.8rem}
.best{font-family:var(--mono);font-size:.56rem;background:var(--gold);border:1.5px solid var(--ink);
  border-radius:999px;padding:2px 8px;margin-left:8px;letter-spacing:.1em;font-weight:700;vertical-align:middle}
.rates-note{margin-top:20px;font-family:var(--mono);font-size:.7rem;color:var(--ink-soft);letter-spacing:.04em}

/* ============ HOW IT WORKS (sticky) ============ */
.how-grid{display:grid;grid-template-columns:1fr 1.15fr;gap:70px;align-items:start}
.how-sticky{position:sticky;top:110px}
.how-sticky .big-n{font-family:var(--display);font-weight:800;font-size:clamp(5rem,10vw,9rem);line-height:1;
  color:transparent;-webkit-text-stroke:2px var(--ink);margin-top:26px}
.how-sticky p{color:var(--ink-soft);max-width:340px;margin-top:18px}
.steps{display:flex;flex-direction:column;gap:0;border-left:2px dashed rgba(13,50,38,.35);padding-left:44px}
.step{position:relative;padding:34px 0 34px 10px;border-bottom:2px dashed rgba(13,50,38,.2)}
.step:last-child{border-bottom:none}
.step::before{content:"";position:absolute;left:-53px;top:44px;width:16px;height:16px;border-radius:50%;
  background:var(--gold);border:2px solid var(--ink)}
.step .n{font-family:var(--mono);font-weight:700;color:var(--green);font-size:.8rem;letter-spacing:.2em}
.step h3{font-size:1.5rem;margin:8px 0 8px}
.step p{color:var(--ink-soft);max-width:440px;font-size:.95rem}
.step .mini{display:inline-block;margin-top:12px;font-family:var(--mono);font-size:.66rem;letter-spacing:.08em;
  background:var(--paper2);border:1.5px solid var(--ink);border-radius:8px;padding:6px 12px}

/* ============ BENTO ============ */
.bento{display:grid;grid-template-columns:repeat(4,1fr);grid-template-areas:
  "up up fund fund"
  "up up renew photo"
  "recv sec support photo";gap:20px}
.tile{border:2px solid var(--ink);border-radius:16px;background:var(--card);padding:26px;
  box-shadow:4px 4px 0 var(--ink);transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column;gap:10px}
.tile:hover{transform:translate(-3px,-3px);box-shadow:7px 7px 0 var(--ink)}
.tile h3{font-size:1.25rem}
.tile p{font-size:.86rem;color:var(--ink-soft)}
.tile .ic{width:42px;height:42px;border:2px solid var(--ink);border-radius:11px;display:grid;place-items:center;background:var(--paper)}
.tile .ic svg{width:20px;height:20px}
.t-up{grid-area:up;background:var(--green-deep);color:#EAF4EE}
.t-up p{color:rgba(234,244,238,.72)}
.t-up .huge{font-family:var(--display);font-weight:800;font-size:3.4rem;letter-spacing:-.02em;color:var(--gold)}
.bars{display:flex;align-items:flex-end;gap:7px;height:120px;margin-top:auto;padding-top:20px}
.bars i{flex:1;background:rgba(255,255,255,.28);border-radius:4px 4px 0 0;height:var(--h);
  transform-origin:bottom;animation:grow .9s cubic-bezier(.2,.8,.2,1) both;animation-delay:calc(var(--i)*70ms)}
.bars i.hi{background:var(--gold)}
@keyframes grow{from{transform:scaleY(0)}}
.t-fund{grid-area:fund}
.banks{display:flex;gap:8px;flex-wrap:wrap;margin-top:6px}
.banks span{font-family:var(--mono);font-size:.64rem;border:1.5px solid var(--ink);border-radius:999px;padding:4px 12px;background:var(--paper)}
.t-renew{grid-area:renew}
.switch{width:58px;height:32px;border:2px solid var(--ink);border-radius:999px;background:#E7DFC8;position:relative;margin-top:auto}
.switch i{position:absolute;top:2px;left:4px;width:22px;height:22px;border-radius:50%;background:var(--green);
  border:2px solid var(--ink);animation:flip 3.4s ease-in-out infinite}
@keyframes flip{0%,38%{transform:translateX(0)}52%,88%{transform:translateX(24px)}100%{transform:translateX(0)}}
.t-photo{grid-area:photo;padding:0;overflow:hidden;position:relative;min-height:320px}
.t-photo img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;animation:kenburns 16s ease-in-out infinite alternate}
@keyframes kenburns{from{transform:scale(1)}to{transform:scale(1.14) translate(2%,-2%)}}
.t-photo .cap{position:absolute;left:14px;right:14px;bottom:14px;background:var(--gold);border:2px solid var(--ink);
  border-radius:10px;padding:10px 14px;font-family:var(--mono);font-size:.66rem;font-weight:700;letter-spacing:.1em}
.t-recv{grid-area:recv}
.receipt{background:#fff;border:2px solid var(--ink);border-radius:8px;padding:14px;font-family:var(--mono);
  font-size:.62rem;line-height:1.9;margin-top:auto;border-bottom:2px dashed var(--ink)}
.receipt b{display:block;font-size:.72rem}
.t-sec{grid-area:sec}
.t-support{grid-area:support;background:var(--gold)}
.t-support p{color:rgba(13,50,38,.8)}

/* ============ TESTIMONIALS (scattered postcards) ============ */
.quotes-wrap{position:relative;min-height:560px}
.qcard{position:absolute;width:330px;background:var(--card);border:2px solid var(--ink);border-radius:6px;
  padding:26px;box-shadow:6px 8px 0 rgba(13,50,38,.75);transition:transform .35s,z-index 0s;transform:rotate(var(--r))}
.qcard:hover{transform:rotate(0) scale(1.04);z-index:10}
.qcard::before{content:"";position:absolute;top:-12px;left:50%;width:92px;height:26px;background:rgba(255,196,46,.85);
  border:1px solid rgba(13,50,38,.25);transform:translateX(-50%) rotate(-2deg)}
.qcard .stars{color:var(--gold-deep);letter-spacing:3px;font-size:.95rem;margin-bottom:12px}
.qcard blockquote{font-size:.94rem;line-height:1.55}
.qcard .who{display:flex;align-items:center;gap:12px;margin-top:18px;border-top:1.5px dashed rgba(13,50,38,.3);padding-top:14px}
.qcard .who img{width:42px;height:42px;border-radius:50%;border:2px solid var(--ink);filter:grayscale(1) contrast(1.05)}
.qcard .who b{display:block;font-size:.9rem}
.qcard .who span{font-family:var(--mono);font-size:.64rem;color:var(--ink-soft);letter-spacing:.08em}
.q1{--r:-4deg;left:0;top:30px}
.q2{--r:3deg;left:31%;top:120px}
.q3{--r:-2.5deg;right:0;top:0}
.q4{--r:5deg;left:12%;bottom:0}

/* ============ RESELLER (dark) ============ */
.reseller{background:var(--green-deep);color:#EAF4EE;border-block:2px solid var(--ink)}
.res-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center}
.reseller .eyebrow{color:var(--gold)}
.reseller .eyebrow::before{background:var(--gold)}
.reseller h2 mark{background:linear-gradient(transparent 52%,rgba(255,196,46,.9) 52%)}
.reseller p.lead{color:rgba(234,244,238,.75);margin:20px 0 28px;max-width:460px}
.commission{border:2px solid rgba(255,196,46,.5);border-radius:14px;overflow:hidden;margin-bottom:30px}
.commission div{display:flex;justify-content:space-between;padding:12px 20px;font-family:var(--mono);font-size:.78rem;
  border-bottom:1.5px dashed rgba(255,196,46,.3)}
.commission div:last-child{border-bottom:none}
.commission b{color:var(--gold)}
.code-window{background:#051E15;border:2px solid var(--ink);border-radius:16px;overflow:hidden;
  box-shadow:10px 10px 0 rgba(0,0,0,.4)}
.code-bar{display:flex;gap:7px;padding:14px 18px;border-bottom:1.5px solid rgba(234,244,238,.15)}
.code-bar i{width:11px;height:11px;border-radius:50%;background:rgba(234,244,238,.25)}
.code-bar i:first-child{background:var(--gold)}
.code-window pre{padding:22px;font-family:var(--mono);font-size:.78rem;line-height:1.85;overflow-x:auto;color:#BFDCCF}
.code-window .m{color:#7FD6AE}.code-window .g{color:var(--gold)}.code-window .c{color:#6C8F81}
.res-stats{display:flex;gap:40px;margin-top:26px;flex-wrap:wrap}
.res-stats .num{font-family:var(--mono);font-weight:700;font-size:1.5rem;color:var(--gold)}
.res-stats .lbl{font-size:.76rem;color:rgba(234,244,238,.6)}

/* ============ PLANS ============ */
.plans-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.plan{border:2px solid var(--ink);border-radius:18px;background:var(--card);padding:34px 30px;position:relative;
  transition:transform .25s,box-shadow .25s;display:flex;flex-direction:column}
.plan:hover{transform:translateY(-6px);box-shadow:var(--hs)}
.plan.pop{background:var(--ink);color:var(--paper)}
.plan.pop p,.plan.pop li{color:rgba(246,241,229,.8)}
.plan .pop-tag{position:absolute;top:-14px;right:24px;background:var(--gold);color:var(--ink);border:2px solid var(--ink);
  font-family:var(--mono);font-size:.6rem;font-weight:700;letter-spacing:.16em;padding:6px 14px;border-radius:999px}
.plan h3{font-size:1.4rem}
.plan .amount{font-family:var(--display);font-weight:800;font-size:2.4rem;margin:14px 0 4px}
.plan .amount small{font-family:var(--mono);font-size:.7rem;font-weight:400;color:inherit;opacity:.6}
.plan .sub{font-size:.85rem;color:var(--ink-soft);margin-bottom:22px}
.plan ul{flex:1;margin-bottom:26px}
.plan li{display:flex;gap:10px;padding:8px 0;font-size:.9rem;border-bottom:1.5px dashed rgba(13,50,38,.2)}
.plan.pop li{border-color:rgba(246,241,229,.2)}
.plan li svg{width:16px;height:16px;flex:none;margin-top:4px;stroke:var(--green)}
.plan.pop li svg{stroke:var(--gold)}

/* ============ FAQ ============ */
.faq-list{max-width:780px;margin-inline:auto}
.faq-item{border:2px solid var(--ink);border-radius:14px;background:var(--card);margin-bottom:16px;overflow:hidden;
  transition:box-shadow .25s}
.faq-item:hover{box-shadow:5px 5px 0 var(--ink)}
.faq-item summary{display:flex;justify-content:space-between;align-items:center;gap:20px;cursor:pointer;
  padding:22px 26px;font-weight:600;font-family:var(--display);font-size:1.1rem;list-style:none}
.faq-item summary::-webkit-details-marker{display:none}
.faq-item summary::after{content:"+";font-family:var(--mono);font-size:1.5rem;font-weight:400;flex:none;
  width:34px;height:34px;border:2px solid var(--ink);border-radius:50%;display:grid;place-items:center;transition:transform .3s,background .3s}
.faq-item[open] summary::after{transform:rotate(45deg);background:var(--gold)}
.faq-item .a{padding:0 26px 24px;color:var(--ink-soft);font-size:.94rem;max-width:640px}

/* ============ DOWNLOAD ============ */
.dl-panel{background:var(--gold);border:2px solid var(--ink);border-radius:24px;padding:64px;
  display:grid;grid-template-columns:1.25fr .75fr;gap:48px;align-items:center;box-shadow:12px 12px 0 var(--ink);position:relative;overflow:hidden}
.dl-panel::before{content:"₦";position:absolute;right:-40px;bottom:-90px;font-family:var(--display);font-weight:800;
  font-size:20rem;line-height:1;color:transparent;-webkit-text-stroke:2px rgba(13,50,38,.18);pointer-events:none}
.dl-panel h2{font-size:clamp(2rem,4vw,3.1rem)}
.dl-panel .pts{display:flex;gap:22px;flex-wrap:wrap;margin:22px 0 30px;font-family:var(--mono);font-size:.72rem;font-weight:700;letter-spacing:.06em}
.dl-panel .pts span{display:flex;align-items:center;gap:8px}
.dl-panel .pts svg{width:15px;height:15px}
.badges{display:flex;gap:14px;flex-wrap:wrap}
.badge{display:inline-flex;align-items:center;gap:12px;background:var(--ink);color:var(--paper);border:2px solid var(--ink);
  border-radius:13px;padding:11px 20px;transition:transform .2s,box-shadow .2s;box-shadow:4px 4px 0 rgba(13,50,38,.4)}
.badge:hover{transform:translate(-3px,-3px);box-shadow:7px 7px 0 rgba(13,50,38,.4)}
.badge svg{width:24px;height:24px;flex:none}
.badge small{display:block;font-family:var(--mono);font-size:.56rem;letter-spacing:.14em;opacity:.7}
.badge b{font-size:1.02rem;letter-spacing:-.01em}
.qr-wrap{text-align:center;position:relative;z-index:1}
.qr{width:158px;height:158px;background:#fff;border:2px solid var(--ink);border-radius:12px;margin:0 auto;
  padding:12px;box-shadow:6px 6px 0 rgba(13,50,38,.5)}
.qr-grid{width:100%;height:100%;background:repeating-conic-gradient(var(--ink) 0 25%,#fff 0 50%) 0 0/17px 17px;
  border:3px solid #fff;outline:2px solid var(--ink)}
.qr-wrap p{font-family:var(--mono);font-size:.64rem;letter-spacing:.14em;margin-top:14px;font-weight:700}

/* ============ FOOTER ============ */
footer{background:var(--green-deep);color:#CFE3D8;border-top:2px solid var(--ink);padding:72px 0 0}
.f-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr 1fr;gap:44px;padding-bottom:56px}
.f-brand p{font-size:.86rem;color:rgba(207,227,216,.65);margin:16px 0 20px;max-width:280px}
footer h4{font-family:var(--mono);font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);margin-bottom:18px}
footer li{margin-bottom:10px;font-size:.9rem}
footer li a{color:rgba(207,227,216,.85);transition:color .2s}
footer li a:hover{color:var(--gold)}
.pay-row{display:flex;gap:10px;flex-wrap:wrap}
.pay-row span{font-family:var(--mono);font-size:.62rem;letter-spacing:.08em;border:1.5px solid rgba(207,227,216,.4);
  border-radius:8px;padding:6px 12px}
.f-status{display:inline-flex;align-items:center;gap:10px;font-family:var(--mono);font-size:.66rem;letter-spacing:.1em;
  border:1.5px solid rgba(207,227,216,.35);border-radius:999px;padding:8px 16px;margin-top:20px}
.barcode{height:44px;background:repeating-linear-gradient(90deg,#CFE3D8 0 2px,transparent 2px 5px,#CFE3D8 5px 8px,transparent 8px 11px,#CFE3D8 11px 12px,transparent 12px 16px);
  opacity:.35;margin-bottom:26px}
.f-bottom{border-top:1.5px solid rgba(207,227,216,.2);padding:24px 0;display:flex;justify-content:space-between;
  gap:16px;flex-wrap:wrap;font-family:var(--mono);font-size:.66rem;letter-spacing:.06em;color:rgba(207,227,216,.6)}

/* ============ SCROLL REVEAL (progressive) ============ */
@supports (animation-timeline: view()){
  .reveal{animation:revealUp .7s cubic-bezier(.2,.7,.2,1) backwards;animation-timeline:view();animation-range:entry 0% entry 42%}
  @keyframes revealUp{from{opacity:0;transform:translateY(30px)}}
}

/* ============ RESPONSIVE ============ */
@media(max-width:1080px){
  .hero{grid-template-columns:1fr;gap:70px}
  .stage{min-height:auto;padding-top:20px}
  .stage::before{font-size:18rem;right:0}
  .how-grid{grid-template-columns:1fr;gap:40px}
  .how-sticky{position:static}
  .bento{grid-template-columns:repeat(2,1fr);grid-template-areas:
    "up up" "fund fund" "renew sec" "recv support" "photo photo"}
  .t-photo{min-height:280px}
  .res-grid{grid-template-columns:1fr;gap:48px}
  .quotes-wrap{min-height:0}
  .qcard{position:static;width:100%;margin-bottom:26px;--r:-1.5deg}
  .qcard:nth-child(even){--r:1.5deg}
  .plans-grid{grid-template-columns:1fr;max-width:520px;margin-inline:auto}
  .f-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:880px){
  .links{position:absolute;top:100%;left:0;right:0;background:var(--paper);border-bottom:2px solid var(--ink);
    flex-direction:column;gap:0;padding:8px 0;opacity:0;transform:translateY(-12px);pointer-events:none;transition:.3s}
  .links a{padding:14px 6%;border-top:1px dashed rgba(13,50,38,.2)}
  .nav input:checked~.nav-in .links{opacity:1;transform:none;pointer-events:auto}
  .burger{display:flex}
  .nav .btn-sm{display:none}
  .nets-grid{grid-template-columns:repeat(2,1fr)}
  .rpanel{grid-template-columns:1fr!important}
  .dl-panel{grid-template-columns:1fr;padding:40px 28px}
  .rates-box{padding:24px}
}
@media(max-width:560px){
  body{font-size:15.5px}
  section{padding:72px 0}
  .nets-grid{grid-template-columns:1fr}
  .stats{gap:22px}
  .device{width:268px}
  .toast.t1{left:-4%}.toast.t2{right:-2%}
  .stamp{width:96px;height:96px;right:0}
  .rows .rh,.rows .rrow{grid-template-columns:1fr 1fr 34px;padding:12px 14px}
  .rows .val{display:none}
  .sec-head{margin-bottom:36px}
}

/* ============ REDUCED MOTION ============ */
@media (prefers-reduced-motion: reduce){
  html{scroll-behavior:auto}
  *,*::before,*::after{animation:none!important;transition:none!important}
}
</style>
</head>
<body id="top">
<a class="skip" href="#main">Skip to content</a>

<!-- ================= NAV ================= -->
<header class="nav">
  <input type="checkbox" id="menu" aria-label="Toggle menu">
  <div class="nav-in container">
    <a class="brand" href="#top" aria-label="{{ $platformName }} home">
      @if(!empty($logoImage))
        <img src="{{ str_starts_with($logoImage, 'http') ? $logoImage : asset('storage/' . $logoImage) }}" alt="{{ $platformName }}" style="height:40px;max-width:180px;object-fit:contain;">
      @else
        <span class="mark">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D3226" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>
        </span>
        {{ $platformName }} <small>VTU</small>
      @endif
    </a>
    <nav class="links" aria-label="Primary">
      <a href="#services">Services</a>
      <a href="#rates">Rates</a>
      <a href="#how">How it works</a>
      <a href="#resellers">Resellers</a>
      <a href="#faq">FAQ</a>
    </nav>

    @auth
      <a class="btn btn-gold btn-sm" href="{{ route('dashboard') }}">Dashboard</a>
    @else
      <a class="btn btn-gold btn-sm" href="{{ route('register') }}">Open account</a>
    @endauth
    <label class="burger" for="menu" aria-hidden="true"><span></span><span></span><span></span></label>
  </div>
</header>

<main id="main">

<!-- ================= HERO ================= -->
<section class="hero container">
  <div class="hero-copy">
    <span class="live-pill"><span class="pulse"></span> {{ strtoupper($hero['badge'] ?? 'ALL NETWORKS LIVE • 99.98% UPTIME') }}</span>
    <h1>
      <span class="mask"><span style="--i:.05s">{{ $hero['title'] ?? 'Recharge anything.' }}</span></span>
    </h1>
    <p class="lede">{{ $hero['subtitle'] ?? ($platformName . " is Nigeria's fastest VTU wallet — buy airtime at a discount, load SME data, pay electricity and cable bills, and get WAEC & JAMB pins instantly.") }}</p>
    <div class="hero-cta">
      @auth
        <a class="btn btn-ink" href="{{ route('dashboard') }}">Go to Dashboard
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
      @else
        <a class="btn btn-ink" href="{{ $hero['cta_url'] ?? route('register') }}">{{ $hero['cta_text'] ?? 'Get Started' }}
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
      @endauth
      <a class="btn" href="#rates">See today's rates</a>
    </div>
    <div class="stats">
      <div class="stat">
        <div class="num"><span class="odo"><span class="roll" style="--d:1;--dl:.5s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span>.<span class="odo"><span class="roll" style="--d:2;--dl:.65s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span>M+</div>
        <div class="lbl">top-ups monthly</div>
      </div>
      <div class="stat">
        <div class="num"><span class="odo"><span class="roll" style="--d:3;--dl:.8s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span><span class="odo"><span class="roll" style="--d:0;--dl:.9s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span>s</div>
        <div class="lbl">avg. delivery</div>
      </div>
      <div class="stat">
        <div class="num"><span class="odo"><span class="roll" style="--d:9;--dl:1s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span><span class="odo"><span class="roll" style="--d:9;--dl:1.05s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span>.<span class="odo"><span class="roll" style="--d:9;--dl:1.1s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span><span class="odo"><span class="roll" style="--d:8;--dl:1.15s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span>%</div>
        <div class="lbl">uptime, last 90 days</div>
      </div>
      <div class="stat">
        <div class="num"><span class="odo"><span class="roll" style="--d:4;--dl:1.25s">0<br>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9</span></span></div>
        <div class="lbl">networks supported</div>
      </div>
    </div>
  </div>

  <div class="stage" aria-hidden="true">
    <svg class="stamp" viewBox="0 0 128 128">
      <circle class="c" cx="64" cy="64" r="62"/>
      <path id="cir" fill="none" d="M64 14a50 50 0 1 1 0 100 50 50 0 1 1 0-100"/>
      <text><textPath href="#cir">INSTANT TOP-UP • SHARP SHARP • NAIJA'S OWN •</textPath></text>
      <path d="M68 44 54 66h9l-3 18 14-22h-9l3-18z" fill="#0D3226"/>
    </svg>

    <div class="fl-chip f1">AIRTIME &amp; DATA</div>
    <div class="fl-chip f2">TOKEN SENT • 00:12s</div>

    <div class="device">
      <div class="screen">
        <div class="s-status"><span>09:41</span><span>5G &nbsp; 100%</span></div>
        <div class="s-hello">Good afternoon,<b>Customer</b></div>
        <div class="s-bal">
          <span class="lbl">WALLET BALANCE</span>
          <div class="amt">₦12,450.75</div>
          <span class="fund">+ FUND WALLET</span>
        </div>
        <div class="s-actions">
          <div class="s-act"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>Airtime</div>
          <div class="s-act"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12a10 10 0 0 1 14 0M8.5 15.5a5 5 0 0 1 7 0M12 19h.01"/></svg>Data</div>
          <div class="s-act"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg>Power</div>
          <div class="s-act"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M8 21h8M12 18v3"/></svg>Cable</div>
        </div>
        <div class="s-recent">
          <h4>RECENT</h4>
          <div class="s-row"><span class="dot"></span><b>MTN Airtime • 0803</b><span>₦1,000</span></div>
          <div class="s-row"><span class="dot"></span><b>DStv Compact+</b><span>₦15,700</span></div>
          <div class="s-row"><span class="dot"></span><b>IKEDC Meter 4512</b><span>₦5,000</span></div>
        </div>
        <div class="s-nav">
          <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11 12 3l9 8v9a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1v-9z"/></svg>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H4zM2 20h20"/></svg>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
        </div>
      </div>
    </div>

    <div class="toast t1"><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg></span><span>₦1,000 MTN airtime delivered<small>REF ST-88213 • 8s ago</small></span></div>
    <div class="toast t2"><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg></span><span>DStv Compact+ activated<small>IUC •••• 4412</small></span></div>
    <div class="toast t3"><span class="tick"><svg viewBox="0 0 24 24" fill="none" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg></span><span>Wallet funded +₦5,000<small>AUTO-CREDIT • 27s</small></span></div>
  </div>
</section>

<!-- ================= TICKER ================= -->
<div class="ticker" aria-hidden="true">
  <div class="ticker-track">
    <ul>
      <li>AIRTIME DISCOUNTS</li><li>SME &amp; GIFTING DATA</li><li>ALL DISCO METERS</li>
      <li>DSTV • GOTV • STARTIMES</li><li>EXAM RESULT PINS</li>
      <li>INSTANT AUTO-REFUNDS</li><li>24/7 SUPPORT</li>
    </ul>
    <ul>
      <li>AIRTIME DISCOUNTS</li><li>SME &amp; GIFTING DATA</li><li>ALL DISCO METERS</li>
      <li>DSTV • GOTV • STARTIMES</li><li>EXAM RESULT PINS</li>
      <li>INSTANT AUTO-REFUNDS</li><li>24/7 SUPPORT</li>
    </ul>
  </div>
</div>

<!-- ================= NETWORKS ================= -->
<section class="container" id="networks">
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">01 — Networks</span>
      <h2>One wallet.<br>All four networks.</h2>
    </div>
    <p>Direct integrations with every major Nigerian network — no middlemen, no delays.</p>
  </div>
  <div class="nets-grid">
    <div class="net reveal" style="--c:var(--mtn);--tc:#141414">
      <span class="sig" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      <div class="nn">MTN</div><span class="tag">CHEAP SME &amp; GIFTING</span>
    </div>
    <div class="net reveal" style="--c:var(--glo);--tc:#fff">
      <span class="sig" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      <div class="nn">Glo</div><span class="tag">INSTANT RECHARGE</span>
    </div>
    <div class="net reveal" style="--c:var(--airtel);--tc:#fff">
      <span class="sig" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      <div class="nn">Airtel</div><span class="tag">CG DATA BUNDLES</span>
    </div>
    <div class="net reveal" style="--c:var(--9m);--tc:#fff">
      <span class="sig" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      <div class="nn">9mobile</div><span class="tag">FAST FULFILLMENT</span>
    </div>
  </div>
</section>

<!-- ================= SERVICES ================= -->
<section class="services" id="services">
  <div class="container">
    <div class="sec-head reveal">
      <div>
        <span class="eyebrow">02 — Services</span>
        <h2>Everything you need,<br>in one wallet.</h2>
      </div>
      <p>Automated VTU services. Every transaction comes with an instant receipt and auto-refund protection.</p>
    </div>
    <div class="tix">
      <article class="ticket reveal">
        <span class="t-tag">DISCOUNTED</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="7" y="2" width="10" height="20" rx="2.5"/><path d="M11 18h2M10.5 5h3"/></svg></div>
          <h3>{{ $services['airtime']['name'] ?? 'Airtime Top-up' }}</h3>
          <p>{{ $services['airtime']['description'] ?? 'Instant airtime on MTN, Glo, Airtel and 9mobile.' }}</p>
        </div>
        <div class="foot"><span class="price">Instant<small>delivered in ~5s</small></span><a class="lnk" href="{{ route('register') }}">Top up <span class="arrow">→</span></a></div>
      </article>

      <article class="ticket reveal">
        <span class="t-tag">BEST RATES</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M5 12a10 10 0 0 1 14 0M8.5 15.5a5 5 0 0 1 7 0M12 19h.01"/></svg></div>
          <h3>{{ $services['data']['name'] ?? 'Cheap Data Bundles' }}</h3>
          <p>{{ $services['data']['description'] ?? 'SME and gifting data plans at wholesale prices.' }}</p>
        </div>
        <div class="foot"><span class="price">Wholesale<small>30-day validity</small></span><a class="lnk" href="#rates">See rates <span class="arrow">→</span></a></div>
      </article>

      <article class="ticket reveal">
        <span class="t-tag">ALL DISCOS</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg></div>
          <h3>{{ $services['electricity']['name'] ?? 'Electricity Bills' }}</h3>
          <p>{{ $services['electricity']['description'] ?? 'Prepaid meter tokens and postpaid payments for all DISCOs.' }}</p>
        </div>
        <div class="foot"><span class="price">Instant Token<small>Prepaid &amp; Postpaid</small></span><a class="lnk" href="{{ route('register') }}">Pay now <span class="arrow">→</span></a></div>
      </article>

      <article class="ticket reveal">
        <span class="t-tag">ALL PLANS</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M8 21h8M12 18v3M7 9.5h6M7 12.5h4"/></svg></div>
          <h3>{{ $services['cable']['name'] ?? 'Cable TV Subscriptions' }}</h3>
          <p>{{ $services['cable']['description'] ?? 'DStv, GOtv, StarTimes and Showmax renewals.' }}</p>
        </div>
        <div class="foot"><span class="price">Instant<small>Zero extra charges</small></span><a class="lnk" href="{{ route('register') }}">Renew <span class="arrow">→</span></a></div>
      </article>

      <article class="ticket reveal">
        <span class="t-tag">INSTANT PIN</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M12 3 2 8l10 5 10-5-10-5zM6 10.5V16c0 1.5 3 3 6 3s6-1.5 6-3v-5.5M22 8v6"/></svg></div>
          <h3>{{ $services['exam']['name'] ?? 'Exam Result PINs' }}</h3>
          <p>{{ $services['exam']['description'] ?? 'WAEC e-PIN, NECO result checker, JAMB e-PIN and NABTEB.' }}</p>
        </div>
        <div class="foot"><span class="price">Instant PIN<small>24/7 delivery</small></span><a class="lnk" href="{{ route('register') }}">Buy pin <span class="arrow">→</span></a></div>
      </article>

      <article class="ticket reveal">
        <span class="t-tag">FAIR RATES</span>
        <div class="body">
          <div class="t-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M5.5 9.5h.01M18.5 14.5h.01"/></svg></div>
          <h3>{{ $services['airtime_cash']['name'] ?? 'Airtime to Cash' }}</h3>
          <p>{{ $services['airtime_cash']['description'] ?? 'Convert excess airtime from any network back into cash.' }}</p>
        </div>
        <div class="foot"><span class="price">Instant Cash<small>Bank deposit</small></span><a class="lnk" href="{{ route('register') }}">Convert <span class="arrow">→</span></a></div>
      </article>
    </div>
  </div>
</section>

<!-- ================= RATES ================= -->
<section class="container" id="rates">
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">03 — Rates</span>
      <h2>Today's data rates,<br>no hiding anything.</h2>
    </div>
    <p>Prices update live as networks adjust. What you see here is what you pay — nothing else added at checkout.</p>
  </div>

  <div class="rates-box reveal">
    @if(isset($popularDataPlans) && count($popularDataPlans) > 0)
      @foreach($popularDataPlans as $net => $plans)
        <input class="rate-radio" type="radio" name="net" id="r-{{ strtolower($net) }}" {{ $loop->first ? 'checked' : '' }}>
      @endforeach
      <div class="rtabs">
        @foreach($popularDataPlans as $net => $plans)
          <label class="rate-tab" for="r-{{ strtolower($net) }}">{{ strtoupper($net) }}</label>
        @endforeach
      </div>
      <div class="rpanels">
        @foreach($popularDataPlans as $net => $plans)
          <div class="rpanel p-{{ strtolower($net) }}">
            <div class="deal" style="--nc:var(--{{ strtolower($net) }}, var(--green));--nt:#fff">
              <span class="k">{{ strtoupper($net) }} DATA PLANS</span>
              <div class="big">1GB <b>₦{{ isset($plans[0]) ? number_format($plans[0]->selling_price) : '365' }}</b></div>
              <p>Best seller • 30 days validity • delivered in about 5 seconds.</p>
              <span class="note">AIRTIME TOP-UPS EARN UP TO 4.5% INSTANT DISCOUNT</span>
            </div>
            <ul class="rows">
              <li class="rh"><span>Plan</span><span>Validity</span><span>Price</span><span></span></li>
              @foreach($plans as $plan)
                <li class="rrow">
                  <span class="plan">{{ $plan->name }} @if($loop->iteration == 2)<span class="best">BEST</span>@endif</span>
                  <span class="val">30 days</span>
                  <span class="prc">₦{{ number_format($plan->selling_price) }}</span>
                  <a href="{{ route('register') }}" class="go">&rarr;</a>
                </li>
              @endforeach
            </ul>
          </div>
        @endforeach
      </div>
    @endif
    <p class="rates-note">* Live data rates from database. Airtime discounts apply automatically at checkout.</p>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="container" id="how">
  <div class="how-grid">
    <div class="how-sticky">
      <span class="eyebrow">04 — How it works</span>
      <h2>From zero to topped-up in four moves.</h2>
      <p>No paperwork, no branch visits. If you can send a WhatsApp message, you can run {{ $platformName }}.</p>
      <div class="big-n" aria-hidden="true">4x</div>
      <p class="mono" style="font-size:.7rem;letter-spacing:.18em">FASTER THAN THE AVERAGE AGENT QUEUE</p>
    </div>
    <div class="steps">
      <div class="step reveal">
        <span class="n">STEP 01</span>
        <h3>Create your wallet</h3>
        <p>Sign up with your phone number and verification. Your wallet is ready in under a minute.</p>
        <span class="mini">NO PAPERWORK • 60 SECONDS</span>
      </div>
      <div class="step reveal">
        <span class="n">STEP 02</span>
        <h3>Fund it in seconds</h3>
        <p>Transfer to your dedicated virtual account and it auto-credits in about 30 seconds.</p>
        <span class="mini">AUTO-CREDIT • ~30s</span>
      </div>
      <div class="step reveal">
        <span class="n">STEP 03</span>
        <h3>Pick a service</h3>
        <p>Airtime, data, electricity token, cable renewal or exam pin. Enter the number, confirm, done.</p>
        <span class="mini">ONE TAP CONFIRMATION</span>
      </div>
      <div class="step reveal">
        <span class="n">STEP 04</span>
        <h3>Delivered &amp; Receipted</h3>
        <p>Instant delivery with a shareable receipt. If anything ever fails, the money snaps back to your wallet automatically.</p>
        <span class="mini">AUTO-REFUND PROTECTION</span>
      </div>
    </div>
  </div>
</section>

<!-- ================= BENTO FEATURES ================= -->
<section class="container" id="features">
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">05 — {{ $about['title'] ?? ("Why Choose " . $platformName) }}</span>
      <h2>Built like infrastructure,<br>not just an app.</h2>
    </div>
    <p>{{ $about['description'] ?? "We are a trusted digital telecom distribution platform built with direct API gateways." }}</p>
  </div>
  <div class="bento">
    <div class="tile t-up reveal">
      <span class="mono" style="font-size:.66rem;letter-spacing:.22em;opacity:.7">SYSTEM STATUS — LAST 30 DAYS</span>
      <div class="huge">99.98%</div>
      <p>Uptime across all four networks. Direct telco integrations mean we don't queue behind aggregators.</p>
      <div class="bars" aria-hidden="true">
        <i style="--h:62%;--i:1"></i><i style="--h:78%;--i:2"></i><i style="--h:70%;--i:3"></i><i style="--h:88%;--i:4"></i>
        <i style="--h:74%;--i:5"></i><i class="hi" style="--h:96%;--i:6"></i><i style="--h:82%;--i:7"></i><i style="--h:90%;--i:8"></i>
        <i style="--h:76%;--i:9"></i><i style="--h:94%;--i:10"></i><i style="--h:86%;--i:11"></i><i class="hi" style="--h:100%;--i:12"></i>
      </div>
    </div>
    <div class="tile t-fund reveal">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M3 7a2 2 0 0 1 2-2h13v3M3 7v10a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-8a1 1 0 0 0-1-1H3zm13 6h.01"/></svg></div>
      <h3>Wallet funding that actually funds</h3>
      <p>Dedicated virtual accounts auto-credit in ~30 seconds. Card and USSD fallbacks included.</p>
      <div class="banks"><span>MONNIFY</span><span>PAYRANT</span><span>PAYSTACK</span><span>OPAY</span></div>
    </div>
    <div class="tile t-renew reveal">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7M21 3v6h-6"/></svg></div>
      <h3>Auto-renew</h3>
      <p>Your data plan renews itself before it expires. Set once, never think again.</p>
      <div class="switch" aria-hidden="true"><i></i></div>
    </div>
    <div class="tile t-photo reveal">
      <img src="https://picsum.photos/seed/lagos-street-market/620/860" alt="Busy street scene in Lagos" loading="lazy">
      <div class="cap">{{ strtoupper($businessAddress ?? 'ALL 36 STATES + FCT • SERVED DAILY') }}</div>
    </div>
    <div class="tile t-recv reveal">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2zM9 7h6M9 11h6M9 15h4"/></svg></div>
      <h3>Receipts for everything</h3>
      <p>Every transaction gets an instant, shareable receipt — with reference, token and timestamp.</p>
      <div class="receipt" aria-hidden="true">
        <b>{{ strtoupper($platformName) }} RECEIPT</b>
        MTN AIRTIME ......... ₦1,000.00<br>
        STATUS .............. SUCCESS<br>
        REF ................. ST-88213<br>
        THANK YOU FOR USING {{ strtoupper($platformName) }}
      </div>
    </div>
    <div class="tile t-sec reveal">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z"/><path d="m9 12 2 2 4-4"/></svg></div>
      <h3>Locked down proper</h3>
      <p>Bank-grade encryption, biometric login, and transaction alerts on every movement.</p>
    </div>
    <div class="tile t-support reveal">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M4 13a8 8 0 0 1 16 0M4 13v4a2 2 0 0 0 2 2h1v-6H5a1 1 0 0 0-1 1zm16 0v4a2 2 0 0 1-2 2h-1v-6h2a1 1 0 0 1 1 1zM17 19v1a2 2 0 0 1-2 2h-3"/></svg></div>
      <h3>Humans on WhatsApp, 24/7</h3>
      <p>Average reply time: 3 minutes. Real people, Nigerian time zone, no bots pretending.</p>
    </div>
  </div>
</section>

<!-- ================= TESTIMONIALS ================= -->
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">06 — Word on the street</span>
      <h2>40,000+ resellers.<br>Millions of top-ups.</h2>
    </div>
    <p>From Owerri to Kano, here's what people say when the airtime lands before they finish blinking.</p>
  </div>
  <div class="quotes-wrap">
    <article class="qcard q1 reveal">
      <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
      <blockquote>"I used to close my shop early just to queue at the bank hall for a meter token. Now my meter beeps before I finish counting my change."</blockquote>
      <div class="who"><img src="https://picsum.photos/seed/adaeza-owerri-trader/96/96" alt="Portrait of Adaeze N."><span><b>Adaeze N.</b><span>SHOP OWNER • OWERRI</span></span></div>
    </article>
    <article class="qcard q2 reveal">
      <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
      <blockquote>"My JAMB pin arrived in 10 seconds at 11pm, the night before registration closed. {{ $platformName }} honestly saved my admission."</blockquote>
      <div class="who"><img src="https://picsum.photos/seed/ibrahim-kano-student/96/96" alt="Portrait of Ibrahim S."><span><b>Ibrahim S.</b><span>STUDENT • KANO</span></span></div>
    </article>
    <article class="qcard q3 reveal">
      <div class="stars" aria-label="5 out of 5 stars">★★★★★</div>
      <blockquote>"My WhatsApp data business runs entirely on their API. 300+ orders a day and I haven't scratched a recharge card since 2024."</blockquote>
      <div class="who"><img src="https://picsum.photos/seed/folake-ibadan-reseller/96/96" alt="Portrait of Folake A."><span><b>Folake A.</b><span>DATA RESELLER • IBADAN</span></span></div>
    </article>
    <article class="qcard q4 reveal">
      <div class="stars" aria-label="4 out of 5 stars">★★★★</div>
      <blockquote>"2% off every airtime buy doesn't sound like much until you're topping up three phones daily. Small small, it adds up serious."</blockquote>
      <div class="who"><img src="https://picsum.photos/seed/chinedu-ph-driver/96/96" alt="Portrait of Chinedu O."><span><b>Chinedu O.</b><span>RIDE-HAIL DRIVER • PORT HARCOURT</span></span></div>
    </article>
  </div>
</section>

<!-- ================= PLANS / WALLET TIERS ================= -->
<section class="container" id="plans">
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">07 — Wallet levels</span>
      <h2>Pick your level.<br>Upgrade anytime.</h2>
    </div>
    <p>Start free, move up when your volume does. No monthly charges on any tier — ever.</p>
  </div>
  <div class="plans-grid">
    <!-- Starter Tier -->
    <div class="plan reveal">
      <h3>Smart User</h3>
      <div class="amount">₦0 <small>/ forever</small></div>
      <p class="sub">For personal top-ups and everyday bill payments.</p>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>All VTU services at standard rates</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Instant receipts &amp; auto-refunds</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Dedicated virtual account funding</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>24/7 customer support</li>
      </ul>
      <a class="btn" href="{{ route('register') }}">Create free wallet</a>
    </div>

    <!-- Reseller Tier -->
    <div class="plan pop reveal">
      <span class="pop-tag">MOST POPULAR</span>
      <h3>Reseller Package</h3>
      <div class="amount">₦{{ isset($tiers['reseller_fee']) ? number_format((float)$tiers['reseller_fee']) : '1,500' }} <small>/ one-time</small></div>
      <p class="sub">For vendors, POS points and campus entrepreneurs.</p>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Up to {{ $tiers['reseller_discount'] ?? '3.5' }}% airtime &amp; data margin</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Wholesale price list &amp; dashboard</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Automated 24/7 order dispatch</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Priority WhatsApp support</li>
      </ul>
      <a class="btn btn-gold" href="{{ route('register') }}">Become a reseller</a>
    </div>

    <!-- VIP / Merchant Tier -->
    <div class="plan reveal">
      <h3>VIP Merchant</h3>
      <div class="amount">₦{{ isset($tiers['vip_fee']) ? number_format((float)$tiers['vip_fee']) : '3,500' }} <small>/ one-time</small></div>
      <p class="sub">For high-volume vendors and top-tier distributors.</p>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Max {{ $tiers['vip_discount'] ?? '4.5' }}% airtime &amp; data margin</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Lowest wholesale pricing tier</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Earn ₦{{ isset($tiers['referral_bonus']) ? number_format((float)$tiers['referral_bonus']) : '100' }} per referral</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>Dedicated account manager</li>
      </ul>
      <a class="btn" href="{{ route('register') }}">Upgrade to VIP</a>
    </div>
  </div>
</section>

<!-- ================= FAQ ================= -->
<section class="container" id="faq">
  <div class="sec-head reveal">
    <div>
      <span class="eyebrow">09 — FAQ</span>
      <h2>Questions we hear plenty.</h2>
    </div>
    <p>Straight answers, no wahala. Still confused? Our WhatsApp line is open 24/7.</p>
  </div>
  <div class="faq-list">
    @if(isset($faqs) && count($faqs) > 0)
      @foreach($faqs as $faq)
        <details class="faq-item reveal">
          <summary>{{ $faq->question }}</summary>
          <p class="a">{{ $faq->answer }}</p>
        </details>
      @endforeach
    @else
      <details class="faq-item reveal">
        <summary>How fast is delivery, really?</summary>
        <p class="a">Airtime and data typically land in 5–30 seconds. Electricity tokens and cable renewals usually complete within 60 seconds. We route directly through telco integrations, so there's no third-party queue slowing you down.</p>
      </details>
      <details class="faq-item reveal">
        <summary>What happens if a transaction fails?</summary>
        <p class="a">Your money snaps back to your wallet automatically — no support ticket needed. You'll also get an instant notification with the reference. On the rare occasion a reversal takes longer than 10 minutes, our 24/7 team resolves it with priority.</p>
      </details>
      <details class="faq-item reveal">
        <summary>Which networks and DisCos do you support?</summary>
        <p class="a">All four networks — MTN, Glo, Airtel and 9mobile — plus every major electricity DisCo including Ikeja, Eko, Abuja, Port Harcourt, Ibadan, Kano, Jos, Kaduna, Enugu and Benin. Cable support covers DStv, GOtv, StarTimes and Showmax.</p>
      </details>
      <details class="faq-item reveal">
        <summary>How do I fund my wallet?</summary>
        <p class="a">Every account gets a dedicated virtual account number. Transfer from any Nigerian bank — GTBank, Access, Zenith, Kuda, OPay and more — and your wallet credits automatically in about 30 seconds. Card payments and USSD work too.</p>
      </details>
      <details class="faq-item reveal">
        <summary>Can I resell or build on {{ $platformName }}?</summary>
        <p class="a">Yes. The Reseller tier gives you wholesale margins, a price list and bulk tools for ₦2,000 one-time. The API Merchant tier unlocks our full REST API with webhooks, sub-wallets and up to 8% margin on data plans.</p>
      </details>
    @endif
  </div>
</section>

<!-- ================= DOWNLOAD ================= -->
<section class="container" id="download">
  <div class="dl-panel reveal">
    <div>
      <span class="eyebrow" style="color:var(--ink)">10 — Get the app</span>
      <h2>Carry sharp sharp in your pocket.</h2>
      <div class="pts">
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>FREE TO DOWNLOAD</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>UNDER 8 MB</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>WORKS ON 2G</span>
        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l5 5L20 6"/></svg>USSD FALLBACK</span>
      </div>
      <div class="badges">
        <a class="badge" href="{{ route('register') }}" aria-label="Download on the App Store">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 12.54c-.03-2.5 2.04-3.7 2.13-3.76-1.16-1.7-2.97-1.93-3.61-1.96-1.54-.16-3 .9-3.78.9-.78 0-1.98-.88-3.26-.86-1.68.03-3.22.98-4.08 2.48-1.74 3.02-.44 7.49 1.25 9.94.83 1.2 1.82 2.55 3.12 2.5 1.25-.05 1.72-.8 3.23-.8 1.5 0 1.93.8 3.25.78 1.35-.02 2.2-1.22 3.02-2.43.95-1.39 1.34-2.74 1.36-2.81-.03-.01-2.6-1-2.63-3.98zM14.56 4.6c.69-.83 1.15-1.99 1.02-3.15-.99.04-2.19.66-2.9 1.49-.64.74-1.2 1.92-1.05 3.05 1.1.09 2.24-.57 2.93-1.39z"/></svg>
          <span><small>DOWNLOAD ON THE</small><b>App Store</b></span>
        </a>
        <a class="badge" href="{{ route('register') }}" aria-label="Get it on Google Play">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 3.5v17c0 .38.42.61.74.41l13.45-8.5a.48.48 0 0 0 0-.82L4.74 3.09a.48.48 0 0 0-.74.41zM5.5 5.2l9.3 6.8-2.1 1.3-7.2-8.1zm0 13.6 7.2-8.1 2.1 1.3-9.3 6.8zM16.3 10l2.8 1.77c.3.19.3.62 0 .81L16.3 14l-2.5-2 2.5-2z"/></svg>
          <span><small>GET IT ON</small><b>Google Play</b></span>
        </a>
      </div>
    </div>
    <div class="qr-wrap">
      <div class="qr" aria-hidden="true"><div class="qr-grid"></div></div>
      <p>SCAN TO DOWNLOAD<br>ANDROID 7+ • iOS 13+</p>
    </div>
  </div>
</section>

</main>

<!-- ================= FOOTER ================= -->
<footer>
  <div class="container">
    <div class="f-grid" style="grid-template-columns: 1.4fr 1fr 1fr 1fr;">
      <div class="f-brand">
        <a class="brand" href="#top" style="color:#F6F1E5">
          @if(!empty($logoImage))
            <img src="{{ str_starts_with($logoImage, 'http') ? $logoImage : asset('storage/' . $logoImage) }}" alt="{{ $platformName }}" style="height:36px;max-width:160px;object-fit:contain;">
          @else
            <span class="mark"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0D3226" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8z"/></svg></span>
            {{ $platformName }} <small>VTU</small>
          @endif
        </a>
        <p>Nigeria's fastest virtual top-up wallet. Airtime, data, bills and pins — delivered sharp sharp.</p>
        <div class="f-status"><span class="pulse"></span> ALL SYSTEMS OPERATIONAL</div>
      </div>
      <div>
        <h4>Services</h4>
        <ul>
          <li><a href="#services">Airtime top-up</a></li>
          <li><a href="#rates">Cheap Data bundles</a></li>
          <li><a href="#services">Electricity tokens</a></li>
          <li><a href="#services">Cable TV renewal</a></li>
          <li><a href="#services">Exam result pins</a></li>
        </ul>
      </div>
      <div>
        <h4>Support</h4>
        <ul>
          <li><a href="#faq">Help centre &amp; FAQ</a></li>
          <li><a href="#plans">Reseller Pricing Tiers</a></li>
          <li><a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
          @if(!empty($supportPhone))
            <li><a href="tel:{{ $supportPhone }}">{{ $supportPhone }}</a></li>
          @endif
        </ul>
      </div>
      <div>
        <h4>Legal</h4>
        <ul>
          <li><a href="{{ route('terms') }}">Terms of service</a></li>
          <li><a href="{{ route('privacy') }}">Privacy policy</a></li>
        </ul>
      </div>
    </div>
    <div class="barcode" aria-hidden="true"></div>
    <div class="f-bottom">
      <span>&copy; {{ date('Y') }} {{ strtoupper($platformName) }} • ALL RIGHTS RESERVED</span>
      <span>AUTOMATED VTU PLATFORM</span>
    </div>
  </div>
</footer>

</body>
</html>
