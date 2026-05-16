<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		@php
			$host = request()->getHost();
		@endphp

		@if(Str::contains($host, 'technicul.com'))
			<meta name="robots" content="noindex, nofollow">
			<meta name="Googlebot" content="noindex, nofollow">
		@endif

		<title>Admin Login — Society Maintenance Management System</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta name="handheldfriendly" content="true" />
		<meta name="MobileOptimized" content="width" />
		<meta name="description" content="Sign in to the Society Maintenance Management System admin workspace to manage maintenance, billing, and member communication." />
		<meta name="author" content="Society Maintenance Management System" />
		<meta name="keywords" content="society maintenance, housing society, admin login, billing, maintenance requests" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<link id="themeColors" rel="stylesheet" href="/backend/dist/css/style.min.css" />
		<style>
			:root {
				--login-primary: #1b6ff2;
				--login-primary-dark: #104fb8;
				--login-ink: #17233c;
				--login-muted: #667085;
				--login-border: #d9e2f1;
				--login-panel: #f5f8ff;
			}

			body {
				background: #eef4ff;
			}

			.login-shell {
				min-height: 100vh;
				background:
					radial-gradient(circle at 18% 12%, rgba(27, 111, 242, 0.16), transparent 28%),
					radial-gradient(circle at 82% 84%, rgba(57, 189, 248, 0.16), transparent 30%),
					linear-gradient(135deg, #f7fbff 0%, #edf4ff 48%, #f9fbff 100%);
				padding: 12px;
			}

			.login-card {
				width: min(1080px, 100%);
				height: calc(100vh - 24px);
				min-height: 0;
				max-height: 1000px;
				background: rgba(255, 255, 255, 0.96);
				border: 1px solid rgba(217, 226, 241, 0.9);
				border-radius: 24px;
				box-shadow: 0 24px 70px rgba(23, 35, 60, 0.14);
				overflow: hidden;
			}

			.login-visual {
				position: relative;
				background:
					linear-gradient(160deg, rgba(27, 111, 242, 0.95), rgba(16, 79, 184, 0.96)),
					var(--login-primary);
				color: #fff;
				padding: 34px 42px;
			}

			.login-visual::after {
				content: "";
				position: absolute;
				inset: auto -70px -110px auto;
				width: 310px;
				height: 310px;
				border-radius: 50%;
				background: rgba(255, 255, 255, 0.1);
			}

			.brand-chip {
				display: inline-flex;
				align-items: center;
				gap: 10px;
				padding: 10px 14px;
				border: 1px solid rgba(255, 255, 255, 0.24);
				border-radius: 999px;
				background: rgba(255, 255, 255, 0.12);
				font-weight: 700;
			}

			.brand-chip img {
				width: 112px;
				filter: brightness(0) invert(1);
			}

			.visual-copy {
				position: relative;
				z-index: 1;
				max-width: 470px;
			}

			.visual-copy h1 {
				font-size: clamp(1.9rem, 3vw, 2.8rem);
				line-height: 1.08;
				letter-spacing: 0;
				margin: 20px 0 12px;
				color: #fff;
			}

			.visual-copy p {
				color: rgba(255, 255, 255, 0.82);
				font-size: 0.96rem;
				line-height: 1.55;
				margin-bottom: 18px;
			}

			.login-illustration {
				position: relative;
				z-index: 1;
				width: min(400px, 82%);
				margin-top: 8px;
				filter: drop-shadow(0 24px 34px rgba(0, 0, 0, 0.18));
			}

			.trust-row {
				position: relative;
				z-index: 1;
				display: grid;
				grid-template-columns: repeat(3, 1fr);
				gap: 10px;
				margin-top: 20px;
			}

			.trust-item {
				padding: 12px;
				border: 1px solid rgba(255, 255, 255, 0.18);
				border-radius: 16px;
				background: rgba(255, 255, 255, 0.1);
			}

			.trust-item strong {
				display: block;
				font-size: 1rem;
				color: #fff;
			}

			.trust-item span {
				display: block;
				margin-top: 4px;
				color: rgba(255, 255, 255, 0.72);
				font-size: 0.82rem;
			}

			.login-form-panel {
				padding: 34px 42px;
			}

			.form-wrap {
				width: min(430px, 100%);
			}

			.form-logo {
				width: 300px;
			}

			.form-title {
				color: var(--login-ink);
				font-size: 1.75rem;
				line-height: 1.18;
				letter-spacing: 0;
			}

			.form-subtitle {
				color: var(--login-muted);
				line-height: 1.6;
			}

			.form-label {
				color: var(--login-ink);
				font-weight: 700;
				margin-bottom: 8px;
			}

			.form-control {
				min-height: 46px;
				border-color: var(--login-border);
				border-radius: 12px;
				color: var(--login-ink);
				padding: 12px 14px;
			}

			.form-control:focus {
				border-color: var(--login-primary);
				box-shadow: 0 0 0 4px rgba(27, 111, 242, 0.13);
			}

			.btn-login {
				min-height: 46px;
				border: 0;
				border-radius: 12px;
				background: linear-gradient(135deg, var(--login-primary), var(--login-primary-dark));
				box-shadow: 0 14px 26px rgba(27, 111, 242, 0.24);
				font-weight: 800;
			}

			.btn-login:hover,
			.btn-login:focus {
				background: linear-gradient(135deg, #0f63e7, #0b459f);
			}

			.form-check-input {
				border-color: var(--login-border);
			}

			.form-check-input:checked {
				background-color: var(--login-primary);
				border-color: var(--login-primary);
			}

			.security-note {
				display: flex;
				gap: 10px;
				align-items: flex-start;
				margin-top: 18px;
				padding: 12px;
				border-radius: 14px;
				background: var(--login-panel);
				color: var(--login-muted);
				font-size: 0.88rem;
				line-height: 1.5;
			}

			.security-note span {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				flex: 0 0 26px;
				width: 26px;
				height: 26px;
				border-radius: 50%;
				background: rgba(27, 111, 242, 0.12);
				color: var(--login-primary);
				font-weight: 800;
			}

			.login-eyebrow {
				font-size: 0.75rem;
				font-weight: 600;
				letter-spacing: 0.08em;
				text-transform: uppercase;
				color: rgba(255, 255, 255, 0.72);
				margin-bottom: 14px;
			}

			.form-eyebrow {
				font-size: 0.75rem;
				font-weight: 600;
				letter-spacing: 0.08em;
				text-transform: uppercase;
				color: var(--login-muted);
				margin-bottom: 10px;
			}

			@media (max-width: 991.98px) {
				.login-card {
					min-height: auto;
				}

				.login-form-panel {
					min-height: 100vh;
					padding: 36px 22px;
				}
			}

			@media (min-width: 992px) {
				.login-visual {
					height: 100%;
					min-height: 0;
					padding: 34px 40px;
				}

				.login-form-panel {
					padding: 34px 42px;
				}

				.trust-row {
					gap: 10px;
				}
			}

			@media (min-width: 992px) and (max-height: 700px) {
				.login-card {
					max-height: none;
				}

				.login-visual,
				.login-form-panel {
					padding: 24px 34px;
				}

				.visual-copy h1 {
					font-size: 1.95rem;
					margin: 14px 0 8px;
				}

				.visual-copy p,
				.form-subtitle {
					font-size: 0.88rem;
					line-height: 1.45;
				}

				.login-illustration {
					width: min(250px, 76%);
				}

				.trust-row {
					margin-top: 12px;
				}

				.trust-item {
					padding: 9px;
				}

				.form-logo {
					width: 112px;
				}

				.form-title {
					font-size: 1.5rem;
				}

				.form-control,
				.btn-login {
					min-height: 40px;
					padding: 8px 12px;
				}

				.security-note {
					display: none;
				}
			}

			@media (max-width: 575.98px) {
				.login-shell {
					padding: 0;
				}

				.login-card {
					border: 0;
					border-radius: 0;
					box-shadow: none;
				}

				.login-form-panel {
					padding: 28px 18px;
				}

				.form-title {
					font-size: 1.65rem;
				}
			}
		</style>
	</head>
	<body>
		<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
			<main class="login-shell d-flex align-items-center justify-content-center">
				<section class="login-card">
					<div class="row g-0" style="    height: 60% !important;">
						<div class="col-lg-7 d-none d-lg-flex">
							<div class="login-visual w-100 d-flex flex-column justify-content-between">
								<div class="visual-copy">
									<p class="login-eyebrow mb-0">Society Maintenance Management System</p>
									{{-- <div class="brand-chip mt-3">
										<img src="/logo.png" alt="Society Maintenance Management System">
									</div> --}}
									<h1>Run your society from one secure workspace.</h1>
									<p>Centralize maintenance requests, billing, and member communication—everything your committee needs in a single professional admin dashboard.</p>
								</div>

								<div class="text-center">
									<img src="/backend/dist/images/backgrounds/login-security.svg" alt="Illustration representing secure access to society administration" class="login-illustration">
								</div>

								<div class="trust-row">
									<div class="trust-item">
										<strong>Maintenance</strong>
										<span>Track requests and work orders</span>
									</div>
									<div class="trust-item">
										<strong>Billing</strong>
										<span>Stay on top of dues and notices</span>
									</div>
									<div class="trust-item">
										<strong>Members</strong>
										<span>Keep residents informed</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-5">
							<div class="login-form-panel h-100 d-flex align-items-center justify-content-center bg-white">
								<div class="form-wrap">
									<div class="mb-4 text-center">
										<img src="/logo.png" class="form-logo" alt="Society Maintenance Management System">
										<p class="form-eyebrow mb-0">Society Maintenance Management System</p>
										<h2 class="form-title fw-bolder mb-2">Welcome back</h2>
										<p class="form-subtitle mb-0">Sign in with the email and password for your society admin account to open the dashboard.</p>
									</div>

									@if ($errors->any())
										<div class="alert alert-danger rounded-3 mb-4" role="alert">
											Please check your email and password, then try again.
										</div>
									@endif

									<form method="POST" action="{{ route('login') }}" novalidate>
										@csrf
										<div class="mb-3">
											<label for="email" class="form-label">Email address</label>
											<input
												type="email"
												id="email"
												class="form-control @error('email') is-invalid @enderror"
												name="email"
												value="{{ old('email') }}"
												placeholder="admin@example.com"
												autocomplete="email"
												required
												autofocus
											/>
											@error('email')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>

										<div class="mb-3">
											<label for="password" class="form-label">Password</label>
											<input
												type="password"
												id="password"
												class="form-control @error('password') is-invalid @enderror"
												name="password"
												placeholder="Enter your password"
												autocomplete="current-password"
												required
											/>
											@error('password')
												<div class="invalid-feedback">{{ $message }}</div>
											@enderror
										</div>

										<div class="d-flex align-items-center justify-content-between mb-3">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
												<label class="form-check-label text-muted" for="remember">Remember me</label>
											</div>
										</div>

										<button type="submit" class="btn btn-primary btn-login w-100 py-8 mb-0">Sign in</button>
									</form>

									<p class="text-center text-muted small mt-4 mb-0">
										New society?
										<a href="/" class="fw-semibold text-decoration-none">Register here</a>
									</p>
 
								</div>
							</div>
						</div>
					</div>
				</section>
			</main>
		</div>

		<script src="/backend/dist/libs/jquery/dist/jquery.min.js"></script>
		<script src="/backend/dist/libs/simplebar/dist/simplebar.min.js"></script>
		<script src="/backend/dist/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
		<script src="/backend/dist/js/app.min.js"></script>
		<script src="/backend/dist/js/app.init.js"></script>
		<script src="/backend/dist/js/app-style-switcher.js"></script>
		<script src="/backend/dist/js/sidebarmenu.js"></script>
		<script src="/backend/dist/js/custom.js"></script>
	</body>
</html>
