<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <style>
        body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            background:#f4f4f4;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .login-box{
            background:#fff;
            padding:30px;
            width:350px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        .login-box h2{
            text-align:center;
            margin-bottom:20px;
        }

        .input-group{
            margin-bottom:15px;
        }

        .input-group label{
            display:block;
            margin-bottom:5px;
        }

        .input-group input{
            width:100%;
            padding:10px;
            border:1px solid #ccc;
            border-radius:5px;
            box-sizing:border-box;
        }

        .btn{
            width:100%;
            padding:10px;
            background:#007bff;
            border:none;
            color:white;
            font-size:16px;
            border-radius:5px;
            cursor:pointer;
        }

        .btn:hover{
            background:#0056b3;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    @if(session('error'))
        <span style="color:red;">{{ session('error') }}</span>
    @endif

    <form action="{{ route('loggedin') }}" method="POST">
        @csrf

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter Email" required>

            @error('email')
                <span style="color:red;">{{ $message }}</span>
            @enderror
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>

            @error('password')
                <span style="color:red;">{{ $message }}</span>
            @enderror
        </div>

        <input type="submit" class="btn" value="Login">
    </form>
</div>

</body>
</html>