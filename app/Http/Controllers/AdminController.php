<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use App\Mail\InviteMemberMail;

class AdminController extends Controller
{
    public function adminDashboard()
    {
        $authid = Auth::id();

        $admin_urls = DB::table('urls')
        ->leftJoin('users', 'urls.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'urls.original_url',
            'urls.short_code',
            'urls.clicks',
            'urls.created_at'
        )
        ->where('urls.company_id', $authid)
        ->paginate(5);


        $team_member = DB::table('users')
        ->leftJoin('urls', 'users.company_id', '=', 'urls.company_id')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            DB::raw('COUNT(DISTINCT urls.id) as total_urls'),
            DB::raw('COALESCE(SUM(DISTINCT urls.clicks), 0) as total_clicks') 
        )
        ->where('users.company_id', Auth::id())
        ->groupBy('users.id', 'users.name', 'users.email', 'users.role')
        ->paginate(5);

        //echo "<pre>"; print_r($team_member); exit;

        return view('admin.dashboard', compact('admin_urls', 'team_member'));
    }

    public function generateShortUrl()
    {
        return view('admin.generate_short_url');
    }

    public function generatedUrl(Request $request)
    {
        $request->validate([
            'long_url' => 'required|url'
        ]);

        do
        {
            $short_code = Str::random(6);
        }
        while(
            DB::table('urls')->where('short_code', $short_code)->exists()
        );

        $insert_urls = DB::table('urls')->insert([
            'user_id' => Auth::id(),
            'company_id' => Auth::id(),
            'original_url' => $request->long_url,
            'short_code' => $short_code,
            'clicks' => 0,
            'created_at' => now()
        ]);    

        $shortUrls = url('/admin') . '/' . $short_code;

        return back()->with([
            'short_url' => $shortUrls,
            'long_url' => $request->long_url
        ]);
    }

    public function redirectUrl($code)
    {
        $url = DB::table('urls')->where('short_code', $code)->first();

        if(!$url)
        {
            abort(404);
        }

        DB::table('urls')->where('id', $url->id)->increment('clicks');

        return redirect($url->original_url);
    }

    public function ShortUrl()
    {
        $authid = Auth::id();

        $admin_urls = DB::table('urls')
        ->leftJoin('users', 'urls.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'urls.original_url',
            'urls.short_code',
            'urls.clicks',
            'urls.created_at'
        )
        ->where('urls.company_id', $authid)
        ->paginate(5);

        return view('admin.short_urls', compact('admin_urls'));
    }

    public function TeamMember()
    {
        $authid = Auth::id();

        $team_member = DB::table('users')
        ->leftJoin('urls', 'users.company_id', '=', 'urls.company_id')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            'users.role',
            DB::raw('COUNT(DISTINCT urls.id) as total_urls'),
            DB::raw('COALESCE(SUM(DISTINCT urls.clicks), 0) as total_clicks') 
        )
        ->where('users.company_id', $authid)
        ->groupBy('users.id', 'users.name', 'users.email', 'users.role')
        ->paginate(5);

        return view('admin.team_member', compact('team_member'));
    }

    public function InviteTeamMember()
    {
        return view('admin.invite_team_member');
    }

    public function saveInvitedMember(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'role' => 'required'
        ]);

        $name = $request->name;
        $email = $request->email;
        $role = $request->role;

        $exists_email = DB::table('users')->where('email', $email)->exists();

        if($exists_email)
        {
            return back()->with('error','Email already exists');
        }

        $pass = Str::random(10);

        $new_client = DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'role' => 'member',
            'company_id' => Auth::id(),
            'password' => Hash::make($pass),
            'email_verified_at' => null,
            'remember_token' => null,
            'created_at' => now(),
        ]);

        $password_new = Hash::make($pass);

        Mail::to($email)->send(new InviteMemberMail($name, $email, $pass));

        return back()->with('success', 'Invitaion has been sent on an email');
    }

    public function downloadCsv(Request $request)
    {
        $authid = Auth::id();

        $query = DB::table('urls')
            ->leftJoin('users', 'urls.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'urls.original_url',
                'urls.short_code',
                'urls.clicks',
                'urls.created_at'
            )
            ->where('urls.company_id', $authid);

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
                'Created At'
            ]);

            foreach ($urls as $url) {
               
                fputcsv($handle, [
                    $url->name,
                    $url->original_url,
                    $url->original_url.'/'.$url->short_code,
                    $url->clicks,
                    $url->created_at,
                ]);
            }

            fclose($handle);
        }, 'urls.csv');
    }
}