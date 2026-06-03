<?php
$src = '/home/prozac/.gemini/antigravity/brain/329b1245-21c3-4274-84c5-5acefa494608/hero_paris_side_1780449975614.png';
$destDir = __DIR__ . '/assets/img';
if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
}
$dest = $destDir . '/hero_paris.png';
if (copy($src, $dest)) {
    echo "Copied successfully to " . $dest . "\n";
} else {
    echo "Failed to copy " . $src . "\n";
}
?>
