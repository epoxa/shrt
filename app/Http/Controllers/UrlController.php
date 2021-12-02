<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Click;
use App\Models\Url;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
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
                || $url->short_url === 'stat'
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
        $url = new Url(request()->all()); // Original url may be changed
        $url->save();
        return redirect()->route('stat', ['url' => $url->short_url]);
    }

    /**
     * Jump to the specified link.
     */
    public function click(Url $url)
    {
        if (empty($url)) abort(404);
        if ($url->valid_until && $url->valid_until < Date::create()) abort(404);
        $click = new Click();
        $click->url_id = $url->id;
        $click->ip = request()->ip();
        if ($url->is_commercial) {
            $file_name = $this->getRandomBanner();
            $click->banner_id = Banner::firstOrCreate(['file_name' => $file_name])->id;
            $click->save();
            return view('banner', ['banner_url' => request()->root() . "/banners/$file_name", "url" => $url->original_url]);
        } else {
            $click->save();
            return redirect($url->original_url);
        }
    }

    /**
     * Display the specified link statistics.
     */
    public function stat(Url $url)
    {
        return view('url-stat', ['url' => $url]);
    }

    public function getRandomBanner(): string
    {
        $banners = glob(public_path() . '/banners/*.jpg');
        $idx = rand(0, count($banners) - 1);
        preg_match('#/([^/]+\.jpg)$#', $banners[$idx], $a);
        return $a[1];
    }

}
