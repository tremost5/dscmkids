<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim((string) config('app.url'), '/');
        $urls = [
            ['loc' => $base.'/', 'priority' => '1.0'],
            ['loc' => $base.'/berita', 'priority' => '0.8'],
            ['loc' => $base.'/materi', 'priority' => '0.8'],
            ['loc' => $base.'/murid/arcade', 'priority' => '0.7'],
            ['loc' => $base.'/murid/progress', 'priority' => '0.7'],
        ];

        foreach (News::query()->where('is_published', true)->latest('published_at')->take(200)->get(['slug', 'updated_at']) as $news) {
            $urls[] = [
                'loc' => $base.'/berita/'.$news->slug,
                'lastmod' => optional($news->updated_at)->toAtomString(),
                'priority' => '0.6',
            ];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}

