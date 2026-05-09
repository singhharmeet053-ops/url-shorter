<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use App\Mail\InviteClientMail;

class SuperAdminController extends Controller
{
    public function superAdminDashboard()
    {
        $clinet_data = DB::table('companies')
        ->leftjoin('users', 'companies.id', '=', 'users.company_id')
        ->leftjoin('urls', 'companies.id', '=', 'urls.company_id')
        ->select(
            'companies.id',
            'users.name',
            'users.email',
            DB::raw('COUNT(DISTINCT users.id) as total_user'),
            DB::raw('COUNT(DISTINCT urls.id) as total_urls'),
            DB::raw('SUM((urls.clicks)) as total_click')
        )
        ->groupBy('companies.id','users.name','users.email')
        ->where('users.role','member')
        ->paginate(5);

        $shortUrls = DB::table('urls')
        ->leftJoin('users', 'urls.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'urls.original_url',
            'urls.short_code',
            'urls.clicks',
            'urls.created_at'
        )
        ->orderBy('urls.id', 'desc')
        ->paginate(5);

        return view('superadmin/dashboard',compact('clinet_data','shortUrls'));
    }

    public function clientInvitation()
    {
        return view('superadmin/client_invitation');
    }

    public function invitedNewClient(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $name = $request->name;
        $email = $request->email;

        $exists_email = DB::table('users')->where('email', $email)->exists();

        if($exists_email)
        {
            return back()->with('error','Email already exists');
        }

        $company_id = DB::table('companies')->insertGetId([
            'name' => $request->name,
            'domain' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $pass = Str::random(10);

        $new_client = DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'role' => 'member',
            'company_id' => $company_id,
            'password' => Hash::make($pass),
            'email_verified_at' => null,
            'remember_token' => null,
            'created_at' => now(),
        ]);    

        //$newpass = Hash::make($pass);

        Mail::to($email)->send(new InviteClientMail($name, $email, $pass));

        return back()->with('success','Invitaion has been sent on an email');
    }

    public function generatedUrls()
    {
        $shortUrls = DB::table('urls')
        ->leftJoin('users', 'urls.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'urls.original_url',
            'urls.short_code',
            'urls.clicks',
            'urls.created_at'
        )
        ->orderBy('urls.id', 'desc')
        ->paginate(5);

        return view('superadmin/generated_urls', compact('shortUrls'));
    }

    public function clientViewData()
    {
        $clinet_data = DB::table('companies')
        ->leftjoin('users', 'companies.id', '=', 'users.company_id')
        ->leftjoin('urls', 'companies.id', '=', 'urls.company_id')
        ->select(
            'companies.id',
            'users.name',
            'users.email',
            DB::raw('COUNT(DISTINCT users.id) as total_user'),
            DB::raw('COUNT(DISTINCT urls.id) as total_urls'),
            DB::raw('SUM((urls.clicks)) as total_click')
        )
        ->groupBy('companies.id','users.name','users.email')
        ->where('users.role','member')
        ->paginate(5);

        return view('superadmin.client_view_data', compact('clinet_data'));
    }

    public function downloadCsv(Request $request)
{
    $query = DB::table('urls')
        ->leftJoin('users', 'urls.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'users.role',
            'urls.original_url',
            'urls.short_code',
            'urls.clicks',
            'urls.created_at'
        );

    if ($request->filter == 'today') {
        $query->whereDate('urls.created_at', today());
    } elseif ($request->filter == 'last_week') {
        $query->whereBetween('urls.created_at', [now()->subWeek(), now()]);
    } elseif ($request->filter == 'this_month') {
        $query->whereMonth('urls.created_at', now()->month)
              ->whereYear('urls.created_at', now()->year);
    } elseif ($request->filter == 'last_month') {
        $query->whereMonth('urls.created_at', now()->subMonth()->month)
              ->whereYear('urls.created_at', now()->subMonth()->year);
    }
    
    $urls = $query->get();
    
    return response()->streamDownload(function () use ($urls) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, [
            'Name',
            'Long URL',
            'Short URL',
            'Hits',
            'Role',
            'Created At'
        ]);
        foreach ($urls as $url) {
            fputcsv($handle, [
                $url->name,
                $url->original_url,
                $url->original_url.'/'.$url->short_code,
                $url->clicks,
                $url->role,
                $url->created_at,
            ]);
        }
        fclose($handle);
    }, 'urls.csv');
}
}