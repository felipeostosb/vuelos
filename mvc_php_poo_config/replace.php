<?php
$directory = new RecursiveDirectoryIterator(__DIR__ . '/views');
$iterator = new RecursiveIteratorIterator($directory);

$replacements = [
    '#0A192F' => '#3C5973',
    '#0070F3' => '#0583F2',
    '#0051CC' => '#3076B3',
    '#ef9912' => '#F29705',
    '#d6880e' => '#c47903',
    'text-yellow-500' => 'text-[#9D7638]'
];

$updatedFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        foreach ($replacements as $search => $replace) {
            $content = str_ireplace($search, $replace, $content);
        }
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            $updatedFiles[] = $file->getPathname();
        }
    }
}
echo "Updated " . count($updatedFiles) . " files.\n";
foreach($updatedFiles as $f) { echo $f . "\n"; }
?>
