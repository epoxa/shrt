<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Click;
use App\Models\Url;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlController extends Controller
{

    /**
     * Show the form for creating a new link.
     */
    public function create()
    {
        $url = new Url(request()->query());
        if (empty($url->short_url)) {
            do {
                $url->short_url = Str::random(5);
            } while (
                Url::select('*')->where(['short_url' => $url->short_url])->exists()
            );
        }
        $bugs = request()->has('bugs') ? request('bugs') : [];
        return view('create-url', ['url' => $url, 'bugs' => $bugs]);
    }

    /**
     * Store a newly created link in storage.
     */
    public function save(Request $request)
    {
        $url = new Url(request()->all()); // Url is not modified
        $original_url = request('original_url');
        if (!preg_match('#^https?://#', $original_url)) {
            request()['original_url'] = "http://$original_url";
        }

        try {
            $this->validate($request, [
                'original_url' => 'bail|required|url',
                'short_url' => ['bail', 'required', 'regex:/^[0-9A-Za-z]+$/', 'min:5', 'max:20', 'not_in:stat', 'unique:App\Models\Url,short_url'],
            ], [
                'required' => 'Поле должно быть заполнено',
                'url' => 'Некорректный формат ссылки',
                'regex' => 'Можно только латинские буквы и цифры',
                'min' => 'Нужно минимум :min символов',
                'max' => 'Максимум :max символов',
                'unique' => 'Ссылка уже использована',
                'not_in' => 'Выберите другое слово',
            ]);
        } catch (ValidationException $e) {
            return redirect()->route('create', array_merge($url->attributesToArray(), ['bugs' => $e->errors()]));
        }

        $url = Url::create(request()->all()); // Original url may be changed
        return redirect()->route('stat', ['url' => $url->short_url]);
    }

    /**
     * Jump to the specified link.
     */
    public function click(Url $url)
    {
        if (empty($url)) abort(404);
        if ($url->valid_until && $url->valid_until < Carbon::now()->toDateTimeString()) abort(404);

        $click = new Click();
        $click->url_id = $url->id;
        $click->ip = request()->header('X-Real-IP') ?? request()->ip();

        if ($url->is_commercial) {
            $banner = Banner::getRandom();
            $click->banner_id = $banner->id;
            $click->save();
            return view('banner', [
                'banner_url' => request()->root() . "/banners/$banner->file_name",
                "url" => $url->original_url
            ]);
        } else {
            $click->save();
            return redirect($url->original_url);
        }
    }

    /**
     * Display the specified link statistics.
     */
    public function stat(Url $url = null)
    {
        list('ips_total' => $ips_total, 'clicks_total' => $clicks_total) = (array)DB::table('clicks')
            ->select(DB::raw('count(distinct ip) as ips_total, count(*) as clicks_total'))
            ->where('url_id', $url->id)
            ->get()[0];

        $clicks = Click::where('url_id', $url->id)->orderBy('created_at', 'desc')->paginate(10);

        return response()->view('url-stat', [
            'url' => $url,
            'time_remains' => $url->getTimeRemains(),
            'ips_total' => $ips_total,
            'clicks_total' => $clicks_total,
            'clicks' => $clicks,
        ])->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 0,
        ]);
    }

    public function globalStat()
    {
        list(
            'ips_total' => $ips_total, 'urls_total' => $urls_total,
            'clicks_total' => $clicks_total, 'clicks_unique' => $clicks_unique,
            ) = (array)DB::table('clicks')
            ->select(DB::raw('
                count(distinct url_id) as urls_total, count(distinct ip) as ips_total,
                count(distinct url_id, ip) as clicks_unique, count(*) as clicks_total
            '))
            ->where('created_at', '>', Carbon::createFromDate('14 days ago')->toDateTimeString())
            ->get()[0];

        return view('global-stat', [
            'urls_total' => $urls_total,
            'ips_total' => $ips_total,
            'clicks_total' => $clicks_total,
            'clicks_unique' => $clicks_unique,
        ]);
    }

    public function test()
    {
        return $_SERVER;
    }

}
