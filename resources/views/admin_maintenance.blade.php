<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', 'MyApp') }} - Admin Break</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .admin-container {
            text-align: center;
            padding: 40px;
            border: 1px solid #30363d;
            background-color: #161b22;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 10px;
            color: #58a6ff;
        }

        p {
            font-size: 14px;
            color: #8b949e;
            margin-bottom: 20px;
        }

        .footer {
            font-size: 12px;
            color: #6e7681;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <img src="https://technicul.com/images/technicul-logo.svg" alt="Admin Logo" class="logo">
        <h1>🔧 Admin Break in Progress 🔧</h1>
        <p>The admin panel is temporarily offline for scheduled maintenance. We'll be back shortly.</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ env('APP_NAME', 'MyApp') }}. Admin Area.
        </div>
    </div>
</body>
</html>
