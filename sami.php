<?php
$dir = __DIR__ . '/app';

$iterator = Symfony\Component\Finder\Finder::create()
->files()
->name('*.php')
->exclude(['Providers', 'Events'])
->in($dir);

$options = [
'theme'                => 'codeblock',
'title'                => 'Codeblock Documentation',
'build_dir'            => __DIR__ . '/storage/doc/build',
'cache_dir'            => __DIR__ . '/storage/doc/cache',
];

$sami = new Sami\Sami($iterator, $options);

$sami = new Sami\Sami($iterator, $options);
$templates = $sami['template_dirs'];
$templates[] = __DIR__ . '/resources/themes/';
$sami['template_dirs'] = $templates;


return $sami;