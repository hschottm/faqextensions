<?php

// Run against an installed Contao 5.3 application without changing its files or database:
// CONTAO_APP=/path/to/contao php tests/integration/render.php
$application = getenv('CONTAO_APP');
if (!$application || !is_file($application.'/vendor/autoload.php')) {
    throw new RuntimeException('Set CONTAO_APP to an installed Contao 5.3 application.');
}
$loader = require $application.'/vendor/autoload.php';
$extension = dirname(__DIR__, 2);
$loader->addPsr4('Hschottm\\FaqExtensionsBundle\\', $extension.'/src');
foreach (glob($extension.'/src/Resources/contao/{modules,models}/*.php', GLOB_BRACE) as $file) {
    $loader->addClassMap(['Hschottm\\FaqExtensionsBundle\\'.basename($file, '.php') => $file]);
}

$project = sys_get_temp_dir().'/faqextensions-twig-'.bin2hex(random_bytes(6));
$filesystem = new Symfony\Component\Filesystem\Filesystem();
$filesystem->mkdir([$project.'/config', $project.'/templates', $project.'/public', $project.'/system/config']);
symlink($application.'/vendor', $project.'/vendor');
copy($application.'/composer.json', $project.'/composer.json');
file_put_contents($project.'/config/routes.yaml', "faqextensions:\n    resource: '".$extension."/src/Resources/config/routes.yaml'\n");
file_put_contents($project.'/templates/mod_faqtoplist.html.twig', "{% extends '@Contao/mod_faqtoplist.html.twig' %}{% block content %}{{ parent() }}<p class=\"custom-override\">Override</p>{% endblock %}");
file_put_contents($project.'/config/config.yaml', "framework:\n  secret: faqextensions-render-test\ndoctrine:\n  dbal:\n    connections:\n      default:\n        driver: pdo_sqlite\n        url: 'sqlite://localhost/%kernel.project_dir%/test.db'\n");

class FaqRenderingKernel extends Contao\ManagerBundle\HttpKernel\ContaoKernel
{
    public function registerBundles(): array
    {
        return [...parent::registerBundles(), new Hschottm\FaqExtensionsBundle\HschottmFaqExtensionsBundle()];
    }
}

FaqRenderingKernel::setProjectDir($project);
$kernel = new FaqRenderingKernel('prod', false);
try {
    $kernel->boot();
    $container = $kernel->getContainer();
    $container->get('contao.framework')->initialize();
    $twig = $container->get('twig');
    $request = Symfony\Component\HttpFoundation\Request::create('https://example.org/faq?keep=1');
    $request->attributes->set('_scope', 'frontend');
    $request->setLocale('en');
    $responseContext = new Contao\CoreBundle\Routing\ResponseContext\ResponseContext();
    $jsonLd = new Contao\CoreBundle\Routing\ResponseContext\JsonLd\JsonLdManager($responseContext);
    $responseContext->add($jsonLd);
    $request->attributes->set('_contao_response_context', $responseContext);
    $container->get('request_stack')->push($request);
    $container->get('translator')->setLocale('en');
    Contao\Environment::set('uri', 'https://example.org/faq?keep=1');
    $db = $container->get('database_connection');
    $db->executeStatement("CREATE TABLE tl_faq (id INTEGER PRIMARY KEY, alias TEXT, pid INTEGER DEFAULT 1, question TEXT DEFAULT 'Question', published INTEGER DEFAULT 1, showhelpful INTEGER DEFAULT 1, helpful INTEGER DEFAULT 0, nothelpful INTEGER DEFAULT 0)");
    $db->insert('tl_faq', ['id' => 1, 'alias' => '1']);
    $db->executeStatement('CREATE TABLE tl_faq_vote (id INTEGER PRIMARY KEY, pid INTEGER, voter TEXT, tstamp INTEGER, UNIQUE(pid,voter))');
    $checks = 0;
    $check = static function (bool $ok, string $message) use (&$checks): void {
        if (!$ok) { throw new RuntimeException($message); }
        ++$checks;
    };
    $render = static function (string $name, array $data): string {
        // Exercise the same PHP-to-Twig bridge used by Contao's frontend modules.
        $template = new Contao\FrontendTemplate($name);
        $template->setData($data);
        return $template->parse();
    };
    foreach (['top', 'recent', 'recommended', 'helpful'] as $kind) {
        $name = 'mod_faq'.$kind.'list';
        $html = $render($name, ['faq' => [], 'empty' => 'No FAQs', 'class' => $name]);
        $check(str_contains($html, '<p class="empty">No FAQs</p>'), "$name: empty state");
        $html = $render($name, [
            'class' => $name, 'headline' => 'Questions', 'hl' => 'h2',
            'cssID' => ' id="faq-list"', 'pagination' => '<nav>Page 2</nav>',
            'faq' => [['class' => 'first last', 'href' => '/faq?a=1&amp;b=2', 'title' => 'A &amp; B', 'question' => '&lt;script&gt;alert(1)&lt;/script&gt; &amp; B']],
        ]);
        $check(str_contains($html, '<nav>Page 2</nav>'), "$name: pagination markup");
        $check(str_contains($html, 'id="faq-list"'), "$name: module wrapper");
        $check(str_contains($html, '<h2>Questions</h2>'), "$name: headline");
        $check(!str_contains($html, '<script>') && !str_contains($html, '&amp;amp;'), "$name: escaping");
        if ($kind === 'top') {
            $check(str_contains($html, 'class="custom-override"'), 'Application override extends bundle template');
        }
    }
    $data = [
        'class' => 'mod_faqreader', 'question' => 'A &amp; B', 'answer' => '<p>Rich <strong>answer</strong></p>',
        'id' => 1, 'alias' => '1', 'showhelpful' => true, 'helpful' => 2, 'nothelpful' => 1,
        'info' => 'Author', 'addImage' => true, 'addBefore' => false,
        'picture' => ['img' => ['src' => '/test.png', 'srcset' => '/test.png', 'width' => 10, 'height' => 10], 'alt' => 'Example'],
        'caption' => 'Image caption',
        'enclosure' => [['extension' => 'pdf', 'href' => '/download?file=1&amp;x=2', 'title' => 'Download', 'link' => 'Guide.pdf', 'filesize' => '2 KiB']],
        'referer' => '/faq', 'back' => 'Back', 'allowComments' => true, 'hlc' => 'h2', 'hlcText' => 'Comments',
        'comments' => ['<article>Existing comment</article>'], 'pagination' => '<nav>Comments page</nav>',
        'addComment' => 'Add comment', 'formId' => 'comments', 'requestToken' => 'test-token', 'fields' => [], 'submit' => 'Submit',
        'getSchemaOrgData' => static fn () => ['@type' => 'FAQPage', 'mainEntity' => [['@type' => 'Question', 'name' => 'A & B']]],
    ];
    $html = $render('mod_faqreader', $data);
    $check(str_contains($html, '<strong>answer</strong>'), 'Reader: rich answer');
    $check(str_contains($html, '<img src="/test.png"'), 'Reader: core image/picture rendering');
    $check(strpos($html, '<strong>answer') < strpos($html, '<img'), 'Reader: image after answer');
    $check(str_contains($html, 'Guide.pdf') && str_contains($html, '2 KiB'), 'Reader: enclosure');
    $check(str_contains($html, '2 out of 3 found this article helpful'), 'Reader: translated vote count');
    $check(str_contains($html, '/_faqextensions/en/vote/1/1') && str_contains($html, '/_faqextensions/en/vote/1/-1'), 'Reader: real insert tag URLs');
    $check(!str_contains($html, '{{faq_') && !str_contains($html, '&amp;amp;'), 'Reader: insert tags resolved and escaped once');
    $check(str_contains($html, '<article>Existing comment</article>') && str_contains($html, 'name="REQUEST_TOKEN"'), 'Reader: comments and core form');
    $check(str_contains($html, '<p class="back">'), 'Reader: overview link');
    $check(str_contains($jsonLd->collectFinalScriptFromGraphs(), 'FAQPage'), 'Reader: schema.org data');
    $data['addBefore'] = true;
    $data['showhelpful'] = false;
    $data['allowComments'] = false;
    $data['referer'] = '';
    $html = $render('mod_faqreader', $data);
    $check(strpos($html, '<img') < strpos($html, '<strong>answer'), 'Reader: image before answer');
    $check(!str_contains($html, 'class="helpful"') && !str_contains($html, 'ce_comments') && !str_contains($html, 'class="back"'), 'Reader: optional sections disabled');
    $data['showhelpful'] = true;
    $responseContext = new Contao\CoreBundle\Routing\ResponseContext\ResponseContext();
    $jsonLd = new Contao\CoreBundle\Routing\ResponseContext\JsonLd\JsonLdManager($responseContext);
    $responseContext->add($jsonLd);
    $request->attributes->set('_contao_response_context', $responseContext);
    $html = $render('mod_faqpage', [
        'class' => 'mod_faqpage', 'faq' => [['headline' => 'Category', 'items' => [$data]]],
        'request' => '/faq', 'topLink' => 'Top', 'getSchemaOrgData' => $data['getSchemaOrgData'],
    ]);
    $check(str_contains($html, '<h2>Category</h2>') && str_contains($html, '<h3 id="1">A &amp; B</h3>'), 'Page: category and question');
    $check(str_contains($html, '<img src="/test.png"') && str_contains($html, 'Guide.pdf'), 'Page: image and enclosure context');
    $check(str_contains($html, '/_faqextensions/en/vote/1/1') && str_contains($html, '/faq#top'), 'Page: voting and top link');
    $check(str_contains($jsonLd->collectFinalScriptFromGraphs(), 'FAQPage'), 'Page: independent schema.org registration');
    $html = $render('mod_faqpage', ['faq' => [], 'getSchemaOrgData' => $data['getSchemaOrgData']]);
    $check(!str_contains($html, '<article>'), 'Page: empty categories');
    $html = $render('mod_faqreader', ['error' => '<p>Not found</p>']);
    $check(str_contains($html, '<p>Not found</p>'), 'Reader: error state without schema callback');
    $container->get('translator')->setLocale('de');
    $html = $render('mod_faqreader', $data);
    $check(str_contains($html, 'War dieser Artikel hilfreich?'), 'German voting question');
    $check(str_contains($html, '>ja</a>') && str_contains($html, '>nein</a>'), 'German voting labels');
    $data['alias'] = '';
    $html = $render('mod_faqreader', $data);
    $check(str_contains($html, '/_faqextensions/en/vote/1/1'), 'Voting supports ID when alias is empty');
    $container->get('translator')->setLocale('en');
    $container->get('request_stack')->pop();
    $call = static function (string $method, array $cookies = [], array $body = [], string $return = '/faq', int $vote = 1) use ($kernel): Symfony\Component\HttpFoundation\Response {
        $signature = (new Hschottm\FaqExtensionsBundle\Voting\VoterIdentity('faqextensions-render-test'))->voteSignature(1, $vote);
        $request = Symfony\Component\HttpFoundation\Request::create('https://example.org/_faqextensions/en/vote/1/'.$vote.'?signature='.$signature.'&return='.rawurlencode($return), $method, $body, $cookies);
        return $kernel->handle($request);
    };
    $unsigned = $kernel->handle(Symfony\Component\HttpFoundation\Request::create('https://example.org/_faqextensions/en/vote/1/1'));
    $check($unsigned->getStatusCode() === 403 && !str_contains($unsigned->getContent(), 'Question'), 'Voting: unsigned links cannot expose a question');
    $response = $call('GET');
    $check($response->getStatusCode() === 200 && str_contains($response->getContent(), 'Submit rating'), 'Voting: confirmation form');
    $check($response->headers->hasCacheControlDirective('no-store') && $response->headers->hasCacheControlDirective('private'), 'Voting: never shared-cache identity or tokens');
    $cookies = [];
    foreach ($response->headers->getCookies() as $responseCookie) {
        $cookies[$responseCookie->getName()] = $responseCookie->getValue();
        if ($responseCookie->getName() === Hschottm\FaqExtensionsBundle\Voting\VoterIdentity::COOKIE) { $cookie = $responseCookie; }
    }
    $check($cookie->isHttpOnly() && $cookie->isSecure() && $cookie->getSameSite() === 'lax', 'Voting: secure persistent cookie');
    preg_match('/name="REQUEST_TOKEN" value="([^"]+)"/', $response->getContent(), $matches);
    $token = html_entity_decode($matches[1]);
    $check((int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1') === 0, 'Voting: GET cannot vote');
    $response = $call('POST', [], ['REQUEST_TOKEN' => $token]);
    $check($response->getStatusCode() === 400, 'Voting: cookieless POST rejected');
    $response = $call('POST', [$cookie->getName() => 'forged'], ['REQUEST_TOKEN' => $token]);
    $check($response->getStatusCode() === 400, 'Voting: forged identity rejected');
    $response = $call('POST', $cookies, ['REQUEST_TOKEN' => 'invalid']);
    $check($response->getStatusCode() === 400, 'Voting: invalid CSRF rejected');
    $check((int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1') === 0, 'Voting: rejected requests leave counters unchanged');
    $response = $call('POST', $cookies, ['REQUEST_TOKEN' => $token]);
    $check($response->getStatusCode() === 200 && str_contains($response->getContent(), 'Your rating has been saved'), 'Voting: valid POST accepted');
    $response = $call('POST', $cookies, ['REQUEST_TOKEN' => $token]);
    $check(str_contains($response->getContent(), 'already rated'), 'Voting: repeat POST explained');
    $response = $call('POST', $cookies, ['REQUEST_TOKEN' => $token], '/faq', -1);
    $check(str_contains($response->getContent(), 'already rated'), 'Voting: opposite vote also rejected');
    $check((int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1') === 1 && (int) $db->fetchOne('SELECT nothelpful FROM tl_faq WHERE id=1') === 0, 'Voting: only one vote counted');
    $response = $call('GET', [], [], '//evil.example');
    $check(str_contains($response->getContent(), 'href="/"'), 'Voting: external return URL rejected');
    $db->executeStatement('UPDATE tl_faq SET showhelpful=0 WHERE id=1');
    $response = $call('POST', $cookies, ['REQUEST_TOKEN' => $token]);
    $check($response->getStatusCode() === 404, 'Voting: disabled answer unavailable');
    $schema = Contao\DcaExtractor::getInstance('tl_faq_vote')->getDbInstallerArray();
    $check(isset($schema['TABLE_FIELDS']['voter']), 'Migration: voter field discovered from bundle DCA');
    $check(str_contains(implode(' ', $schema['TABLE_CREATE_DEFINITIONS']), 'UNIQUE'), 'Migration: unique voter claim index');
    echo "OK ($checks integration assertions, Contao ".Composer\InstalledVersions::getPrettyVersion('contao/core-bundle').")\n";
} finally {
    $kernel->shutdown();
    $filesystem->remove($project);
}
