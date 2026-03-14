<?php
$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../src'));
foreach($it as $f){
    if (!$f->isFile()) continue;
    if (pathinfo($f, PATHINFO_EXTENSION)!=='php') continue;
    $path=$f->getPathname();
    $s=file_get_contents($path);
    $s=str_replace(["\r\n","\r"],"\n",$s);
    $s=str_replace("\t",'    ',$s);
    file_put_contents($path,$s);
}
echo "normalized\n";

