<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Society Registration — Society Maintenance Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --smms-bg: #eef4ff;
            --smms-card: #ffffff;
            --smms-muted: #667085;
            --smms-border: #d9e2f1;
            --smms-primary: #0d6efd;
            --smms-primary-dark: #0b5ed7;
            --smms-ink: #17233c;
            --smms-panel: #f5f8ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "DM Sans", system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(circle at 12% 12%, rgba(13, 110, 253, 0.12), transparent 28%),
                radial-gradient(circle at 88% 82%, rgba(32, 201, 151, 0.12), transparent 30%),
                var(--smms-bg);
            color: var(--smms-ink);
            min-height: 100vh;
        }

        .register-shell {
            min-height: 100vh;
            padding: 16px;
        }

        .register-card {
            width: min(1180px, 100%);
            height: calc(100vh - 32px);
            min-height: 0;
            max-height: 760px;
            background: var(--smms-card);
            border: 1px solid var(--smms-border);
            border-radius: 22px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .register-hero {
            height: 100%;
            background: linear-gradient(160deg, #0d6efd 0%, #0b5ed7 100%);
            color: #fff;
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        .register-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -90px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }

        .eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: clamp(1.75rem, 2.5vw, 2.55rem);
            line-height: 1.08;
            letter-spacing: 0;
        }

        .hero-copy {
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.55;
            font-size: 0.95rem;
        }

        .hero-metric-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 22px;
        }

        .hero-metric {
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 12px;
        }

        .hero-metric strong,
        .hero-metric span {
            display: block;
        }

        .hero-metric strong {
            color: #fff;
            font-size: 0.98rem;
        }

        .hero-metric span {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.78rem;
            margin-top: 3px;
        }

        .building-art {
            position: relative;
            z-index: 1;
            width: min(330px, 90%);
            max-height: 220px;
            margin-top: 18px;
        }

        .form-panel {
            height: 100%;
            padding: 28px 34px;
        }

        .form-card-title {
            font-size: 1.58rem;
            line-height: 1.12;
            letter-spacing: 0;
        }

        .form-card-subtitle {
            color: var(--smms-muted);
            font-size: 0.9rem;
        }

        .register-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 14px;
            row-gap: 10px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--smms-ink);
        }

        .form-control,
        .form-select,
        .input-group-text,
        .password-toggle {
            min-height: 42px;
            border-color: var(--smms-border);
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.52rem 0.72rem;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fc;
            color: var(--smms-muted);
            padding-inline: 0.72rem;
        }

        .input-group .form-control,
        .input-group .form-select {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group .rounded-0 {
            border-radius: 0 !important;
        }

        textarea.form-control {
            min-height: 76px;
            resize: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #9ec5fe;
            box-shadow: 0 0 0 0.18rem rgba(13, 110, 253, 0.12);
        }

        .btn-primary {
            min-height: 44px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--smms-primary), var(--smms-primary-dark));
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.22);
            font-weight: 700;
        }

        .password-toggle {
            border-color: var(--smms-border) !important;
            border-radius: 0 10px 10px 0;
        }

        .form-footer {
            margin-top: 14px;
        }

        @media (min-width: 992px) {
            body {
                overflow: hidden;
            }
        }

        @media (min-width: 992px) and (max-height: 700px) {
            .register-card {
                max-height: none;
            }

            .register-hero {
                padding: 22px;
            }

            .hero-title {
                font-size: 1.8rem;
                margin-bottom: 0.65rem !important;
            }

            .hero-copy {
                font-size: 0.84rem;
                line-height: 1.4;
            }

            .hero-metric-grid {
                gap: 7px;
                margin-top: 14px;
            }

            .hero-metric {
                padding: 8px;
            }

            .building-art {
                max-height: 150px;
                margin-top: 8px;
            }

            .form-panel {
                padding: 16px 28px;
            }

            .form-card-title {
                font-size: 1.35rem;
            }

            .form-card-subtitle {
                font-size: 0.82rem;
            }

            .register-grid {
                row-gap: 7px;
            }

            .form-label {
                font-size: 0.78rem;
                margin-bottom: 3px;
            }

            .form-control,
            .form-select,
            .input-group-text,
            .password-toggle {
                min-height: 38px;
                font-size: 0.84rem;
            }

            textarea.form-control {
                min-height: 58px;
            }

            .btn-primary {
                min-height: 40px;
            }

            .form-footer {
                margin-top: 10px;
            }
        }

        @media (max-width: 991.98px) {
            .register-shell {
                padding: 0;
            }

            .register-card {
                height: auto;
                min-height: 100vh;
                max-height: none;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                overflow: visible;
            }

            .register-hero {
                height: auto;
                padding: 28px 22px;
            }

            .building-art {
                max-height: 150px;
            }

            .form-panel {
                height: auto;
                padding: 24px 18px 30px;
            }

            .register-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="register-shell d-flex align-items-center justify-content-center">
        <section class="register-card">
            <div class="row g-0 h-100">
                <div class="col-lg-4 d-none d-lg-flex">
                    <aside class="register-hero d-flex flex-column justify-content-between w-100">
                        <div>
                            <p class="eyebrow mb-2 text-white-50">Society Maintenance Management System</p>
                            <h1 class="hero-title fw-bold mb-3">Set up your society workspace faster.</h1>
                            <p class="hero-copy mb-0">Register the society, create the first admin account, and start managing members, dues, requests, and notices from one place.</p>

                            <div class="hero-metric-grid">
                                <div class="hero-metric">
                                    <strong>Members</strong>
                                    <span>Resident records</span>
                                </div>
                                <div class="hero-metric">
                                    <strong>Billing</strong>
                                    <span>Dues and receipts</span>
                                </div>
                                <div class="hero-metric">
                                    <strong>Requests</strong>
                                    <span>Complaint tracking</span>
                                </div>
                                <div class="hero-metric">
                                    <strong>Notices</strong>
                                    <span>Fast updates</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <svg class="building-art" viewBox="0 0 400 260" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <rect x="38" y="56" width="324" height="174" rx="14" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.28)" stroke-width="2"/>
                                <rect x="72" y="102" width="70" height="128" rx="6" fill="#fff" opacity="0.95"/>
                                <rect x="166" y="78" width="72" height="152" rx="6" fill="#fff" opacity="0.95"/>
                                <rect x="262" y="118" width="68" height="112" rx="6" fill="#fff" opacity="0.95"/>
                                <g fill="#dbeafe">
                                    <rect x="96" y="124" width="22" height="22" rx="3"/>
                                    <rect x="96" y="158" width="22" height="22" rx="3"/>
                                    <rect x="96" y="192" width="22" height="22" rx="3"/>
                                    <rect x="191" y="101" width="22" height="22" rx="3"/>
                                    <rect x="191" y="135" width="22" height="22" rx="3"/>
                                    <rect x="191" y="169" width="22" height="22" rx="3"/>
                                    <rect x="285" y="141" width="22" height="22" rx="3"/>
                                    <rect x="285" y="175" width="22" height="22" rx="3"/>
                                </g>
                                <circle cx="200" cy="42" r="18" fill="#fff" opacity="0.92"/>
                                <path d="M200 29 L209 48 L191 48 Z" fill="#0d6efd"/>
                            </svg>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-8">
                    <div class="form-panel d-flex align-items-center">
                        <div class="w-100">
                            <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                                <div>
                                    <p class="eyebrow text-secondary mb-1">Create society account</p>
                                    <h2 class="form-card-title fw-bold mb-1">Society Registration</h2>
                                    <p class="form-card-subtitle mb-0">Enter the core details to create your admin workspace.</p>
                                </div>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm rounded-3 flex-shrink-0">Log in</a>
                            </div>

                            <form class="needs-validation" method="POST" action="{{ route('society.register') }}" novalidate>
                                @csrf

                                <div class="register-grid">
                                    <div>
                                        <label for="name" class="form-label">Full Name</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Full name" required autocomplete="name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="mobile" class="form-label">Mobile Number</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                            <input type="tel" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="+91 98765 43210" autocomplete="tel">
                                            @error('mobile')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="email" class="form-label">Email Address</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="role" class="form-label">Role</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                                <option value="" disabled {{ old('role') ? '' : 'selected' }} hidden>Select role</option>
                                                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                                <option value="member" @selected(old('role') === 'member')>Member</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="field-full">
                                        <label for="society_name" class="form-label">Society Name</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                                            <input type="text" class="form-control @error('society_name') is-invalid @enderror" id="society_name" name="society_name" value="{{ old('society_name') }}" placeholder="Green Valley Housing Society">
                                            @error('society_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="field-full">
                                        <label for="address" class="form-label">Address</label>
                                        <div class="input-group align-items-start has-validation">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Street, area, city, PIN code">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                            <input type="password" class="form-control rounded-0 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Create password" required autocomplete="new-password" data-password-field>
                                            <button type="button" class="btn btn-outline-secondary password-toggle" data-password-toggle aria-label="Show password" tabindex="-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" class="form-control rounded-0 @error('password') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password" data-password-field>
                                            <button type="button" class="btn btn-outline-secondary password-toggle" data-password-toggle aria-label="Show password" tabindex="-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-footer d-flex align-items-center gap-3">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="bi bi-person-plus me-2"></i>Register Society
                                    </button>
                                    {{-- <p class="text-secondary small mb-0 d-none d-md-block">Already registered? Use the login button above.</p> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        (function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var group = btn.closest('.input-group');
                    var input = group && group.querySelector('[data-password-field]');
                    if (!input) return;
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    var icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
                    }
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            });

            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
