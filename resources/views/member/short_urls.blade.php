<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Admin Dashboard</title>

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
            font-size:26px;
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
            font-size:20px;
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

    </style>
</head>
<body>

    <!-- Navbar -->

    <div class="navbar">

        <div class="logo">

            <div class="logo-box">
                >URL<
            </div>

            <h3><a href="{{ route('member.dashboard') }}">Dashboard</a></h3>

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

        <!-- Clients Section -->

        <div class="card">

            <div class="card-header">

                <div class="card-title">
                    Generated Short URLs <a href="{{ route('admin.generate_short_url') }}" class="download-btn">Generate</a>
                </div>

                <div class="filter-box">

                    <select>
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>Last Week</option>
                        <option>Today</option>
                    </select>

                    <button class="download-btn">
                        Download
                    </button>

                </div>

            </div>

            <table>

                <thead>

                    <tr>
                        <th>Client Name</th>
                        <th>Users</th>
                        <th>Total Generated URLs</th>
                        <th>Total URL Hits</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>
                            <strong>ASP Corp.</strong>

                            <div class="company-email">
                                client@example.com
                            </div>
                        </td>

                        <td class="stats">11</td>

                        <td class="stats">1200</td>

                        <td class="stats">5200</td>

                    </tr>


                    <tr>

                        <td>
                            <strong>Sembark Tech</strong>

                            <div class="company-email">
                                sembark@example.com
                            </div>
                        </td>

                        <td class="stats">5</td>

                        <td class="stats">1200</td>

                        <td class="stats">1000</td>

                    </tr>

                </tbody>

            </table>

            <div class="pagination">

                Showing 2 of total 1200

                <button>Prev</button>

                <button>Next</button>

            </div>

        </div>
    </div>

</body>
</html>