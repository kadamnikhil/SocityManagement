<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', 'MyApp') }} - Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }

        .card {
            background-color: #ffffff;
            color: #333;
            padding: 50px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: contain;
            margin-bottom: 20px;
            border: 3px solid #667eea;
            padding: 5px;
        }

        h1 {
            font-size: 28px;
            color: #4a5568;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
        }

        .footer {
            font-size: 12px;
            color: #a0aec0;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="card">
        <img src="https://img.freepik.com/free-vector/website-maintenance-abstract-concept-vector-illustration-website-service-webpage-seo-maintenance-web-design-corporate-site-professional-support-security-analysis-update-abstract-metaphor_335657-2295.jpg" alt="Logo" class="logo">
        <h1>🚧 Maintenance Mode 🚧</h1>
        <p>We're performing some upgrades to serve you better. Please check back soon.</p>
        <div class="footer">
            &copy; {{ date('Y') }} {{ env('APP_NAME', 'MyApp') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
