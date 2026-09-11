<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SignGyaan is a simple, accessible learning platform for learners, teachers and parents.">

    <title>{{ config('app.name', 'SignGyaan') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body { margin: 0; font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; background: #f8fafc; color: #0f172a; }
        * { box-sizing: border-box; }
        a { text-decoration: none; }
        .shell { min-height: 100vh; }
        .container { width: min(1120px, calc(100% - 32px)); margin: 0 auto; }
        .nav { display: flex; align-items: center; justify-content: space-between; min-height: 72px; gap: 24px; border-bottom: 1px solid #e2e8f0; }
        .brand { display: flex; align-items: center; gap: 12px; color: #0f172a; font-weight: 700; font-size: 20px; }
        .brand-mark { width: 36px; height: 36px; border-radius: 11px; display: grid; place-items: center; background: #4f46e5; color: white; font-weight: 700; }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 16px; border-radius: 11px; font-size: 14px; font-weight: 600; transition: .2s ease; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: white; color: #334155; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #f8fafc; }
        .hero { display: grid; grid-template-columns: 1.08fr .92fr; gap: 56px; align-items: center; padding: 92px 0 80px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 999px; background: white; color: #475569; font-size: 13px; font-weight: 600; }
        h1 { margin: 20px 0 18px; max-width: 760px; font-size: clamp(42px, 7vw, 72px); line-height: 1.02; letter-spacing: -.045em; }
        .lead { max-width: 650px; margin: 0; color: #64748b; font-size: 18px; line-height: 1.75; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .hero-note { margin-top: 18px; color: #94a3b8; font-size: 13px; }
        .preview { background: white; border: 1px solid #e2e8f0; border-radius: 24px; box-shadow: 0 20px 60px rgba(15, 23, 42, .08); padding: 18px; }
        .preview-top { display: flex; align-items: center; justify-content: space-between; padding: 2px 2px 16px; }
        .preview-title { font-size: 14px; font-weight: 700; }
        .status { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; color: #64748b; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }
        .panel { border: 1px solid #e2e8f0; border-radius: 18px; padding: 20px; background: #f8fafc; }
        .metric { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 0; border-bottom: 1px solid #e2e8f0; }
        .metric:last-child { border-bottom: 0; }
        .metric-label { color: #64748b; font-size: 13px; }
        .metric-value { font-size: 16px; font-weight: 700; }
        .progress { width: 100%; height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden; margin-top: 14px; }
        .progress > span { display: block; width: 68%; height: 100%; background: #4f46e5; border-radius: inherit; }
        .features { padding: 0 0 88px; }
        .section-head { max-width: 700px; margin-bottom: 28px; }
        .section-head h2 { margin: 0 0 10px; font-size: 32px; letter-spacing: -.025em; }
        .section-head p { margin: 0; color: #64748b; line-height: 1.7; }
        .feature-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .feature { padding: 24px; border: 1px solid #e2e8f0; border-radius: 18px; background: white; }
        .feature-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: #eef2ff; color: #4f46e5; font-weight: 700; margin-bottom: 18px; }
        .feature h3 { margin: 0 0 8px; font-size: 17px; }
        .feature p { margin: 0; color: #64748b; line-height: 1.65; font-size: 14px; }
        footer { border-top: 1px solid #e2e8f0; padding: 24px 0 34px; color: #94a3b8; font-size: 13px; }
        .footer-row { display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        @media (max-width: 820px) {
            .hero { grid-template-columns: 1fr; padding: 64px 0; gap: 36px; }
            .feature-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .container { width: min(100% - 24px, 1120px); }
            .nav { min-height: 64px; }
            .brand span:last-child { font-size: 18px; }
            .nav-actions .btn-secondary { display: none; }
            .hero { padding: 48px 0 54px; }
            h1 { font-size: 44px; }
            .lead { font-size: 16px; }
            .hero-actions { flex-direction: column; }
            .hero-actions .btn { width: 100%; }
            .footer-row { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="container">
        <nav class="nav" aria-label="Main navigation">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark" aria-hidden="true">S</span>
                <span>SignGyaan</span>
            </a>

            <div class="nav-actions">
                @auth
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Open dashboard</a>
                @else
                    <a class="btn btn-secondary" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Get started</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="container hero">
            <div>
                <span class="eyebrow">Accessible learning, made simple</span>
                <h1>Learn clearly. Teach better. Track progress.</h1>
                <p class="lead">SignGyaan brings learners, teachers and parents into one clean learning space with simple navigation, clear progress and focused course content.</p>

                <div class="hero-actions">
                    @auth
                        <a class="btn btn-primary" href="{{ route('dashboard') }}">Go to dashboard</a>
                    @else
                        <a class="btn btn-primary" href="{{ route('register') }}">Create account</a>
                        <a class="btn btn-secondary" href="{{ route('login') }}">I already have an account</a>
                    @endauth
                </div>
                <p class="hero-note">Minimal interface · Light theme · Mobile friendly</p>
            </div>

            <div class="preview" aria-label="SignGyaan dashboard preview">
                <div class="preview-top">
                    <span class="preview-title">Learning overview</span>
                    <span class="status"><span class="dot"></span> Active</span>
                </div>
                <div class="panel">
                    <div class="metric">
                        <span class="metric-label">Current course</span>
                        <span class="metric-value">Digital Skills</span>
                    </div>
                    <div class="metric">
                        <span class="metric-label">Completed lessons</span>
                        <span class="metric-value">12 / 18</span>
                    </div>
                    <div class="metric">
                        <span class="metric-label">Progress</span>
                        <span class="metric-value">68%</span>
                    </div>
                    <div class="progress" aria-label="68 percent complete"><span></span></div>
                </div>
            </div>
        </section>

        <section class="container features">
            <div class="section-head">
                <h2>Everything important, without the clutter.</h2>
                <p>A focused interface keeps learning tasks easy to find and progress easy to understand.</p>
            </div>

            <div class="feature-grid">
                <article class="feature">
                    <div class="feature-icon" aria-hidden="true">01</div>
                    <h3>Simple learning path</h3>
                    <p>Courses, units and lessons are organised in a clear structure so learners always know what comes next.</p>
                </article>
                <article class="feature">
                    <div class="feature-icon" aria-hidden="true">02</div>
                    <h3>Visible progress</h3>
                    <p>Track lesson completion and course progress with clean summaries that are quick to understand.</p>
                </article>
                <article class="feature">
                    <div class="feature-icon" aria-hidden="true">03</div>
                    <h3>Role-based experience</h3>
                    <p>Learners, teachers, parents and administrators each get the tools relevant to their role.</p>
                </article>
            </div>
        </section>
    </main>

    <footer>
        <div class="container footer-row">
            <span>© {{ date('Y') }} SignGyaan</span>
            <span>Accessible learning platform</span>
        </div>
    </footer>
</div>
</body>
</html>
