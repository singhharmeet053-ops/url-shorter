<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function memberDashboard()
    {
        $urls = DB::table('urls')->where('user_id', Auth::id())->paginate(5);
        return view('member.dashboard', compact('urls'));
    }

    public function generateShortUrl()
    {
        return view('member.generate_short_url');
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

        $shortUrls = url('/member') . '/' . $short_code;

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

    public function downloadCsv(Request $request)
    {
        $query = DB::table('urls')
            ->where('user_id', Auth::id());

        if ($request->filter == 'today') {

            $query->whereDate('created_at', today());

        } elseif ($request->filter == 'last_week') {

            $query->whereBetween('created_at', [
                now()->subWeek(),
                now()
            ]);

        } elseif ($request->filter == 'this_month') {

            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);

        } elseif ($request->filter == 'last_month') {

            $query->whereMonth(
                'created_at',
                now()->subMonth()->month
            )->whereYear(
                'created_at',
                now()->subMonth()->year
            );
        }

        $urls = $query->get();

        return response()->streamDownload(function () use ($urls) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Original URL',
                'Short Code',
                'Clicks',
                'Created At'
            ]);

            foreach ($urls as $url) {

                fputcsv($handle, [
                    $url->id,
                    $url->original_url,
                    $url->original_url.'/member/'.$url->short_code,
                    $url->clicks,
                    $url->created_at,
                ]);
            }

            fclose($handle);

        }, 'urls.csv');
    }
}
