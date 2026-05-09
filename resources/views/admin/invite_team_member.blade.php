<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Dashboard</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:Arial, sans-serif;
            background:#f5f5f5;
        }

        .navbar{
            width:100%;
            background:#fff;
            border-bottom:2px solid #f39c12;
            padding:15px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .form-row{
            display:flex;
            gap:20px;
            margin-bottom:20px;
        }

        .form-group{
            flex:1;
            display:flex;
            flex-direction:column;
        }

        .form-group label{
            margin-bottom:8px;
            font-weight:bold;
            color:#333;
        }

        .form-group input{
            padding:12px 15px;
            font-size:16px;
            border:2px solid #ccc;
            border-radius:5px;
            outline:none;
            width:100%;
        }

        .form-group input:focus{
            border-color:#4da3ff;
        }

        .send-btn{
            background:#4da3ff;
            color:white;
            border:none;
            padding:12px 25px;
            font-size:18px;
            cursor:pointer;
            border-radius:5px;
        }
        .logo{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .logo-box{
            border:2px solid orange;
            color:orange;
            padding:5px 10px;
            font-weight:bold;
        }

        .logout-btn{
            background:none;
            border:none;
            font-size:15px;
            cursor:pointer;
        }

        .container{
            width:90%;
            margin:40px auto;
        }

        .card{
            background:#fff;
            border:2px solid #333;
            padding:20px;
            margin-bottom:40px;
        }

        .card-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .card-title{
            color:#007bff;
            font-size:30px;
            font-weight:bold;
        }

        .invite-btn{
            background:#4da3ff;
            color:white;
            border:none;
            padding:10px 25px;
            font-size:18px;
            cursor:pointer;
            border-radius:5px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th{
            text-align:left;
            padding:15px;
            border-bottom:2px solid #999;
            color:#777;
        }

        table td{
            padding:20px 15px;
            border-bottom:1px solid #ccc;
        }

        .company-email{
            color:#777;
            font-size:14px;
            margin-top:5px;
        }

        .stats{
            font-weight:bold;
            font-size:26px;
        }

        .url-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
        }

        .filter-box{
            display:flex;
            gap:10px;
            align-items:center;
        }

        select{
            padding:10px;
            border:2px solid #007bff;
            font-size:16px;
        }

        .download-btn{
            background:#4da3ff;
            border:none;
            color:white;
            padding:10px 20px;
            font-size:18px;
            cursor:pointer;
        }

        .pagination{
            margin-top:20px;
        }

        .pagination button{
            padding:8px 15px;
            border:none;
            background:#4da3ff;
            color:white;
            cursor:pointer;
            margin-right:10px;
        }

        .success-message{
            background:#d4edda;
            color:#155724;
            padding:12px;
            border-radius:5px;
            margin-bottom:15px;
            border:1px solid #c3e6cb;
        }
        .error-message{
            background:#f8d7da;
            color:#155724;
            padding:12px;
            border-radius:5px;
            margin-bottom:15px;
            border:1px solid #f5c6cb;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

    <!-- Navbar -->

    <div class="navbar">

        <div class="logo">

            <div class="logo-box">
                >URL<
            </div>

            <h3><a href="{{ route('admin.dashboard') }}">Dashboard</a></h3>

        </div>

        <div>

            Welcome, {{ Auth::user()->name }}

            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf

                <button type="submit" class="logout-btn">
                    Logout →
                </button>
            </form>

        </div>

    </div>


    <div class="container">

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <!-- Invite New Client Section -->

            <div class="card">

                <div class="card-header">
                    <div class="card-title">
                        Invite New Team Member
                    </div>
                </div>

                <form class="invite-form" action="{{ route('admin.save_invited_member') }}" method="POST">
                @csrf    
                    <div class="form-row">

                        <div class="form-group">
                            <label>User Name</label>
                            <input type="text" name="name" placeholder="User Name....">
                            @error('name')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="ex. sample@example.com">
                            @error('email')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control">
                                <option value="0">--- Select Role ---</option>
                                <option value="admin">Admin</option>
                                <option value="member">Member</option>
                            </select>
                            @error('role')
                            <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <button type="submit" class="send-btn">
                        Send Invitation
                    </button>

                </form>

            </div>
        
    </div>
</body>
</html>