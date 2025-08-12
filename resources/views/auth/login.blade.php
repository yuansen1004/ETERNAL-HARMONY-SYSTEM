<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 3fr 2fr;
            width: 90%;
            max-width: 1200px;
            min-height: 600px;
            background-color: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .login-left-section {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px 50px;
            background-color: #fff;
        }

        .login-left-section .logo {
            align-self: flex-start;
            margin-bottom: 20px;
        }

        .login-left-section .logo img {
            max-width: 150px;
            height: auto;
        }

        .login-form-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding-bottom: 50px;
        }

        .login-form-container h2 {
            text-align: left;
            margin-bottom: 30px;
            color: #333;
            font-size: 2.2em;
            font-weight: bold;
        }

        .login-right-section {
            background-color: #e0f2f7;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .login-right-section img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%;
        }
        .form-group:last-of-type {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1.1em;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .form-group input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.1);
        }
        .form-group .remember-me-label {
            color: #666;
            font-size: 0.95em;
        }

        .form-group .error-message {
            color: #e74c3c;
            font-size: 0.85em;
            margin-top: 8px;
        }

        .btn-primary {
            width: 100%;
            padding: 12px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .forgot-password {
            text-align: right;
            margin-top: 10px;
            font-size: 0.9em;
        }
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }

        @media (max-width: 992px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                width: 90%;
                max-width: 500px;
                min-height: auto;
            }

            .login-right-section {
                display: none;
            }

            .login-left-section {
                padding: 30px;
                min-height: 500px;
                justify-content: center;
            }

            .login-left-section .logo {
                text-align: center;
                align-self: center;
                margin-bottom: 30px;
            }

            .login-form-container {
                padding-bottom: 0;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
                width: 95%;
                border-radius: 8px;
                box-shadow: none;
            }
            .login-left-section {
                padding: 20px;
            }
            .login-form-container h2 {
                font-size: 1.8em;
            }
            .btn-primary {
                padding: 10px;
                font-size: 1em;
            }
        }

    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left-section">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Your Company Logo">
            </div>

            <div class="login-form-container">
                <h2>Login</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember" class="remember-me-label">Remember me</label>
                    </div>

                    <button type="submit" class="btn-primary">Login</button>

                    @if (Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">Forgot your password?</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="login-right-section">
            <img src="{{ asset('images/logo.png') }}" alt="Login Illustration">
        </div>
    </div>
</body>
</html>