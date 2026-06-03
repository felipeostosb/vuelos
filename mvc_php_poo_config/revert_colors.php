<?php
$directory = new RecursiveDirectoryIterator(__DIR__ . '/views');
$iterator = new RecursiveIteratorIterator($directory);

// Reverting everything back to the original Tailwind blue and slate
$replacements = [
    '#3C5973' => '#0A192F',
    '#0583F2' => '#0070F3',
    '#3076B3' => '#0051CC',
    '#F29705' => '#0070F3', // Revert custom oranges back to original blue
    '#ef9912' => '#0070F3', 
    '#c47903' => '#0051CC', // Revert custom hover oranges back to original hover blue
    '#d6880e' => '#0051CC',
    'text-[#9D7638]' => 'text-yellow-500'
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
echo "Reverted " . count($updatedFiles) . " files.\n";
foreach($updatedFiles as $f) { echo $f . "\n"; }
?>
