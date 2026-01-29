<?php

namespace App\Utils;

use ZipArchive;

class ZipHelper
{
    public function compress(string $xmlPath, string $outputPath): bool
    {
        $zip = new ZipArchive();

        $res = $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res === TRUE) {
            $fileName = basename($xmlPath);
            $zip->addFile($xmlPath, $fileName);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}
