<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Files
{
    public static function humanFileSize($size, $unit = "")
    {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2, '.', ' ') . " GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2, '.', ' ') . " MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2, '.', ' ') . " KB";
        return number_format($size, 0, '.', ' ') . " bytes";
    }

    public static function stringToBytes($str)
    {
        $val = trim($str);
        if (is_numeric($val)) {
            return (float) $val;
        }

        $last = strtolower($val[strlen($val) - 1]);
        $val = substr($val, 0, -1);

        $val = (float) $val;
        switch ($last) {
            case 't':
                $val *= 1024;
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    public static function mimeType($file)
    {
        return Storage::disk('public')->mimeType($file->path());
    }

    public static function isDisplayableImage($mimeType)
    {
        return in_array($mimeType, [
            "image/png",
            "image/jpeg",
            "image/gif",
            "image/bmp",
            "image/tiff",
            "image/x-icon",
            "image/svg+xml",
            "image/webp",
        ], false);
    }

    /*
     * Possible types : image, video, audio, text, font, pdf, zip, file.
     */
    public static function simplifyMimeType($mimeType)
    {
        $explode = explode("/", $mimeType);

        switch ($mimeType) {
            case "application/pdf":
                return "pdf";
            case "application/x-bzip":
            case "application/x-bzip2":
            case "application/x-rar-compressed":
            case "application/x-tar":
            case "application/x-zip":
            case "application/x-7r-compressed":
                return "zip";
            case "application/json":
            case "application/javascript":
            case "application/typescript":
            case "application/xml":
                return "text";
            default:
                if ($explode[0] == "application") return "file";
                return $explode[0];
        }
    }
}