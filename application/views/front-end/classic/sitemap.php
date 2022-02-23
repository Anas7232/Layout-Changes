<?= '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= base_url(); ?></loc>
        <priority>1.0</priority>
    </url>
    <?php foreach ($product_slugs as $slug) { ?>
        <url>
            <loc><?= base_url() . 'products/details/' . $slug ?></loc>
            <priority>0.9</priority>
        </url>
    <?php } ?>
    <url>
        <loc><?= base_url("products"); ?></loc>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= base_url("home/categories"); ?></loc>
        <priority>0.7</priority>
    </url>
    <?php foreach ($categories_slugs as $slug) { ?>
        <url>
            <loc><?= base_url() . 'products/category/' . $slug ?></loc>
            <priority>0.7</priority>
        </url>
    <?php } ?>

</urlset>