const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
function edit(name, fn) {
  const file = path.join(root, name);
  fs.writeFileSync(file, fn(fs.readFileSync(file, 'utf8')));
}
edit('app.php', text => {
  const routes = ['refund-policy', 'internship-placement-policy', 'certificate-policy', 'course-information'];
  const block = routes.map(slug => `$router->get('/${slug}', ['TCM\\Controllers\\PublicWorkshopController', 'policy']);`).join('\n');
  return text.replace("$router->get('/privacy',", block + "\n$router->get('/privacy',");
});
edit('src/Controllers/PublicWorkshopController.php', text => text.replace('    public function partnerPolicy(): void', `    /** Serve only the explicitly allowed public information pages. */
    public function policy(): void
    {
        $pages = [
            '/refund-policy' => 'refund-policy.html',
            '/internship-placement-policy' => 'internship-placement-policy.html',
            '/certificate-policy' => 'certificate-policy.html',
            '/course-information' => 'course-information.html',
        ];
        $file = $pages[Request::path()] ?? null;
        if ($file === null || !is_file(BASE_PATH . '/' . $file)) {
            http_response_code(404);
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        readfile(BASE_PATH . '/' . $file);
    }

    public function partnerPolicy(): void`));
edit('views/partials/public-footer.html', text => text.replace('        <a href="{{base}}/partner-policy.html">', `        <a href="{{base}}/refund-policy.html">Refund &amp; Cancellation</a>
        <a href="{{base}}/course-information.html">Course Fees &amp; Delivery</a>
        <a href="{{base}}/internship-placement-policy.html">Internship &amp; Placement</a>
        <a href="{{base}}/certificate-policy.html">Certificate Policy</a>
        <a href="{{base}}/partner-policy.html">`));
edit('sitemap.php', text => text.replace('    //POLICIES', '    //POLICIES').replace("    ['loc' => '/sitemap.html',", `    ['loc' => '/refund-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/internship-placement-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/certificate-policy', 'lastmod' => $today, 'changefreq' => 'yearly', 'priority' => '0.3'],
    ['loc' => '/course-information', 'lastmod' => $today, 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => '/sitemap.html',`));
console.log('Registered policy routes, shared footer links and XML sitemap.');
