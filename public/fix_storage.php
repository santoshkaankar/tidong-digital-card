<?php
$target = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
$shortcut = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';

if (!is_link($shortcut)) {
    symlink($target, $shortcut);
    echo "Symlink created successfully!";
} else {
    echo "Symlink already exists!";
}
?>