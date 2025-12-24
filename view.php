<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Driving Experience — PHP + MySQL</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='0.9em' font-size='90'%3E%F0%9F%8F%8E%EF%B8%8F%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" type="text/css">

<style>
  :root {
    --bg:rgb(7, 12, 26); --bg-elev:#0e152b; --card:#0f1a33; --fg:#e6e9f2; --muted:#9aa4bf;
    --pri:#7c8cff; --pri-2:#6a7df8; --ok:#22c55e; --warn:#f59e0b; --line:#1a2442;
    --shadow: 0 6px 30px rgba(2,10,35,.28);
    --radius: 14px; --radius-lg: 18px; --blur: 12px;
    --brand-grad: linear-gradient(90deg,#8ea2ff, #7c8cff, #9bd8ff);
  }
  :root.light {
    --bg:#f6f7fb; --bg-elev:#ffffff; --card:#ffffff; --fg:#0e1320; --muted:#5f6b86;
    --pri:#5b6bff; --pri-2:#4d5ef5; --ok:#16a34a; --warn:#d97706; --line:#e8ecf7;
    --shadow: 0 12px 30px rgba(20,20,40,.12);
    --brand-grad: linear-gradient(90deg,#4658ff,#6aa8ff,#86f);
  }
  * { box-sizing: border-box }
  html,body { height:100% }
  body {
    margin:0; font-family: Inter, system-ui, "Segoe UI", Roboto, Arial;
    color:var(--fg);
    background: var(--bg);
    overflow-x:hidden;
  }

  /* ======= BACKGROUND FX ======= */
  .bg-layer {
    position: fixed; inset: 0; z-index: -3;
    background:
      radial-gradient(1200px 600px at 10% -10%, #17255a 0%, transparent 55%),
      radial-gradient(900px 600px at 120% 10%, #1a1c3a 0%, transparent 55%),
      radial-gradient(800px 800px at 50% 50%, rgba(124,140,255,0.03) 0%, transparent 70%),
      var(--bg);
    animation: bgPulse 20s ease-in-out infinite;
  }
  @keyframes bgPulse {
    0%, 100% { opacity: 1 }
    50% { opacity: 0.95 }
  }
  .bg-orb, .bg-orb2 {
    position: fixed; width: 520px; height: 520px; border-radius: 50%;
    filter: blur(50px); opacity: .22; z-index: -2; pointer-events:none;
    background: radial-gradient(closest-side, #7c8cff, transparent 70%);
    animation: floatA 18s ease-in-out infinite;
  }
  .bg-orb2 {
    width: 620px; height: 620px; left: -120px; top: 40%;
    background: radial-gradient(closest-side, #5de1ff, transparent 70%);
    animation: floatB 22s ease-in-out infinite;
  }
  .bg-orb { right: -140px; top: 15% }
  @keyframes floatA {
    0%{ transform: translateY(0) translateX(0) }
    50%{ transform: translateY(30px) translateX(-20px) }
    100%{ transform: translateY(0) translateX(0) }
  }
  @keyframes floatB {
    0%{ transform: translateY(0) translateX(0) }
    50%{ transform: translateY(-35px) translateX(25px) }
    100%{ transform: translateY(0) translateX(0) }
  }
  /* Small twinkling stars */
  .stars { position: fixed; inset:0; z-index:-1; pointer-events:none; }
  .stars::before, .stars::after {
    content:""; position:absolute; inset:0; background:
      radial-gradient(2px 2px at 20% 30%, #fff8, transparent 40%),
      radial-gradient(2px 2px at 70% 60%, #fff5, transparent 40%),
      radial-gradient(1.5px 1.5px at 40% 80%, #fff6, transparent 40%),
      radial-gradient(2px 2px at 85% 25%, #fff7, transparent 40%),
      radial-gradient(1.5px 1.5px at 10% 70%, #fff5, transparent 40%);
    animation: twinkle 5s linear infinite;
  }
  .stars::after { animation-delay: 2.5s; opacity:.6 }
  @keyframes twinkle { 50% { opacity:.2 } }

  /* ======= HERO HEADER ======= */
  .app-header {
    position:sticky; top:0; z-index:50;
    backdrop-filter: blur(20px) saturate(180%);
    background: linear-gradient(180deg, 
      color-mix(in oklab, var(--bg-elev) 85%, transparent),
      color-mix(in oklab, var(--bg-elev) 75%, transparent));
    border-bottom: 1px solid color-mix(in oklab, var(--line) 50%, transparent);
    display:flex; align-items:center; gap:14px; padding:16px 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,.15), 
                0 1px 3px rgba(0,0,0,.1),
                inset 0 -1px 0 rgba(255,255,255,.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .app-header.scrolled {
    padding: 12px 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
  }
  .brand {
    display:flex; align-items:center; gap:10px;
  }
  .brand .logo {
    width:50px; height:36px; border-radius:12px;
    background: var(--brand-grad);
    box-shadow: 0 6px 20px rgba(124,140,255,.45),
                0 0 40px rgba(124,140,255,.2),
                inset 0 1px 0 rgba(255,255,255,.3);
    position: relative;
    animation: logoPulse 3s ease-in-out infinite;
  }
  .brand .logo::before {
    content: "🏎️";
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    font-size: 20px;
    animation: logoDrive 2.6s ease-in-out infinite;
  }
  @keyframes logoPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 6px 20px rgba(124,140,255,.45), 0 0 40px rgba(124,140,255,.2); }
    50% { transform: scale(1.05); box-shadow: 0 8px 30px rgba(124,140,255,.6), 0 0 60px rgba(124,140,255,.3); }
  }
  @keyframes logoDrive {
    0% { transform: translateX(12px) scaleX(1); }
    49% { transform: translateX(-12px) scaleX(1); }
    50% { transform: translateX(-12px) scaleX(-1); }
    99% { transform: translateX(12px) scaleX(-1); }
    100% { transform: translateX(12px) scaleX(1); }
  }
  .brand h1 {
    margin:0; font-size:22px; font-weight:900; letter-spacing:.3px;
    background: var(--brand-grad); -webkit-background-clip:text; background-clip:text; color:transparent;
  }
  .brand small {
    color: var(--muted); font-weight:600; letter-spacing:.2px
  }
  .spacer { flex:1 }
  .header-badges {
    display:flex; gap:12px; align-items:center;
  }
  
  /* ======= NEON GLOW EFFECTS ======= */
  .neon-text {
    animation: neonGlow 2s ease-in-out infinite;
  }
  @keyframes neonGlow {
    0%, 100% { text-shadow: 0 0 10px var(--pri), 0 0 20px var(--pri), 0 0 30px var(--pri); }
    50% { text-shadow: 0 0 20px var(--pri), 0 0 30px var(--pri), 0 0 40px var(--pri), 0 0 50px var(--pri); }
  }
  
  /* Morphing blob background */
  .morph-shape {
    position: fixed;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(124,140,255,0.15), transparent 70%);
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    animation: morph 20s ease-in-out infinite;
    z-index: -1;
    pointer-events: none;
  }
  @keyframes morph {
    0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; transform: translate(0, 0) rotate(0deg); }
    25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; transform: translate(50px, -50px) rotate(90deg); }
    50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; transform: translate(-30px, 30px) rotate(180deg); }
    75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; transform: translate(-50px, -30px) rotate(270deg); }
  }
  
  /* Hover gradient animation */
  .gradient-shift {
    background: linear-gradient(45deg, var(--pri), #5de1ff, var(--pri-2));
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
  }
  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  .badge {
    font-size:12px; padding:6px 14px; border-radius:999px; 
    border:1px solid var(--line);
    background: linear-gradient(135deg, 
                color-mix(in oklab, var(--bg-elev) 90%, transparent),
                color-mix(in oklab, var(--bg-elev) 95%, transparent));
    backdrop-filter: blur(10px);
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }
  .badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(124,140,255,0.3), transparent);
    animation: dataFlow 3s linear infinite;
  }
  @keyframes dataFlow {
    0% { left: -100%; }
    100% { left: 100%; }
  }
  .badge:hover {
    transform: translateY(-3px) scale(1.08);
    box-shadow: 0 6px 20px rgba(124,140,255,.35),
                0 0 30px rgba(124,140,255,.2);
    border-color: var(--pri);
  }
  .icon-btn {
    background: linear-gradient(145deg, var(--bg-elev), color-mix(in oklab, var(--bg-elev) 95%, var(--line)));
    border:1px solid var(--line); color:var(--fg);
    width:40px; height:40px; border-radius:12px; display:grid; place-items:center; cursor:pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0,0,0,.1),
                inset 0 1px 0 rgba(255,255,255,.05);
    position: relative;
    overflow: hidden;
  }
  .icon-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: color-mix(in oklab, var(--pri) 20%, transparent);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }
  .icon-btn:hover::before {
    width: 100px;
    height: 100px;
  }
  .icon-btn:hover { 
    border-color: color-mix(in oklab, var(--pri) 35%, var(--line));
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,.15);
  }
  .icon-btn:active { 
    transform: translateY(-1px) scale(0.98);
  }
  .icon-btn svg {
    position: relative;
    z-index: 1;
  }

  .topbar {
    height:3px; 
    background: linear-gradient(90deg, var(--pri), #5de1ff, var(--pri-2));
    transform: scaleX(0); 
    transform-origin:left; 
    transition: transform .35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 10px rgba(124,140,255,.5);
    position: relative;
  }
  .topbar::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    width: 20px;
    height: 20px;
    background: var(--pri);
    border-radius: 50%;
    transform: translateY(-50%);
    box-shadow: 0 0 20px var(--pri);
  }
  .topbar.active { transform: scaleX(1) }

  /* ======= NAVIGATION TABS ======= */
  .tabs { 
    display:flex; gap:12px; padding:10px 20px 0; max-width:1200px; margin: 0 auto;
    position: relative;
  }
  .tab {
    flex:1; padding:14px 18px; border:1px solid var(--line); border-radius:14px;
    background: linear-gradient(145deg, var(--bg-elev), color-mix(in oklab, var(--bg-elev) 97%, var(--line)));
    color: var(--fg); font-weight:800; cursor:pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }
  .tab::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, color-mix(in oklab, var(--pri) 15%, transparent), transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .tab:hover { 
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
  }
  .tab:hover::before { opacity: 1; }
  .tab.active {
    background: linear-gradient(145deg, 
                color-mix(in oklab, var(--pri) 18%, var(--bg-elev)), 
                color-mix(in oklab, var(--pri) 12%, var(--bg-elev)));
    border-color: color-mix(in oklab, var(--pri) 45%, var(--line));
    box-shadow: inset 0 0 0 1px color-mix(in oklab, var(--pri) 40%, transparent),
                0 10px 30px rgba(124,140,255,.25),
                0 0 50px rgba(124,140,255,.15);
    transform: translateY(-2px);
  }
  .tab.active::before { opacity: 1; }

  .page { display:none; animation: fade .25s ease }
  .page.active { display:block }
  @keyframes fade { from{opacity:.6; transform:translateY(4px)} to{opacity:1; transform:none} }

  .container { 
    max-width:1200px; 
    margin:0 auto; 
    padding:20px; 
    display:grid; 
    gap:24px;
  }
  
  @media (max-width: 768px) {
    .container {
      padding:16px 12px;
    }
  }
  .grid { display:grid; gap:16px }
  @media(min-width:900px){ .grid-2{grid-template-columns:1.1fr .9fr} .grid-3{grid-template-columns:repeat(3,1fr)} }
  
  @media (max-width: 900px) {
    .grid {
      gap:20px;
    }
  }
  
  /* Fieldset styling for clearer grouping */
  fieldset {
    border:none;
    padding:0;
    margin:0 0 0 0;
  }
  
  fieldset legend {
    font-size:16px;
    font-weight:700;
    color:var(--fg);
    margin-bottom:14px;
    padding:0;
  }
  
  /* Screen-reader only */
  .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
    border-width: 0;
  }
  
  /* Touch target improvements */
  @media (max-width: 768px) {
    button, .pill, select {
      min-height:48px;
    }
  }
  
  /* Improved form spacing on mobile */
  @media (max-width: 768px) {
    .grid > .row {
      margin-bottom:8px;
    }
  }

  .card {
    background:
      repeating-linear-gradient(0deg, rgba(255,255,255,.02) 0 1px, transparent 1px 28px),
      linear-gradient(180deg, color-mix(in oklab, var(--card) 95%, transparent), var(--card));
    border:1px solid var(--line); 
    border-radius: var(--radius-lg); 
    padding:28px;
    box-shadow: 0 10px 24px rgba(0,0,0,.18);
    position: relative;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
                transparent, 
                color-mix(in oklab, var(--pri) 8%, transparent),
                transparent);
    transition: left 0.8s ease;
  }
  .card:hover::before {
    left: 100%;
  }
  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 60px rgba(0,0,0,.2),
                0 0 0 1px rgba(255,255,255,.03),
                inset 0 1px 0 rgba(255,255,255,.08);
  }
  
  @media (max-width: 768px) {
    .card {
      padding:20px 16px;
      border-radius:16px;
    }
  }
  
  .card h2 { 
    margin:0 0 20px 0; 
    font-size:22px;
    font-weight:800;
    letter-spacing:0.3px;
  }
  
  @media (max-width: 768px) {
    .card h2 {
      font-size:20px;
      margin-bottom:18px;
    }
  }

  label { 
    font-size:14px; 
    font-weight:600;
    color:var(--muted); 
    display:block; 
    margin-bottom:8px;
    letter-spacing:0.3px;
  }
  
  /* Required-field cue; asterisk now lives in HTML */
  
  input,select {
    width:100%; 
    padding:14px 16px; 
    border-radius:12px; 
    border:1px solid var(--line);
    background: linear-gradient(145deg, var(--bg-elev), color-mix(in oklab, var(--bg-elev) 98%, var(--card)));
    color:var(--fg); 
    font-size:16px;
    font-family: Inter, system-ui, sans-serif;
    line-height:1.5;
    outline:none; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-appearance: none;
    appearance: none;
    position: relative;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
  }
  
  /* Invalid styling for required fields */
  input:invalid:not(:focus):not(:placeholder-shown),
  select:invalid:not(:focus) {
    border-color: color-mix(in oklab, var(--warn) 35%, var(--line));
  }
  
  /* Improved select styling */
  select {
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%239aa4bf' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 40px;
    cursor: pointer;
  }
  
  /* Mobile-specific tweaks */
  @media (max-width: 768px) {
    input, select {
      font-size:16px; /* Stops iOS zoom */
      padding:16px 18px;
      min-height:48px;
    }
    
    label {
      font-size:15px;
      margin-bottom:10px;
    }
    
    select {
      background-position: right 16px center;
      padding-right: 45px;
    }
  }
  
  input:focus, select:focus {
    border-color: var(--pri);
    box-shadow: 0 0 0 4px color-mix(in oklab, var(--pri) 20%, transparent),
                0 0 20px rgba(124,140,255,.3),
                0 8px 20px rgba(0,0,0,.15),
                inset 0 1px 0 rgba(255,255,255,.05);
    transform: translateY(-2px) scale(1.01);
    background: var(--bg-elev);
  }
  input:active, select:active { transform: translateY(-1px) scale(.998) }
  ::placeholder { 
    color: color-mix(in oklab, var(--muted) 75%, transparent);
    font-size:15px;
  }
  .row { display:grid; gap:16px }
  @media(min-width:700px){ 
    .row-2{grid-template-columns:repeat(2,1fr)} 
    .row-3{grid-template-columns:repeat(3,1fr)} 
  }

  .layout {
    max-width:1200px;
    margin:0 auto;
    padding:20px;
    display:grid;
    grid-template-columns: 240px 1fr;
    gap:24px;
    align-items:start;
  }
  .sidebar {
    position: sticky;
    top: 96px;
    display:flex;
    flex-direction:column;
    gap:16px;
  }
  .main-content { position: relative; }
  .sidebar-card {
    background: color-mix(in oklab, var(--card) 92%, transparent);
    border:1px solid var(--line);
    border-radius: var(--radius-lg);
    padding:16px;
    box-shadow: 0 8px 20px rgba(0,0,0,.18);
  }
  .sidebar-title { font-size:13px; letter-spacing:1.4px; text-transform:uppercase; color:var(--muted); }
  .tabs-vertical { display:flex; flex-direction:column; gap:8px; margin-top:12px; width:100%; }
  .tabs-vertical .tab { width:100%; justify-content:flex-start; text-align:left; border-radius: 12px; }
  .sidebar-note { font-size:13px; color:var(--muted); margin-top:10px; }
  @media (max-width: 980px) {
    .layout { grid-template-columns: 1fr; padding:16px 12px; }
    .sidebar { position: static; }
  }

  .form-steps { display:grid; gap:18px; }
  .step {
    border:1px dashed color-mix(in oklab, var(--line) 80%, transparent);
    border-radius: 14px;
    padding:16px;
    background: color-mix(in oklab, var(--bg-elev) 65%, var(--card));
  }
  .step-head {
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:12px;
  }
  .step-index {
    width:32px;
    height:32px;
    border-radius:10px;
    display:grid;
    place-items:center;
    font-weight:700;
    background: var(--brand-grad);
    color:#f3f3f3;
  }
  .step-title { margin:0; font-size:16px; }
  .step-desc { margin:2px 0 0; font-size:13px; color:var(--muted); }
  
  /* Mobile-first: stack on small widths */
  @media (max-width: 699px) {
    .row {
      grid-template-columns: 1fr;
      gap:18px;
    }
  }

  .btn {
    display:inline-flex; 
    gap:10px; 
    align-items:center; 
    justify-content:center;
    background: linear-gradient(135deg, var(--pri), var(--pri-2), #5de1ff);
    background-size: 200% 200%;
    color:white; 
    border:none; 
    padding:16px 28px; 
    border-radius:14px; 
    font-weight:800; 
    font-size:16px;
    cursor:pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    will-change: transform;
    box-shadow: 0 6px 20px color-mix(in oklab, var(--pri) 40%, transparent),
                0 0 40px rgba(124,140,255,.2),
                inset 0 1px 0 rgba(255,255,255,.3);
    min-height:52px;
    width:100%;
    position: relative;
    overflow: hidden;
  }
  .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.4);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }
  .btn:active::before {
    width: 300px;
    height: 300px;
  }
  
  @media(min-width:600px) {
    .btn {
      width:auto;
      min-width:180px;
    }
  }
  
  .btn:hover { 
    filter: saturate(1.1) brightness(1.12);
    transform: translateY(-5px) scale(1.02);
    background-position: 100% 100%;
    box-shadow: 0 12px 40px color-mix(in oklab, var(--pri) 50%, transparent),
                0 0 60px rgba(124,140,255,.4),
                inset 0 1px 0 rgba(255,255,255,.5);
    animation: magneticPulse 0.6s ease-in-out;
  }
  @keyframes magneticPulse {
    0%, 100% { transform: translateY(-5px) scale(1.02); }
    50% { transform: translateY(-7px) scale(1.03); }
  }
  .btn:active { 
    transform: translateY(-2px) scale(0.98);
    box-shadow: 0 4px 15px color-mix(in oklab, var(--pri) 35%, transparent);
  }
  .btn:disabled{ 
    opacity:.6; 
    cursor:not-allowed;
    transform: none !important;
  }

  .kpis { display:grid; gap:14px }
  @media(min-width:700px){ .kpis{ grid-template-columns:repeat(4,1fr) } }
  .kpi {
    padding:18px; border-radius:16px; 
    background: linear-gradient(145deg, var(--bg-elev), color-mix(in oklab, var(--bg-elev) 97%, var(--card)));
    border:1px solid var(--line);
    position:relative; overflow:hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: floatKpi 6s ease-in-out infinite;
  }
  @keyframes floatKpi {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
  }
  .kpi:hover {
    transform: translateY(-8px) scale(1.03) !important;
    box-shadow: 0 15px 40px rgba(124,140,255,.25),
                0 0 60px rgba(124,140,255,.15);
    border-color: color-mix(in oklab, var(--pri) 40%, var(--line));
  }
  .kpi .v { 
    font-size:26px; 
    font-weight:800;
    background: var(--brand-grad);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: -0.5px;
  }
  .kpi .small {
    position: relative;
    z-index: 1;
  }
  .kpi::after {
    content:""; position:absolute; inset:auto -30% 0 -30%; height:3px;
    background: linear-gradient(90deg, transparent, var(--pri), #5de1ff, var(--pri), transparent);
    opacity:.4;
    animation: slideBar 3s linear infinite;
  }
  @keyframes slideBar {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(50%); }
  }
  .kpi::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(124,140,255,0.1), transparent 60%);
    pointer-events: none;
  }

  /* Filters panel */
  .filters-panel {
    background: var(--bg-elev);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .filters-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }
  
  .filters-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
  }
  
  .filter-toggle {
    background: transparent;
    border: 1px solid var(--line);
    padding: 6px 12px;
    border-radius: 8px;
    color: var(--fg);
    cursor: pointer;
    font-size: 13px;
    transition: all .2s ease;
  }
  
  .filter-toggle:hover {
    background: var(--line);
  }
  
  .filters-content {
    display: grid;
    gap: 12px;
  }
  
  @media(min-width: 900px) {
    .filters-content {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  
  .filter-group label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
  }
  
  .filter-actions {
    display: flex;
    gap: 10px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--line);
  }
  
  .btn-sm {
    padding: 8px 16px;
    font-size: 14px;
    min-height: auto;
    width: auto;
  }
  
  .filter-results {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 12px;
    font-weight: 600;
  }

  table { width:100%; border-collapse:separate; border-spacing:0 6px }
  
  @media(max-width: 768px) {
    table {
      display: none;
    }
  }
  
  thead th {
    font-size:12px; color:var(--muted); text-align:left; padding:6px 10px 8px;
    user-select:none; cursor:pointer;
  }
  tbody td {
    background: var(--bg-elev); border-top:1px solid var(--line); border-bottom:1px solid var(--line);
    padding:12px 10px; font-size:14px;
  }
  tbody tr { transition: transform .08s ease, box-shadow .2s ease; }
  tbody tr:hover { transform: translateY(-1px); box-shadow: 0 8px 16px rgba(0,0,0,.12) }
  tbody tr td:first-child { border-left:1px solid var(--line); border-top-left-radius: 12px; border-bottom-left-radius: 12px }
  tbody tr td:last-child  { border-right:1px solid var(--line); border-top-right-radius:12px; border-bottom-right-radius:12px }
  
  /* Mobile card view */
  .trips-cards {
    display: none;
  }
  
  @media(max-width: 768px) {
    .trips-cards {
      display: grid;
      gap: 16px;
    }
  }
  
  .trip-card {
    background: var(--bg-elev);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 16px;
    transition: all .2s ease;
  }
  
  .trip-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
  }
  
  .trip-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--line);
  }
  
  .trip-card-date {
    font-size: 16px;
    font-weight: 700;
    color: var(--pri);
  }
  
  .trip-card-km {
    font-size: 18px;
    font-weight: 800;
    color: var(--ok);
  }
  
  .trip-card-body {
    display: grid;
    gap: 10px;
  }
  
  .trip-card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
  }
  
  .trip-card-label {
    color: var(--muted);
    font-weight: 600;
  }
  
  .trip-card-value {
    color: var(--fg);
    font-weight: 500;
    text-align: right;
  }
  
  .trip-card-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--line);
    justify-content: flex-end;
  }

  .pill { 
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:10px 14px; 
    border-radius:10px; 
    background: color-mix(in oklab, var(--pri) 8%, var(--bg-elev));
    border:2px solid color-mix(in oklab, var(--pri) 20%, var(--line)); 
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition: all .2s ease;
    user-select: none;
    position: relative;
  }
  
  .pill:hover {
    background: color-mix(in oklab, var(--pri) 15%, var(--bg-elev));
    transform: translateY(-1px);
    border-color: color-mix(in oklab, var(--pri) 35%, var(--line));
  }
  
  /* Checked state: visually distinct */
  .pill:has(input[type="checkbox"]:checked) {
    background: linear-gradient(135deg, color-mix(in oklab, var(--pri) 85%, var(--bg-elev)), color-mix(in oklab, var(--pri) 75%, var(--bg-elev)));
    border-color: var(--pri);
    color: white;
    font-weight: 600;
    box-shadow: 0 2px 12px color-mix(in oklab, var(--pri) 40%, transparent);
  }
  
  .pill:has(input[type="checkbox"]:checked):hover {
    background: linear-gradient(135deg, color-mix(in oklab, var(--pri) 90%, var(--bg-elev)), color-mix(in oklab, var(--pri) 80%, var(--bg-elev)));
    box-shadow: 0 4px 16px color-mix(in oklab, var(--pri) 50%, transparent);
  }
  
  .pill input[type="checkbox"] {
    width:20px;
    height:20px;
    min-height:auto;
    margin:0;
    padding:0;
    cursor:pointer;
    accent-color: var(--pri);
    border-radius: 4px;
  }
  
  /* Custom checkbox styling for visibility */
  .pill input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    border: 2px solid color-mix(in oklab, var(--muted) 60%, transparent);
    background: var(--bg-elev);
    border-radius: 4px;
    display: grid;
    place-content: center;
    position: relative;
  }
  
  .pill input[type="checkbox"]:checked {
    background: white;
    border-color: white;
  }
  
  .pill input[type="checkbox"]:checked::before {
    content: "";
    width: 10px;
    height: 10px;
    background: var(--pri);
    clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
    transform: scale(1);
  }
  
  @media (max-width: 768px) {
    .pill {
      padding:12px 16px;
      font-size:16px;
      gap:12px;
    }
    
    .pill input[type="checkbox"] {
      width:22px;
      height:22px;
    }
    
    .pill input[type="checkbox"]:checked::before {
      width: 11px;
      height: 11px;
    }
  }
  
  .chips { 
    display:flex; 
    flex-wrap:wrap; 
    gap:10px;
  }
  
  @media (max-width: 768px) {
    .chips {
      gap:12px;
    }
  }
  .small { 
    font-size:14px; 
    color:var(--muted);
    line-height:1.6;
  }
  
  @media (max-width: 768px) {
    .small {
      font-size:15px;
    }
  }

  .skeleton { position:relative; overflow:hidden; background: color-mix(in oklab, var(--bg-elev) 85%, var(--line)); border-radius: 10px; min-height:42px }
  .skeleton::after {
    content:""; position:absolute; inset:0; transform: translateX(-100%);
    background: linear-gradient(90deg, transparent, color-mix(in oklab, #fff 12%, transparent), transparent);
    animation: shimmer 1.2s infinite;
  }
  @keyframes shimmer { 100% { transform: translateX(100%) } }

  .toast {
    position: fixed; right: 18px; bottom: 18px; min-width: 240px; max-width: 60ch;
    padding: 12px 14px; border-radius: 12px; background: var(--bg-elev); border: 1px solid var(--line);
    box-shadow: var(--shadow); opacity:0; transform: translateY(10px); pointer-events:none;
    transition: opacity .25s ease, transform .25s ease; z-index:1000;
  }
  .toast.show { opacity:1; transform: translateY(0) }
  .fade-in { animation: fade .25s ease }

  /* Table action buttons */
  .action-btns {
    display:flex; gap:6px; justify-content:flex-end;
  }
  .icon-btn-sm {
    background: var(--bg-elev); border:1px solid var(--line); color:var(--fg);
    width:32px; height:32px; border-radius:8px; display:grid; place-items:center; cursor:pointer;
    transition: all .2s ease;
  }
  .icon-btn-sm:hover { 
    border-color: color-mix(in oklab, var(--pri) 35%, var(--line));
    transform: translateY(-2px);
  }
  .icon-btn-sm.delete:hover { 
    border-color: color-mix(in oklab, var(--warn) 50%, var(--line));
    background: color-mix(in oklab, var(--warn) 10%, var(--bg-elev));
  }
  .icon-btn-sm:active { transform: translateY(0px) }

  /* Modal backdrop */
  .modal-overlay {
    position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px);
    display:none; align-items:center; justify-content:center; z-index:999;
    animation: fadeIn .2s ease;
  }
  .modal-overlay.show { display:flex }
  @keyframes fadeIn { from{opacity:0} to{opacity:1} }

  .modal {
    background: var(--card); border:1px solid var(--line); border-radius:var(--radius-lg);
    padding:28px; max-width:800px; width:90%; max-height:85vh; overflow-y:auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
    animation: slideUp .25s ease;
  }
  @keyframes slideUp { from{transform:translateY(20px); opacity:0} to{transform:none; opacity:1} }

  .modal-header {
    display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;
  }
  .modal-header h3 { margin:0; font-size:20px; font-weight:800 }
  .modal-close {
    background:transparent; border:none; color:var(--muted); cursor:pointer;
    font-size:28px; line-height:1; padding:0; width:32px; height:32px;
    display:grid; place-items:center; border-radius:8px;
    transition: all .2s ease;
  }
  .modal-close:hover { background:var(--bg-elev); color:var(--fg) }

  .modal-actions {
    display:flex; gap:12px; margin-top:24px; justify-content:flex-end;
  }
  .btn-secondary {
    background: var(--bg-elev); color:var(--fg); border:1px solid var(--line);
  }
  .btn-secondary:hover { background: var(--line) }

  /* Summary charts grid */
  .charts-grid { display:grid; gap:16px }
  @media(min-width:900px){ .charts-grid { grid-template-columns: repeat(3, 1fr) } }
  .chart-card { padding:12px; border:1px solid var(--line); border-radius:14px; background: var(--bg-elev) }
  .chart-title { font-size:13px; color:var(--muted); margin: 0 0 8px 4px }
  
  /* Page footer */
  .app-footer {
    margin-top: 30px;
    padding: 20px 20px 10px;
    background: linear-gradient(180deg,
                color-mix(in oklab, var(--bg-elev) 90%, transparent),
                color-mix(in oklab, var(--bg-elev) 95%, transparent));
    border-top: 1px solid color-mix(in oklab, var(--line) 50%, transparent);
    backdrop-filter: blur(20px) saturate(180%);
    position: relative;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.03),
                0 -10px 40px rgba(0,0,0,.1);
  }
  .app-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
                transparent,
                var(--pri),
                #5de1ff,
                var(--pri),
                transparent);
  }
  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    align-items: start;
  }
  .footer-section {
    animation: fadeInUp 0.6s ease-out;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .footer-section h3 {
    font-size: 14px;
    font-weight: 800;
    margin: 0 0 10px 0;
    background: var(--brand-grad);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    letter-spacing: 0.5px;
  }
  .footer-info p {
    margin: 5px 0;
    color: var(--muted);
    font-size: 13px;
    line-height: 1.5;
  }
  .footer-info strong {
    color: var(--fg);
    font-weight: 700;
  }
  .footer-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .footer-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: color-mix(in oklab, var(--bg-elev) 50%, transparent);
    border-radius: 10px;
    border: 1px solid var(--line);
    transition: all 0.3s ease;
  }
  .footer-stat-item:hover {
    transform: translateX(5px);
    background: color-mix(in oklab, var(--pri) 10%, var(--bg-elev));
    border-color: color-mix(in oklab, var(--pri) 30%, var(--line));
  }
  .footer-stat-icon {
    font-size: 20px;
  }
  .footer-stat-value {
    font-size: 15px;
    color: var(--pri);
    font-weight: 700;
  }
  .footer-social {
    display: flex;
    gap: 10px;
    margin-top: 12px;
  }
  .social-link {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: linear-gradient(145deg, var(--bg-elev), color-mix(in oklab, var(--bg-elev) 95%, var(--line)));
    border: 1px solid var(--line);
    display: grid;
    place-items: center;
    color: var(--fg);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
    position: relative;
    overflow: hidden;
  }
  .social-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--brand-grad);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .social-link svg {
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
  }
  .social-link:hover {
    transform: translateY(-5px) scale(1.1);
    box-shadow: 0 8px 20px rgba(124,140,255,.4);
    border-color: var(--pri);
  }
  .social-link:hover::before {
    opacity: 1;
  }
  .social-link:hover svg {
    filter: brightness(0) invert(1);
    transform: scale(1.1);
  }
  .footer-bottom {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid var(--line);
    text-align: center;
    color: var(--muted);
    font-size: 11px;
    line-height: 1.3;
  }
  .footer-bottom a {
    color: var(--pri);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .footer-bottom a:hover {
    color: var(--pri-2);
    text-decoration: underline;
  }
  @media (max-width: 768px) {
    .footer-content {
      grid-template-columns: 1fr;
      gap: 20px;
      text-align: center;
    }
    .footer-social {
      justify-content: center;
    }
  }
  
  /* DataTables custom styling */
  .dataTables_wrapper {
    color: var(--fg);
    font-family: Inter, system-ui, sans-serif;
  }
  
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter,
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_paginate {
    color: var(--muted);
  }
  
  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select {
    background: var(--bg-elev);
    border: 1px solid var(--line);
    color: var(--fg);
    border-radius: 8px;
    padding: 6px 10px;
  }
  
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    background: var(--bg-elev);
    border: 1px solid var(--line);
    color: var(--fg) !important;
    border-radius: 6px;
    padding: 4px 10px;
    margin: 0 2px;
  }
  
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--line) !important;
    border: 1px solid var(--line) !important;
    color: var(--fg) !important;
  }
  
  .dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(180deg, var(--pri), var(--pri-2)) !important;
    border: 1px solid var(--pri) !important;
    color: white !important;
  }
  
  .dt-buttons {
    margin-bottom: 12px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  
  .dt-button {
    background: var(--bg-elev) !important;
    border: 1px solid var(--line) !important;
    color: var(--fg) !important;
    padding: 8px 14px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    transition: all .2s ease !important;
  }
  
  .dt-button:hover {
    background: var(--line) !important;
    transform: translateY(-1px);
  }
  
  table.dataTable thead th {
    background: var(--bg-elev);
    color: var(--fg);
    font-weight: 700;
    border-bottom: 2px solid var(--line);
  }
  
  table.dataTable.stripe tbody tr.odd,
  table.dataTable.display tbody tr.odd {
    background-color: var(--bg-elev);
  }
  
  table.dataTable.stripe tbody tr.even,
  table.dataTable.display tbody tr.even {
    background-color: var(--card);
  }
  
  table.dataTable tbody tr:hover {
    background-color: color-mix(in oklab, var(--pri) 10%, var(--bg-elev)) !important;
  }
  
  /* Fix DataTables Buttons CSS parse errors (strip IE filters) */
  div.dt-buttons > .dt-button,
  div.dt-buttons > div.dt-button-split .dt-button {
    filter: none !important;
  }
  
  div.dt-buttons > .dt-button.dt-button-active:not(.disabled),
  div.dt-buttons > div.dt-button-split .dt-button.dt-button-active:not(.disabled) {
    filter: none !important;
  }
  
  div.dt-buttons > .dt-button.dt-button-active:not(.disabled):hover:not(.disabled),
  div.dt-buttons > div.dt-button-split .dt-button.dt-button-active:not(.disabled):hover:not(.disabled) {
    filter: none !important;
  }
  
  div.dt-buttons > .dt-button:hover:not(.disabled),
  div.dt-buttons > div.dt-button-split .dt-button:hover:not(.disabled) {
    filter: none !important;
  }

  /* ======= ATELIER THEME OVERRIDES ======= */
  :root {
    --bg:#0e1211; --bg-elev:#141a18; --card:#19201d; --fg:#f2f4f1; --muted:#aeb6b1;
    --pri:#0f4c5c; --pri-2:#173b4a; --ok:#2da86a; --warn:#c56b2b; --line:#26302c;
    --shadow: 0 18px 40px rgba(8,10,8,.35);
    --radius: 16px; --radius-lg: 22px; --blur: 8px;
    --brand-grad: linear-gradient(120deg,#0f4c5c,#0f3b4a,#19323a);
  }
  :root.light {
    --bg:#edf1f0; --bg-elev:#f7f9f8; --card:#ffffff; --fg:#1d2321; --muted:#5a6460;
    --pri:#0f4c5c; --pri-2:#1a3f4a; --ok:#2e9b64; --warn:#b85f22; --line:#d8e0dc;
    --shadow: 0 14px 30px rgba(40,30,20,.15);
    --brand-grad: linear-gradient(120deg,#0f4c5c,#1a3f4a,#244b57);
  }
  body {
    font-family: "Space Grotesk", "IBM Plex Sans", sans-serif;
    letter-spacing: .2px;
    background:
      radial-gradient(1000px 700px at 15% -5%, rgba(15,76,92,.18), transparent 62%),
      radial-gradient(900px 700px at 110% 20%, rgba(23,59,74,.22), transparent 60%),
      linear-gradient(180deg, #0c1110 0%, #121816 45%, #0a0f0e 100%);
  }
  input, select, textarea { font-family: "Space Grotesk", "IBM Plex Sans", sans-serif; }
  .dataTables_wrapper { font-family: "Space Grotesk", "IBM Plex Sans", sans-serif; }
  h1, h2, h3, .brand h1 { font-family: "Fraunces", "Space Grotesk", serif; }
  .bg-layer {
    background:
      radial-gradient(900px 560px at 12% 10%, rgba(15,76,92,.2), transparent 60%),
      radial-gradient(820px 640px at 85% 35%, rgba(23,59,74,.18), transparent 62%),
      repeating-linear-gradient(135deg, rgba(255,255,255,.02) 0 2px, transparent 2px 6px),
      linear-gradient(180deg, var(--bg), color-mix(in oklab, var(--bg) 85%, #070a09));
    animation: sunrisePulse 18s ease-in-out infinite;
  }
  @keyframes sunrisePulse {
    0%,100% { opacity: 1 }
    50% { opacity: .92 }
  }
  .app-header {
    background: linear-gradient(180deg,
      color-mix(in oklab, var(--bg-elev) 90%, #f8e9d3),
      color-mix(in oklab, var(--bg-elev) 78%, transparent));
    border-bottom: 1px solid var(--line);
    box-shadow: var(--shadow);
  }
  .brand .logo {
    border-radius: 12px;
    background: var(--brand-grad);
    box-shadow: 0 10px 30px rgba(224,122,31,.35);
  }
  .brand-copy { display:flex; flex-direction:column; gap:2px; }
  .brand-sub {
    margin:0;
    font-size:11px;
    text-transform: uppercase;
    letter-spacing: 1.6px;
    color: var(--muted);
  }
  .header-actions { display:flex; gap:12px; align-items:center; }
  .nav-shell {
    max-width:1200px;
    margin: 16px auto 0;
    padding: 0 20px;
    display:flex;
    align-items:center;
    justify-content: space-between;
    gap:16px;
    flex-wrap:wrap;
  }
  .nav-title { display:flex; flex-direction:column; gap:4px; }
  .nav-eyebrow { font-size:11px; letter-spacing:1.6px; text-transform:uppercase; color:var(--muted); }
  .nav-head { margin:0; font-size:18px; }
  .tabs {
    width: 100%;
    max-width: none;
    margin: 0;
    display:flex;
    align-items:center;
    gap:8px;
    background: color-mix(in oklab, var(--bg-elev) 80%, transparent);
    border:1px solid var(--line);
    border-radius: 999px;
    padding:6px;
    box-shadow: var(--shadow);
    box-sizing: border-box;
    overflow: hidden;
  }
  .nav-shell .tabs { width: auto; }
  .tab {
    flex: 0 0 auto;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    line-height:1;
    border-radius: 999px;
    border: 1px solid transparent;
    background: transparent;
    color: var(--muted);
    padding:10px 16px;
  }
  .tab.active {
    background: var(--brand-grad);
    color: #2b1a0f;
    border-color: color-mix(in oklab, var(--pri) 60%, #fff);
  }
  .hero-card { position: relative; overflow: hidden; }
  .hero-card::after {
    content: "";
    position: absolute;
    inset: -30% -10% auto auto;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(15,76,92,.2), transparent 70%);
    pointer-events: none;
  }
  .hero-pill {
    display:inline-flex;
    align-items:center;
    gap:8px;
    border:1px dashed var(--line);
    padding:6px 10px;
    border-radius:999px;
    font-size:12px;
    color:var(--muted);
  }
  .hero-title { font-size:28px; margin:16px 0 8px; }
  .hero-sub { margin:0 0 16px; }
  .hero-points { margin:0; padding-left:18px; color:var(--muted); line-height:1.7; }
  .hero-metrics {
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:10px;
    margin-top:16px;
  }
  .mini {
    border:1px solid var(--line);
    border-radius:12px;
    padding:10px 12px;
    background: color-mix(in oklab, var(--bg-elev) 60%, var(--card));
  }
  .mini-label { font-size:12px; color:var(--muted); }
  .mini-value { font-weight:700; }
  .section-head {
    display:flex;
    align-items:flex-start;
    justify-content: space-between;
    gap:16px;
    margin-bottom: 18px;
    flex-wrap: wrap;
  }
  .section-desc { margin:6px 0 0; color:var(--muted); }
  .filters-panel { background: color-mix(in oklab, var(--bg-elev) 65%, var(--card)); }
  .app-footer h3 { font-family: "Fraunces", "Space Grotesk", serif; }
  @media (max-width: 900px) {
    .hero-metrics { grid-template-columns: 1fr; }
  }
</style>

<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="css/datatables.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<!-- DataTables Responsive Extension -->
<link rel="stylesheet" type="text/css" href="css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- DataTables Buttons Extension (for export) -->
<link rel="stylesheet" type="text/css" href="css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
</head>
<body>

<div class="bg-layer"></div>
<div class="bg-orb"></div>
<div class="bg-orb2"></div>
<div class="stars"></div>

<header class="app-header" id="mainHeader">
  <div class="brand">
    <div class="logo" aria-hidden="true"></div>
    <div class="brand-copy">
      <h1>Driving Experience</h1>
      <p class="brand-sub">Driving logbook + conditions studio</p>
    </div>
  </div>
  
  <div class="spacer"></div>
  <div class="header-actions">
    <div class="header-badges">
      <span class="badge" id="hdrTotalKm">0.0 km total</span>
      <span class="badge" id="hdrAvgSpeed">0 km/h avg</span>
    </div>
    <button class="icon-btn" id="themeBtn" aria-label="Toggle theme" title="Toggle theme">
      <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 1 0 9.79 9.79z" fill="currentColor"/></svg>
    </button>
  </div>
</header>

<div class="morph-shape" style="top: 10%; left: 5%;"></div>
<div class="morph-shape" style="bottom: 15%; right: 8%; animation-delay: -5s;"></div>

<div class="nav-shell">
  <div class="nav-title">
    <span class="nav-eyebrow">Workspace</span>
    <h2 class="nav-head">Plan / Log / Review</h2>
  </div>
  <nav class="tabs">
    <button class="tab active" data-target="page-dashboard">Dashboard</button>
    <button class="tab" data-target="page-summary">Summary</button>
    <button class="tab" data-target="page-trips">Trips</button>
  </nav>
</div>

<div id="topbarProgress" class="topbar"></div>

<main>
<!-- DASHBOARD: form only -->
<article id="page-dashboard" class="page active">
  <h2 class="sr-only">Dashboard</h2>
  <div class="container">
    <section class="card">
      <div class="section-head" style="margin-bottom:14px">
        <div>
          <h2 style="margin:0">New Trip Log</h2>
          <p class="section-desc">Keep entries clear and consistent.</p>
        </div>
        <span class="badge">Quick entry</span>
      </div>
      <form class="grid form-steps" id="tripForm">
          <div class="step">
            <div class="step-head">
              <div class="step-index">01</div>
              <div>
                <h4 class="step-title">Timing</h4>
                <p class="step-desc">Set the date and travel window.</p>
              </div>
            </div>
            <fieldset class="row row-3">
              <legend class="sr-only">Trip Date and Times</legend>
              <div>
                <label for="date">🗓️ Date *</label>
                <input type="date" id="date" required>
              </div>
              <div>
                <label for="dep">⏱️ Departure Time *</label>
                <input type="time" id="dep" required>
              </div>
              <div>
                <label for="arr">⏰ Arrival Time *</label>
                <input type="time" id="arr" required>
              </div>
            </fieldset>
          </div>

          <div class="step">
            <div class="step-head">
              <div class="step-index">02</div>
              <div>
                <h4 class="step-title">Essentials</h4>
                <p class="step-desc">Distance, weather, and time of day.</p>
              </div>
            </div>
            <fieldset class="row row-3">
              <legend class="sr-only">Primary Trip Details</legend>
              <div>
                <label for="km">📏 Distance (km) *</label>
                <input type="number" id="km" inputmode="decimal" step="0.1" min="0" placeholder="e.g. 12.5" required>
              </div>
              <div>
                <label for="weather">☁️ Weather Conditions *</label>
                <select id="weather" required><option value="">Select weather...</option></select>
              </div>
              <div>
                <label for="tod">🌞 Time of Day *</label>
                <select id="tod" required><option value="">Select time...</option></select>
              </div>
            </fieldset>
          </div>

          <div class="step">
            <div class="step-head">
              <div class="step-index">03</div>
              <div>
                <h4 class="step-title">Conditions</h4>
                <p class="step-desc">Surface, road, and driver health.</p>
              </div>
            </div>
            <fieldset class="row row-3">
              <legend class="sr-only">Road and Surface Conditions</legend>
              <div>
                <label for="surface">🧱 Surface Condition</label>
                <select id="surface"></select>
              </div>
              <div>
                <label for="road">🛣️ Road Condition</label>
                <select id="road"></select>
              </div>
              <div>
                <label for="health">🩺 Driver Health Status</label>
                <select id="health"></select>
              </div>
            </fieldset>
          </div>

          <div class="step">
            <div class="step-head">
              <div class="step-index">04</div>
              <div>
                <h4 class="step-title">Context</h4>
                <p class="step-desc">Maneouvring and GPS details.</p>
              </div>
            </div>
            <fieldset class="row row-2">
              <legend class="sr-only">Additional Information</legend>
              <div>
                <label>🎯 Maneouvring (select multiple)</label>
                <div id="factors" class="chips" role="group" aria-label="Maneouvring"></div>
              </div>
              <div>
                <label for="lat">🧭 GPS Coordinates (optional)</label>
                <div class="row row-2">
                  <input type="number" id="lat" step="0.000001" placeholder="Latitude" aria-label="Latitude">
                  <input type="number" id="lng" step="0.000001" placeholder="Longitude" aria-label="Longitude">
                </div>
              </div>
            </fieldset>
          </div>

          <div>
            <button type="button" id="saveBtn" class="btn" aria-label="Save trip">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
              </svg>
              Save Trip
            </button>
            <div id="msg" class="small" style="margin-top:12px; min-height:24px;"></div>
          </div>
        </form>
    </section>
  </div>
</article>

<!-- SUMMARY: KPIs + Charts -->
<article id="page-summary" class="page">
  <h2 class="sr-only">Summary and Statistics</h2>
  <div class="container">
    <section class="card">
      <div class="section-head">
        <div>
          <h2 style="margin-top:0">Pulse Overview</h2>
          <p class="section-desc">Totals and a first glance at conditions.</p>
        </div>
        <span class="badge">Auto-updates</span>
      </div>
      <div class="kpis" id="kpiWrap">
        <div class="kpi"><div class="small">Total Trips</div><div class="v" id="k_trips">0</div></div>
        <div class="kpi"><div class="small">Total Kilometers</div><div class="v" id="k_km">0.0</div></div>
        <div class="kpi"><div class="small">Total Hours</div><div class="v" id="k_hours">0:00</div></div>
        <div class="kpi"><div class="small">Avg km / Trip</div><div class="v" id="k_avg">0.0</div></div>
      </div>
      <div class="chart-card" style="margin-top:14px">
        <p class="chart-title">Weather Distribution</p>
        <canvas id="chart_weather" height="220"></canvas>
      </div>
    </section>

    <section class="card">
      <div class="section-head">
        <div>
          <h2 style="margin-top:0">Insights & Trends</h2>
          <p class="section-desc">Highlights that stand out across your trips.</p>
        </div>
        <span class="badge">Deep dive</span>
      </div>
      <div class="kpis" style="margin-bottom:12px">
        <div class="kpi"><div class="small">Most Common Weather</div><div class="v" id="k_common_weather">—</div></div>
        <div class="kpi"><div class="small">Peak Time of Day</div><div class="v" id="k_peak_tod">—</div></div>
        <div class="kpi"><div class="small">Longest Trip (km)</div><div class="v" id="k_long_km">0.0</div></div>
        <div class="kpi"><div class="small">Longest Duration</div><div class="v" id="k_long_dur">0:00</div></div>
      </div>

      <div class="charts-grid">
        <div class="chart-card">
          <p class="chart-title">Time of Day</p>
          <canvas id="chart_tod" height="160"></canvas>
        </div>
        <div class="chart-card">
          <p class="chart-title">Surface Conditions</p>
          <canvas id="chart_surface" height="160"></canvas>
        </div>
        <div class="chart-card">
          <p class="chart-title">Road Conditions</p>
          <canvas id="chart_road" height="160"></canvas>
        </div>
        <div class="chart-card">
          <p class="chart-title">Driver Health</p>
          <canvas id="chart_health" height="160"></canvas>
        </div>
        <div class="chart-card">
          <p class="chart-title">Maneouvring (Top)</p>
          <canvas id="chart_external" height="160"></canvas>
        </div>
        <div class="chart-card">
          <p class="chart-title">Kilometers by Date</p>
          <canvas id="chart_km_by_date" height="160"></canvas>
        </div>
      </div>
    </section>
  </div>
</article>

<!-- TRIPS: table + facts derived from rows -->
<article id="page-trips" class="page">
  <h2 class="sr-only">All Trips</h2>
  <div class="container">
    <section class="card">
      <div class="section-head">
        <div>
          <h2 style="margin-top:0">Trip Archive</h2>
          <p class="section-desc">Filter and review every recorded drive.</p>
        </div>
        <span class="badge">Searchable logbook</span>
      </div>
      
      <!-- Filters Panel -->
      <div class="filters-panel">
        <div class="filters-header">
          <h3>Filter & Search</h3>
          <button class="filter-toggle" onclick="toggleFilters()" id="filterToggleBtn">Hide Filters</button>
        </div>
        
        <div class="filters-content" id="filtersContent">
          <div class="filter-group">
            <label for="filterSearch">🔍 Search (any field)</label>
            <input type="text" id="filterSearch" placeholder="Search trips...">
          </div>
          
          <div class="filter-group">
            <label for="filterDateFrom">🗓️ From Date</label>
            <input type="date" id="filterDateFrom">
          </div>
          
          <div class="filter-group">
            <label for="filterDateTo">🗓️ To Date</label>
            <input type="date" id="filterDateTo">
          </div>
          
          <div class="filter-group">
            <label for="filterWeather">☁️ Weather</label>
            <select id="filterWeather">
              <option value="">All Weather</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filterTimeOfDay">🌞 Time of Day</label>
            <select id="filterTimeOfDay">
              <option value="">All Times</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filterMinKm">📏 Min Km</label>
            <input type="number" id="filterMinKm" step="0.1" min="0" placeholder="0">
          </div>
          
          <div class="filter-group">
            <label for="filterMaxKm">📏 Max Km</label>
            <input type="number" id="filterMaxKm" step="0.1" min="0" placeholder="Any">
          </div>
          
          <div class="filter-group">
            <label for="filterSurface">🧱 Surface</label>
            <select id="filterSurface">
              <option value="">All Surfaces</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="filterRoad">🛣️ Road</label>
            <select id="filterRoad">
              <option value="">All Roads</option>
            </select>
          </div>
        </div>
        
        <div class="filter-actions">
          <button class="btn btn-sm btn-secondary" onclick="clearFilters()">Clear All</button>
          <button class="btn btn-sm" onclick="applyFilters()">Apply Filters</button>
        </div>
        
        <div style="margin-top:12px; font-size:13px; color:var(--muted); font-style:italic;">
          Before proceeding, please clear all filters.
        </div>
      </div>
      
      <div class="filter-results" id="filterResults"></div>

      <div class="kpis" id="tripFacts" style="margin-bottom:12px">
        <div class="kpi"><div class="small">Distinct Days</div><div class="v" id="fact_days">0</div></div>
        <div class="kpi"><div class="small">Night Trips (%)</div><div class="v" id="fact_night">0%</div></div>
        <div class="kpi"><div class="small">Trips ≥ 50 km</div><div class="v" id="fact_50km">0</div></div>
        <div class="kpi"><div class="small">Most Used Road Cond.</div><div class="v" id="fact_top_road">—</div></div>
      </div>

      <table id="tbl">
        <thead>
          <tr>
            <th>Date</th><th>Dep</th><th>Arr</th><th>Hours</th><th>Km</th>
            <th>Weather</th><th>Time</th><th>Surface</th><th>Road</th><th>Health</th><th>Factors</th><th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      
      <!-- Mobile Card View -->
      <div class="trips-cards" id="tripsCards"></div>
    </section>
  </div>
</article>
</main>

<footer class="app-footer">
  <div class="footer-content">
    <div class="footer-section footer-info">
      <h3>🚗 Driving Experience</h3>
      <p><strong>Crafted trip journaling</strong></p>
      <p>Track routes, conditions, and maneouvring with a studio-style dashboard.</p>
      <p style="font-size:12px;opacity:0.7;margin-top:12px">
        🔒 Session: <?php echo substr($_SESSION['user_id'], 0, 8); ?>...<br>
        🕒 Started: <?php echo date('H:i', $_SESSION['session_start_time']); ?>
      </p>
      
      <div class="footer-social">
        <a href="https://github.com/Kamrann191/backend-hwp" class="social-link" title="GitHub" aria-label="GitHub" target="_blank" rel="noopener noreferrer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/>
          </svg>
        </a>
        <a href="#" class="social-link" title="Twitter" aria-label="Twitter">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
          </svg>
        </a>
        <a href="https://www.linkedin.com/in/kamran-guliyev-363b41347/" class="social-link" title="LinkedIn" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/>
            <circle cx="4" cy="4" r="2"/>
          </svg>
        </a>
        <a href="#" class="social-link" title="Email" aria-label="Email">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </a>
      </div>
    </div>
    
    <div class="footer-section">
      <h3>📊 Session Snapshot</h3>
      <div class="footer-stats">
        <div class="footer-stat-item">
          <span class="footer-stat-icon">🚗</span>
          <span class="footer-stat-value" id="footerTrips">0 trips</span>
        </div>
        <div class="footer-stat-item">
          <span class="footer-stat-icon">🛣️</span>
          <span class="footer-stat-value" id="footerKm">0 km</span>
        </div>
        <div class="footer-stat-item">
          <span class="footer-stat-icon">⏱️</span>
          <span class="footer-stat-value" id="footerHours">0 hours</span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p>© 2025 <strong>Driving Experience</strong> | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
  </div>
</footer>

<div id="toast" class="toast" role="status" aria-live="polite"></div>

<!-- Edit Trip Modal -->
<div id="editModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <h3>✏️ Edit Driving Experience</h3>
      <button class="modal-close" onclick="closeEditModal()" aria-label="Close">&times;</button>
    </div>
    <div class="grid">
      <input type="hidden" id="edit_id">
      
      <div class="row row-3">
        <div>
          <label for="edit_date">🗓️ Date *</label>
          <input type="date" id="edit_date" required>
        </div>
        <div>
          <label for="edit_dep">⏱️ Departure Time *</label>
          <input type="time" id="edit_dep" required>
        </div>
        <div>
          <label for="edit_arr">⏰ Arrival Time *</label>
          <input type="time" id="edit_arr" required>
        </div>
      </div>

      <div class="row row-3">
        <div>
          <label for="edit_km">📏 Distance (km) *</label>
          <input type="number" id="edit_km" step="0.1" min="0" required>
        </div>
        <div>
          <label for="edit_weather">☁️ Weather *</label>
          <select id="edit_weather" required><option value="">Select weather...</option></select>
        </div>
        <div>
          <label for="edit_tod">🌞 Time of Day *</label>
          <select id="edit_tod" required><option value="">Select time...</option></select>
        </div>
      </div>

      <div class="row row-3">
        <div>
          <label for="edit_surface">🧱 Surface</label>
          <select id="edit_surface"></select>
        </div>
        <div>
          <label for="edit_road">🛣️ Road</label>
          <select id="edit_road"></select>
        </div>
        <div>
          <label for="edit_health">🩺 Driver Health</label>
          <select id="edit_health"></select>
        </div>
      </div>

      <div class="row row-2">
        <div>
          <label>🎯 Maneouvring</label>
          <div id="edit_factors" class="chips"></div>
        </div>
        <div>
          <label>🧭 GPS Coordinates</label>
          <div class="row row-2">
            <input type="number" id="edit_lat" step="0.000001" placeholder="Latitude">
            <input type="number" id="edit_lng" step="0.000001" placeholder="Longitude">
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
      <button id="updateBtn" class="btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        Update Trip
      </button>
    </div>
  </div>
</div>

<?php
/* Expose lookups to JS */
$lookups = [
  'weather'       => $pdo->query("SELECT id,name FROM weather ORDER BY id")->fetchAll(),
  'time_of_day'   => $pdo->query("SELECT id,name FROM time_of_day ORDER BY id")->fetchAll(),
  'surface_cond'  => $pdo->query("SELECT id,name FROM surface_cond ORDER BY id")->fetchAll(),
  'road_cond'     => $pdo->query("SELECT id,name FROM road_cond ORDER BY id")->fetchAll(),
  'driver_health' => $pdo->query("SELECT id,name FROM driver_health ORDER BY id")->fetchAll(),
  'maneouvring'=> $pdo->query("SELECT id,name FROM maneouvring ORDER BY id")->fetchAll(),
];
echo "<script>const LOOKUPS = " . json_encode($lookups, JSON_UNESCAPED_UNICODE) . ";</script>\n";
?>

<script>
/*  ELEMENT REFS  */
const selects = {
  weather: document.getElementById('weather'),
  tod: document.getElementById('tod'),
  surface: document.getElementById('surface'),
  road: document.getElementById('road'),
  health: document.getElementById('health'),
};
const editSelects = {
  weather: document.getElementById('edit_weather'),
  tod: document.getElementById('edit_tod'),
  surface: document.getElementById('edit_surface'),
  road: document.getElementById('edit_road'),
  health: document.getElementById('edit_health'),
};
const factorsWrap = document.getElementById('factors');
const editFactorsWrap = document.getElementById('edit_factors');
const saveBtn = document.getElementById('saveBtn');
const updateBtn = document.getElementById('updateBtn');
const editModal = document.getElementById('editModal');
const chartCtxWeather = document.getElementById('chart_weather');
const ctxTOD = document.getElementById('chart_tod');
const ctxSurface = document.getElementById('chart_surface');
const ctxRoad = document.getElementById('chart_road');
const ctxHealth = document.getElementById('chart_health');
const ctxExternal = document.getElementById('chart_external');
const ctxKmByDate = document.getElementById('chart_km_by_date');

const tabs = document.querySelectorAll('.tab');
const pages = document.querySelectorAll('.page');
const topbar = document.getElementById('topbarProgress');
const toastEl = document.getElementById('toast');
const themeBtn = document.getElementById('themeBtn');

let CHARTS = {}; // keep references
let currentEditId = null;
let allTripsData = []; // Store all trips for filtering
let filteredTripsData = []; // Store filtered results
let filtersVisible = true;
let dataTable = null; // DataTables instance

/*  LOOKUPS (echoed by PHP)  */
function fillSelect(el, rows){
  el.innerHTML = rows.map(r=>`<option value="${r.id}">${r.name}</option>`).join('');
  if (rows.some(r=>String(r.id)==='0')) el.value = '0';
}
function fillFactors(rows, targetWrap = factorsWrap){
  targetWrap.innerHTML = rows.map(r=>`
    <label class="pill" title="${r.name}">
      <input type="checkbox" value="${r.id}">
      <span>${r.name}</span>
    </label>
  `).join('');
}
fillSelect(selects.weather, LOOKUPS.weather);
fillSelect(selects.tod, LOOKUPS.time_of_day);
fillSelect(selects.surface, LOOKUPS.surface_cond);
fillSelect(selects.road, LOOKUPS.road_cond);
fillSelect(selects.health, LOOKUPS.driver_health);
fillFactors(LOOKUPS.maneouvring);

// Fill edit modal selects
fillSelect(editSelects.weather, LOOKUPS.weather);
fillSelect(editSelects.tod, LOOKUPS.time_of_day);
fillSelect(editSelects.surface, LOOKUPS.surface_cond);
fillSelect(editSelects.road, LOOKUPS.road_cond);
fillSelect(editSelects.health, LOOKUPS.driver_health);
fillFactors(LOOKUPS.maneouvring, editFactorsWrap);

// Fill filter dropdowns
fillSelect(document.getElementById('filterWeather'), [{id:'',name:'All Weather'}, ...LOOKUPS.weather]);
fillSelect(document.getElementById('filterTimeOfDay'), [{id:'',name:'All Times'}, ...LOOKUPS.time_of_day]);
fillSelect(document.getElementById('filterSurface'), [{id:'',name:'All Surfaces'}, ...LOOKUPS.surface_cond]);
fillSelect(document.getElementById('filterRoad'), [{id:'',name:'All Roads'}, ...LOOKUPS.road_cond]);

/*  UTIL  */
const lookupOrDefault = (val) => {
  if (val === undefined || val === null || val === '') return 0;
  const parsed = parseInt(val, 10);
  return Number.isNaN(parsed) ? 0 : parsed;
};
function secsToHHMM(secs){
  const h = Math.floor(secs/3600), m = Math.floor((secs%3600)/60);
  return `${h}:${String(m).padStart(2,'0')}`;
}
function showToast(text) {
  toastEl.textContent = text;
  toastEl.classList.add('show');
  setTimeout(()=>toastEl.classList.remove('show'), 2200);
}
function startBar(){ topbar?.classList.add('active') }
function stopBar(){ topbar?.classList.remove('active') }

/*  TABS (persist)  */
function setActivePage(targetId){
  tabs.forEach(tab=>tab.classList.toggle('active', tab.dataset.target === targetId));
  pages.forEach(page=>page.classList.toggle('active', page.id === targetId));
  localStorage.setItem('activeTab', targetId);
}
tabs.forEach(tab=>tab.addEventListener('click', ()=>setActivePage(tab.dataset.target)));
const savedTab = localStorage.getItem('activeTab');
setActivePage(savedTab || (document.querySelector('.tab.active')?.dataset.target) || 'page-dashboard');

/*  THEME TOGGLE  */
(function initTheme(){
  const root = document.documentElement;
  const pref = localStorage.getItem('theme') || 'dark';
  if (pref === 'light') root.classList.add('light');
  themeBtn?.addEventListener('click', ()=>{
    root.classList.toggle('light');
    localStorage.setItem('theme', root.classList.contains('light') ? 'light' : 'dark');
    if (window.__lastStats) drawCharts(window.__lastStats, window.__lastRows || []);
  });
})();

/*  DEFAULTS  */
(function initDefaults(){
  const d = document.getElementById('date');
  const dep = document.getElementById('dep');
  const arr = document.getElementById('arr');
  const now = new Date();
  d.value = now.toISOString().slice(0,10);
  const pad = n=>String(n).padStart(2,'0');
  dep.value = pad(now.getHours())+":"+pad(now.getMinutes());
  arr.value = dep.value;
})();

/*  LIVE DURATION PREVIEW  */
(function liveDuration(){
  const dep = document.getElementById('dep');
  const arr = document.getElementById('arr');
  const km = document.getElementById('km');
  const tag = document.createElement('div');
  tag.className = 'small'; tag.style.marginTop = '6px';
  arr.parentElement.appendChild(tag);
  function toSec(t){ const [H,M]=t.split(':').map(Number); return H*3600+M*60 }
  function render(){
    if(!dep.value || !arr.value) return tag.textContent='';
    let s = toSec(dep.value), e = toSec(arr.value);
    if (isNaN(s) || isNaN(e)) return tag.textContent='';
    if (e < s) e += 24*3600;
    const dur = e - s;
    const speed = (parseFloat(km.value||'0')>0) ? `${(parseFloat(km.value)/ (dur/3600)).toFixed(1)} km/h avg` : '';
    tag.textContent = `Duration preview: ${secsToHHMM(dur)} ${speed?`• ${speed}`:''}`;
  }
  [dep,arr,km].forEach(x=>x.addEventListener('input', render));
  render();
})();

/*  SKELETON ROWS  */
function tableSkeleton(count=6){
  const tb = document.querySelector('#tbl tbody');
  if (!tb) return;
  tb.innerHTML = Array.from({length:count}).map(()=>`
    <tr>
      <td colspan="12"><div class="skeleton"></div></td>
    </tr>
  `).join('');
}

/*  SAVE TRIP  */
saveBtn.addEventListener('click', async () => {
  const payload = {
    date: document.getElementById('date').value,
    departureTime: document.getElementById('dep').value,
    arrivalTime: document.getElementById('arr').value,
    mileageKm: parseFloat(document.getElementById('km').value || '0'),
    weather: parseInt(selects.weather.value, 10),
    timeOfDay: parseInt(selects.tod.value, 10),
    surface: lookupOrDefault(selects.surface.value),
    road: lookupOrDefault(selects.road.value),
    driverHealth: lookupOrDefault(selects.health.value),
    latitude: document.getElementById('lat').value || null,
    longitude: document.getElementById('lng').value || null,
    maneouvrings: [...factorsWrap.querySelectorAll('input[type=checkbox]:checked')].map(c=>parseInt(c.value,10))
  };
  const req = ['date','departureTime','arrivalTime'];
  const fail = !req.every(k=>payload[k]) || !(payload.mileageKm>0);
  if (fail){
    showToast('⚠️ Please fill all required fields (date, times, and positive distance).');
    // Visual feedback on form
    const msgEl = document.getElementById('msg');
    msgEl.textContent = '⚠️ Required fields are missing!';
    msgEl.style.color = 'var(--warn)';
    msgEl.style.fontWeight = '600';
    setTimeout(()=>{ msgEl.textContent = ''; }, 4000);
    return;
  }
  saveBtn.disabled = true;
  startBar();
  try{
    const r = await fetch('?api=add_trip', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)});
    const j = await r.json();
    if (!r.ok) throw new Error(j.error || 'Failed');
    showToast('✅ Trip saved successfully!');
    
    // Clear message and provide success feedback
    const msgEl = document.getElementById('msg');
    msgEl.textContent = '✅ Trip recorded successfully!';
    msgEl.style.color = 'var(--ok)';
    msgEl.style.fontWeight = '600';
    setTimeout(()=>{ msgEl.textContent = ''; }, 3000);
    
    factorsWrap.querySelectorAll('input[type=checkbox]').forEach(c=>c.checked=false);
    await refreshAll();
  }catch(e){ showToast('Error: ' + e.message) }
  finally{ saveBtn.disabled = false; stopBar(); }
});

/*  EDIT & DELETE FUNCTIONS  */
function openEditModal(tripId) {
  currentEditId = tripId;
  startBar();
  fetch(`?api=get_trip&id=${tripId}`)
    .then(r => r.json())
    .then(trip => {
      document.getElementById('edit_id').value = trip.id;
      document.getElementById('edit_date').value = trip.trip_date;
      document.getElementById('edit_dep').value = trip.departure_time;
      document.getElementById('edit_arr').value = trip.arrival_time;
      document.getElementById('edit_km').value = parseFloat(trip.mileage_km);
      editSelects.weather.value = trip.weather_id;
      editSelects.tod.value = trip.time_of_day_id;
      editSelects.surface.value = trip.surface_condition_id;
      editSelects.road.value = trip.road_condition_id;
      editSelects.health.value = trip.driver_health_id;
      document.getElementById('edit_lat').value = trip.latitude || '';
      document.getElementById('edit_lng').value = trip.longitude || '';
      
      // Set maneouvring
      editFactorsWrap.querySelectorAll('input[type=checkbox]').forEach(cb => {
        cb.checked = trip.maneouvrings.includes(parseInt(cb.value, 10));
      });
      
      editModal.classList.add('show');
      stopBar();
    })
    .catch(e => {
      showToast('Error loading trip: ' + e.message);
      stopBar();
    });
}

function closeEditModal() {
  editModal.classList.remove('show');
  currentEditId = null;
}

async function deleteTrip(tripId) {
  if (!confirm('Are you sure you want to delete this driving experience?')) return;
  
  startBar();
  try {
    const r = await fetch('?api=delete_trip', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id: tripId})
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.error || 'Failed');
    showToast('🗑️ Trip deleted successfully!');
    await refreshAll();
  } catch(e) {
    showToast('Error deleting trip: ' + e.message);
  } finally {
    stopBar();
  }
}

updateBtn.addEventListener('click', async () => {
  const payload = {
    id: document.getElementById('edit_id').value,
    date: document.getElementById('edit_date').value,
    departureTime: document.getElementById('edit_dep').value,
    arrivalTime: document.getElementById('edit_arr').value,
    mileageKm: parseFloat(document.getElementById('edit_km').value || '0'),
    weather: parseInt(editSelects.weather.value, 10),
    timeOfDay: parseInt(editSelects.tod.value, 10),
    surface: lookupOrDefault(editSelects.surface.value),
    road: lookupOrDefault(editSelects.road.value),
    driverHealth: lookupOrDefault(editSelects.health.value),
    latitude: document.getElementById('edit_lat').value || null,
    longitude: document.getElementById('edit_lng').value || null,
    maneouvrings: [...editFactorsWrap.querySelectorAll('input[type=checkbox]:checked')].map(c=>parseInt(c.value,10))
  };
  
  const req = ['date','departureTime','arrivalTime'];
  const fail = !req.every(k=>payload[k]) || !(payload.mileageKm>0);
  if (fail){
    showToast('⚠️ Please fill all required fields!');
    return;
  }
  
  updateBtn.disabled = true;
  startBar();
  try {
    const r = await fetch('?api=update_trip', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload)
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.error || 'Failed');
    showToast('✅ Trip updated successfully!');
    closeEditModal();
    await refreshAll();
  } catch(e) {
    showToast('Error updating trip: ' + e.message);
  } finally {
    updateBtn.disabled = false;
    stopBar();
  }
});

// Close modal on overlay click
editModal.addEventListener('click', (e) => {
  if (e.target === editModal) closeEditModal();
});

/*  LOAD + RENDER  */
async function refreshAll(){
  startBar();
  const stats = await (await fetch('?api=stats')).json();
  const rows = await (await fetch('?api=list_trips')).json();

  window.__lastStats = stats;
  window.__lastRows = rows;
  allTripsData = rows;
  filteredTripsData = rows;

  // KPIs
  const trips = stats.kpis ? parseInt(stats.kpis.trips||0) : 0;
  const totalKm = stats.kpis ? parseFloat(stats.kpis.total_km||0) : 0;
  const totalSec = stats.kpis ? parseInt(stats.kpis.total_seconds||0) : 0;
  document.getElementById('k_trips').textContent = trips;
  document.getElementById('k_km').textContent = totalKm.toFixed(1);
  document.getElementById('k_hours').textContent = secsToHHMM(totalSec);
  document.getElementById('k_avg').textContent = trips ? (totalKm/trips).toFixed(1) : '0.0';

  // Header badges: total km + avg speed
  document.getElementById('hdrTotalKm').textContent = `${totalKm.toFixed(1)} km total`;
  const avgSpeed = totalSec > 0 ? (totalKm / (totalSec/3600)) : 0;
  document.getElementById('hdrAvgSpeed').textContent = `${avgSpeed.toFixed(1)} km/h avg`;
  
  // Update footer stats
  document.getElementById('footerTrips').textContent = `${trips} trip${trips !== 1 ? 's' : ''}`;
  document.getElementById('footerKm').textContent = `${totalKm.toFixed(1)} km`;
  document.getElementById('footerHours').textContent = secsToHHMM(totalSec);

  // Extra KPIs + trip facts
  computeExtraKPIs(stats, rows);
  computeTripFacts(rows);

  // Charts
  drawCharts(stats, rows);

  // Table
  tableSkeleton(6);
  renderTable(rows);
  renderMobileCards(rows);
  updateFilterResults();
  stopBar();
}

/*  EXTRA KPIs (Summary)  */
function computeExtraKPIs(stats, rows){
  const weather = (stats.series||[]).find(s=>s.label==='Weather')?.data || [];
  const tod = (stats.series||[]).find(s=>s.label==='Time of Day')?.data || [];
  const maxByCnt = (arr)=> arr.reduce((m,x)=> (+x.cnt > +m.cnt ? x : m), {label:'—',cnt:-1});
  document.getElementById('k_common_weather').textContent = (weather.length? maxByCnt(weather).label : '—');
  document.getElementById('k_peak_tod').textContent = (tod.length? maxByCnt(tod).label : '—');

  let longKm = 0, longDur = 0;
  rows.forEach(r=>{
    longKm = Math.max(longKm, parseFloat(r.mileage_km||0));
    longDur = Math.max(longDur, parseInt(r.duration_seconds||0,10));
  });
  document.getElementById('k_long_km').textContent = longKm.toFixed(1);
  document.getElementById('k_long_dur').textContent = secsToHHMM(longDur);
}

/*  TRIPS FACTS (Trips tab)  */
function computeTripFacts(rows){
  const setDays = new Set();
  let nightCount = 0, over50 = 0;
  const roadFreq = {};
  rows.forEach(r=>{
    setDays.add(r.trip_date);
    if ((r.time_of_day||'').toLowerCase() === 'night') nightCount++;
    if (parseFloat(r.mileage_km||0) >= 50) over50++;
    const road = r.road || '-';
    roadFreq[road] = (roadFreq[road]||0)+1;
  });
  const topRoad = Object.entries(roadFreq).sort((a,b)=>b[1]-a[1])[0]?.[0] || '—';
  document.getElementById('fact_days').textContent = setDays.size.toString();
  document.getElementById('fact_night').textContent = rows.length ? `${Math.round(100*nightCount/rows.length)}%` : '0%';
  document.getElementById('fact_50km').textContent = over50.toString();
  document.getElementById('fact_top_road').textContent = topRoad;
}

/*  CHARTS  */
function gridColor(){
  return getComputedStyle(document.documentElement).getPropertyValue('--line').trim() || '#e5e7eb';
}
function cssVar(name, fallback){
  const v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  return v || fallback;
}
function chartPalette(){
  return [
    cssVar('--pri', '#0f4c5c'),
    cssVar('--pri-2', '#1a3f4a'),
    cssVar('--ok', '#2da86a'),
    cssVar('--warn', '#c56b2b'),
    cssVar('--muted', '#8a928e'),
    '#5b6b6a'
  ];
}
function destroyCharts(){
  Object.values(CHARTS).forEach(c=>{ try{ c.destroy(); }catch(_){} });
  CHARTS = {};
}
function drawCharts(stats, rows){
  destroyCharts();

  // Weather (doughnut)
  const weather = (stats.series||[]).find(s=>s.label==='Weather')?.data || [];
  const palette = chartPalette();
  CHARTS.weather = new Chart(chartCtxWeather, {
    type:'doughnut',
    data:{ labels: weather.map(d=>d.label), datasets:[{ data: weather.map(d=>+d.cnt), backgroundColor: palette }] },
    options:{ responsive:true, plugins:{ legend:{ position:'bottom' } } }
  });

  // Time of day (bar)
  const tod = (stats.series||[]).find(s=>s.label==='Time of Day')?.data || [];
  CHARTS.tod = new Chart(ctxTOD, {
    type:'bar',
    data:{ labels: tod.map(d=>d.label), datasets:[{ label:'Trips', data: tod.map(d=>+d.cnt), backgroundColor: cssVar('--pri', '#0f4c5c') }] },
    options:{ scales:{ y:{ beginAtZero:true, grid:{ color:gridColor() } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
  });

  // Surface (bar)
  const surface = (stats.series||[]).find(s=>s.label==='Surface')?.data || [];
  CHARTS.surface = new Chart(ctxSurface, {
    type:'bar',
    data:{ labels: surface.map(d=>d.label), datasets:[{ label:'Trips', data: surface.map(d=>+d.cnt), backgroundColor: cssVar('--pri-2', '#1a3f4a') }] },
    options:{ scales:{ y:{ beginAtZero:true, grid:{ color:gridColor() } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
  });

  // Road (bar)
  const road = (stats.series||[]).find(s=>s.label==='Road')?.data || [];
  CHARTS.road = new Chart(ctxRoad, {
    type:'bar',
    data:{ labels: road.map(d=>d.label), datasets:[{ label:'Trips', data: road.map(d=>+d.cnt), backgroundColor: cssVar('--ok', '#2da86a') }] },
    options:{ scales:{ y:{ beginAtZero:true, grid:{ color:gridColor() } }, x:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
  });

  // Health (pie)
  const health = (stats.series||[]).find(s=>s.label==='Driver Health')?.data || [];
  CHARTS.health = new Chart(ctxHealth, {
    type:'pie',
    data:{ labels: health.map(d=>d.label), datasets:[{ data: health.map(d=>+d.cnt), backgroundColor: palette }] },
    options:{ plugins:{ legend:{ position:'bottom' } } }
  });

  // Maneouvring (horizontal bar)
  const maneouvringData = (stats.series||[]).find(s=>s.label==='Maneouvring')?.data || [];
  const maneouvringSorted = maneouvringData.slice().sort((a,b)=>b.cnt - a.cnt).slice(0,8);
  CHARTS.maneouvring = new Chart(ctxExternal, {
    type:'bar',
    data:{ labels: maneouvringSorted.map(d=>d.label), datasets:[{ label:'Trips', data: maneouvringSorted.map(d=>+d.cnt), backgroundColor: cssVar('--warn', '#c56b2b') }] },
    options:{ indexAxis:'y', scales:{ x:{ beginAtZero:true, grid:{ color:gridColor() } }, y:{ grid:{ display:false } } }, plugins:{ legend:{ display:false } } }
  });

  // Kilometers by date (line)
  const kmByDate = {};
  rows.forEach(r=>{
    const d = r.trip_date;
    kmByDate[d] = (kmByDate[d] || 0) + parseFloat(r.mileage_km||0);
  });
  const dates = Object.keys(kmByDate).sort((a,b)=> a.localeCompare(b));
  const kms = dates.map(d=> +kmByDate[d].toFixed(1));
  CHARTS.km = new Chart(ctxKmByDate, {
    type:'line',
    data:{ labels: dates, datasets:[{ label:'Km', data:kms, tension:.25, fill:false, borderColor: cssVar('--pri', '#0f4c5c') }] },
    options:{ scales:{ y:{ beginAtZero:true, grid:{ color:gridColor() } }, x:{ grid:{ display:false } } } }
  });
}

/*  SORTABLE TABLE (Trips tab)  */
let sortState = { key: 'trip_date', dir: 'desc' };
const thMap = [
  {key:'trip_date', text:'Date'},
  {key:'departure_time', text:'Dep'},
  {key:'arrival_time', text:'Arr'},
  {key:'duration_seconds', text:'Hours'},
  {key:'mileage_km', text:'Km'},
  {key:'weather', text:'Weather'},
  {key:'time_of_day', text:'Time'},
  {key:'surface', text:'Surface'},
  {key:'road', text:'Road'},
  {key:'driver_health', text:'Health'},
  {key:'maneouvrings', text:'Maneouvring'},
  {key:'actions', text:'Actions', noSort:true},
];
function applySort(rows){
  const {key,dir} = sortState;
  const mul = dir === 'asc' ? 1 : -1;
  return rows.slice().sort((a,b)=>{
    const av = a[key], bv = b[key];
    if (key === 'mileage_km' || key === 'duration_seconds') return (parseFloat(av)-parseFloat(bv))*mul;
    const sa = (Array.isArray(av)? av.join(',') : String(av ?? '')).toLowerCase();
    const sb = (Array.isArray(bv)? bv.join(',') : String(bv ?? '')).toLowerCase();
    return sa.localeCompare(sb)*mul;
  });
}
function renderTableHead(){
  const thead = document.querySelector('#tbl thead tr');
  thead.innerHTML = thMap.map(h=>{
    if (h.noSort) return `<th>${h.text}</th>`;
    const arrow = (sortState.key===h.key) ? (sortState.dir==='asc'?' ▲':' ▼') : '';
    return `<th data-key="${h.key}" title="Sort by ${h.text}">${h.text}${arrow}</th>`;
  }).join('');
  thead.querySelectorAll('th[data-key]').forEach(th=>{
    th.addEventListener('click', ()=>{
      const k = th.dataset.key;
      if (sortState.key === k) sortState.dir = (sortState.dir==='asc'?'desc':'asc');
      else { sortState.key = k; sortState.dir = 'asc'; }
      refreshTableOnly();
    });
  });
}
function renderTable(rows){
  // Destroy existing DataTable if it exists
  if (dataTable) {
    dataTable.destroy();
  }
  
  const tb = document.querySelector('#tbl tbody');
  tb.innerHTML = rows.map(r=>{
    const hours = secsToHHMM(parseInt(r.duration_seconds||0,10));
    const factors = (r.maneouvrings && r.maneouvrings.length)
      ? r.maneouvrings.map(n=>`<span class="pill" style="font-size:11px;padding:3px 8px">${n}</span>`).join(' ')
      : '-';
    // Use anonymous_id for security (session-based)
    const anonymousId = r.anonymous_id || r.id;
    return `<tr>
      <td>${r.trip_date}</td>
      <td>${r.departure_time}</td>
      <td>${r.arrival_time}</td>
      <td>${hours}</td>
      <td data-order="${r.mileage_km}">${parseFloat(r.mileage_km).toFixed(1)}</td>
      <td>${r.weather}</td>
      <td>${r.time_of_day}</td>
      <td>${r.surface}</td>
      <td>${r.road}</td>
      <td>${r.driver_health}</td>
      <td>${factors}</td>
      <td>
        <div class="action-btns">
          <button class="icon-btn-sm" onclick="openEditModal('${anonymousId}')" title="Edit trip" aria-label="Edit trip">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button class="icon-btn-sm delete" onclick="deleteTrip('${anonymousId}')" title="Delete trip" aria-label="Delete trip">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
              <line x1="10" y1="11" x2="10" y2="17"/>
              <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
  
  // Initialize DataTables with advanced features
  dataTable = $('#tbl').DataTable({
    responsive: true,
    pageLength: 10,
    order: [[0, 'desc']], // Sort by date descending
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'copy',
        text: '📋 Copy',
        className: 'dt-button'
      },
      {
        extend: 'csv',
        text: '📊 CSV',
        className: 'dt-button'
      },
      {
        extend: 'excel',
        text: '📈 Excel',
        className: 'dt-button'
      },
      {
        extend: 'print',
        text: '🖨️ Print',
        className: 'dt-button'
      }
    ],
    language: {
      search: '🔍 Search:',
      lengthMenu: 'Show _MENU_ trips',
      info: 'Showing _START_ to _END_ of _TOTAL_ trips<pre>',
      infoEmpty: 'No trips available',
      infoFiltered: '(filtered from _MAX_ total trips)',
      paginate: {
        first: '⏮️',
        last: '⏭️',
        next: '▶️',
        previous: '◀️'
      }
    }
  });
  
  window.__lastRows = rows;
}
async function refreshTableOnly(){
  if (!window.__lastRows){
    tableSkeleton(6);
    const rows = await (await fetch('?api=list_trips')).json();
    allTripsData = rows;
    filteredTripsData = rows;
    renderTable(rows);
    renderMobileCards(rows);
    computeTripFacts(rows);
  } else {
    renderTable(window.__lastRows);
    renderMobileCards(window.__lastRows);
    computeTripFacts(window.__lastRows);
  }
}

/*  FILTER FUNCTIONS  */
function toggleFilters() {
  filtersVisible = !filtersVisible;
  const content = document.getElementById('filtersContent');
  const actions = document.querySelector('.filter-actions');
  const btn = document.getElementById('filterToggleBtn');
  
  if (filtersVisible) {
    content.style.display = 'grid';
    actions.style.display = 'flex';
    btn.textContent = 'Hide Filters';
  } else {
    content.style.display = 'none';
    actions.style.display = 'none';
    btn.textContent = 'Show Filters';
  }
}

function applyFilters() {
  const search = document.getElementById('filterSearch').value.toLowerCase();
  const dateFrom = document.getElementById('filterDateFrom').value;
  const dateTo = document.getElementById('filterDateTo').value;
  const weatherSelect = document.getElementById('filterWeather');
  const weather = weatherSelect.options[weatherSelect.selectedIndex]?.text;
  const timeOfDaySelect = document.getElementById('filterTimeOfDay');
  const timeOfDay = timeOfDaySelect.options[timeOfDaySelect.selectedIndex]?.text;
  const minKm = parseFloat(document.getElementById('filterMinKm').value) || 0;
  const maxKm = parseFloat(document.getElementById('filterMaxKm').value) || Infinity;
  const surfaceSelect = document.getElementById('filterSurface');
  const surface = surfaceSelect.options[surfaceSelect.selectedIndex]?.text;
  const roadSelect = document.getElementById('filterRoad');
  const road = roadSelect.options[roadSelect.selectedIndex]?.text;
  
  filteredTripsData = allTripsData.filter(trip => {
    // Search filter
    if (search) {
      const searchStr = [
        trip.trip_date,
        trip.weather,
        trip.time_of_day,
        trip.surface,
        trip.road,
        trip.driver_health,
        trip.mileage_km,
        ...(trip.maneouvrings || [])
      ].join(' ').toLowerCase();
      
      if (!searchStr.includes(search)) return false;
    }
    
    // Date range
    if (dateFrom && trip.trip_date < dateFrom) return false;
    if (dateTo && trip.trip_date > dateTo) return false;
    
    // Weather (compare names, not IDs)
    if (weather && weather !== 'All Weather' && trip.weather !== weather) return false;
    
    // Time of day (compare names, not IDs)
    if (timeOfDay && timeOfDay !== 'All Times' && trip.time_of_day !== timeOfDay) return false;
    
    // Km range
    const km = parseFloat(trip.mileage_km);
    if (km < minKm || km > maxKm) return false;
    
    // Surface (compare names, not IDs)
    if (surface && surface !== 'All Surfaces' && trip.surface !== surface) return false;
    
    // Road (compare names, not IDs)
    if (road && road !== 'All Roads' && trip.road !== road) return false;
    
    return true;
  });
  
  renderTable(filteredTripsData);
  renderMobileCards(filteredTripsData);
  computeTripFacts(filteredTripsData);
  updateFilterResults();
  showToast(`✓ Found ${filteredTripsData.length} trip(s)`);
}

function clearFilters() {
  document.getElementById('filterSearch').value = '';
  document.getElementById('filterDateFrom').value = '';
  document.getElementById('filterDateTo').value = '';
  document.getElementById('filterWeather').value = '';
  document.getElementById('filterTimeOfDay').value = '';
  document.getElementById('filterMinKm').value = '';
  document.getElementById('filterMaxKm').value = '';
  document.getElementById('filterSurface').value = '';
  document.getElementById('filterRoad').value = '';
  
  filteredTripsData = allTripsData;
  renderTable(filteredTripsData);
  renderMobileCards(filteredTripsData);
  computeTripFacts(filteredTripsData);
  updateFilterResults();
  showToast('✓ Filters cleared');
}

function updateFilterResults() {
  const el = document.getElementById('filterResults');
  if (!el) return;
  
  const total = allTripsData.length;
  const filtered = filteredTripsData.length;
  
  if (filtered === total) {
    el.textContent = `Showing all ${total} trip(s)`;
  } else {
    el.textContent = `Showing ${filtered} of ${total} trip(s)`;
  }
}

/*  MOBILE CARD RENDERING  */
function renderMobileCards(rows) {
  const container = document.getElementById('tripsCards');
  if (!container) return;
  
  const sorted = applySort(rows);
  
  container.innerHTML = sorted.map(r => {
    const hours = secsToHHMM(parseInt(r.duration_seconds || 0, 10));
    const factors = (r.maneouvrings && r.maneouvrings.length)
      ? r.maneouvrings.map(n => `<span class=\"pill\" style=\"font-size:12px;padding:4px 8px\">${n}</span>`).join(' ')
      : '-';
    
    // Use anonymous_id for security
    const anonymousId = r.anonymous_id || r.id;
    
    return `
      <div class=\"trip-card\">
        <div class=\"trip-card-header\">
          <div class=\"trip-card-date\">${r.trip_date}</div>
          <div class=\"trip-card-km\">${parseFloat(r.mileage_km).toFixed(1)} km</div>
        </div>
        
        <div class=\"trip-card-body\">
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">⏰ Time</span>
            <span class=\"trip-card-value\">${r.departure_time} → ${r.arrival_time} (${hours})</span>
          </div>
          
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">🌤️ Weather</span>
            <span class=\"trip-card-value\">${r.weather}</span>
          </div>
          
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">🌅 Time of Day</span>
            <span class=\"trip-card-value\">${r.time_of_day}</span>
          </div>
          
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">🛤️ Surface</span>
            <span class=\"trip-card-value\">${r.surface}</span>
          </div>
          
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">🚧 Road</span>
            <span class=\"trip-card-value\">${r.road}</span>
          </div>
          
          <div class=\"trip-card-row\">
            <span class=\"trip-card-label\">💪 Health</span>
            <span class=\"trip-card-value\">${r.driver_health}</span>
          </div>
          
          ${r.maneouvrings && r.maneouvrings.length ? `
          <div class=\"trip-card-row\" style=\"flex-direction:column;align-items:flex-start;gap:6px\">
            <span class=\"trip-card-label\">⚠️ Factors</span>
            <div style=\"display:flex;flex-wrap:wrap;gap:4px\">${factors}</div>
          </div>
          ` : ''}
        </div>
        
        <div class=\"trip-card-actions\">
          <button class=\"icon-btn-sm\" onclick=\"openEditModal('${anonymousId}')\" title=\"Edit trip\" aria-label=\"Edit trip\">
            <svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">
              <path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"/>
              <path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"/>
            </svg>
          </button>
          <button class=\"icon-btn-sm delete\" onclick=\"deleteTrip('${anonymousId}')\" title=\"Delete trip\" aria-label=\"Delete trip\">
            <svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">
              <polyline points=\"3 6 5 6 21 6\"/>
              <path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"/>
              <line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"/>
              <line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"/>
            </svg>
          </button>
        </div>
      </div>
    `;
  }).join('');
}

// Add real-time search
document.getElementById('filterSearch')?.addEventListener('input', debounce(applyFilters, 300));

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

refreshAll();

// Header scroll effect
let lastScroll = 0;
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', () => {
  const currentScroll = window.pageYOffset;
  if (currentScroll > 50) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
  lastScroll = currentScroll;
});

// Smooth scroll for footer links
document.querySelectorAll('.footer-section a[href^="#"]').forEach(link => {
  link.addEventListener('click', (e) => {
    const href = link.getAttribute('href');
    if (href.startsWith('#page-')) {
      e.preventDefault();
      const pageId = href.substring(1);
      setActivePage(pageId);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });
});

// Add floating particles effect
function createParticle() {
  const particle = document.createElement('div');
  particle.style.cssText = `
    position: fixed;
    width: ${Math.random() * 4 + 2}px;
    height: ${Math.random() * 4 + 2}px;
    background: radial-gradient(circle, rgba(124,140,255,0.8), transparent);
    border-radius: 50%;
    pointer-events: none;
    z-index: -1;
    left: ${Math.random() * 100}%;
    top: 100%;
    animation: float-up ${Math.random() * 10 + 10}s linear infinite;
    opacity: ${Math.random() * 0.5 + 0.3};
  `;
  document.body.appendChild(particle);
  
  setTimeout(() => particle.remove(), 20000);
}

// Create particles periodically
setInterval(createParticle, 2000);

// Add float-up animation if not exists
if (!document.querySelector('#particle-animation-style')) {
  const style = document.createElement('style');
  style.id = 'particle-animation-style';
  style.textContent = `
    @keyframes float-up {
      from {
        transform: translateY(0) translateX(0);
        opacity: 0.6;
      }
      to {
        transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
}

// Card intersection observer for animations
const cardObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.card').forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(30px)';
  card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  cardObserver.observe(card);
});
</script>
</body>
