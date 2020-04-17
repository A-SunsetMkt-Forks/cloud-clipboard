<?php
// 读取设置，创建临时存储文件夹
$config = json_decode(file_get_contents('config.json'));
if (empty($config->server->storage)) exit('Please set a valid storage path in "config.json".');

// 检查分片上传大小限制
if ($config->file->chunk > 5242880) {
    $config->file->chunk = 5242880;
    echo "Upload chunk size is restricted to 5 MB.\n";
}

// 清空或创建临时存储文件夹
if (is_dir($config->server->storage)) {
    echo "Press enter to CLEAR ALL FILES in storage path and continue:\n";
    echo realpath($config->server->storage) . "\n";
    fgets(STDIN);

    // How do I recursively delete a directory and its entire contents (files + sub dirs) in PHP?
    // https://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir#answer-3352564
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($config->server->storage, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        ($fileinfo->isDir() ? 'rmdir' : 'unlink')($fileinfo->getRealPath());
    }
} else {
    mkdir($config->server->storage);
}

return $config;