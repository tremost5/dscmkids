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

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>'.e($url['loc'])."</loc>\n";
            if (!empty($url['lastmod'])) {
                $xml .= '        <lastmod>'.e((string) $url['lastmod'])."</lastmod>\n";
            }
            $xml .= '        <priority>'.e((string) ($url['priority'] ?? '0.5'))."</priority>\n";
            $xml .= "    </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
