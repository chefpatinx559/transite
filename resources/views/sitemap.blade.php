<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @foreach($staticPages as $page)
    <url>
        <loc>{{ $baseUrl . $page['url'] }}</loc>
        <changefreq>{{ $page['changefreq'] }}</changefreq>
        <priority>{{ $page['priority'] }}</priority>
    </url>
    @endforeach

    @foreach($posts as $post)
    <url>
        <loc>{{ $baseUrl }}/blog/{{ $post->slug }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach($products as $product)
    <url>
        <loc>{{ $baseUrl }}/boutique/{{ $product->slug }}</loc>
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach($formations as $formation)
    <url>
        <loc>{{ $baseUrl }}/formations/{{ $formation->slug }}</loc>
        <lastmod>{{ $formation->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

</urlset>
