<?php
namespace App\Lib\Assets;

class RenderAsset
{
    public static function fromUri($filePath)
    {
        $contentType = 'application/octet-stream'; // Default content type

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        $mimeTypes = [
            'EDI' => 'application/EDI-X12',
            'EDIFACT' => 'application/EDIFACT',
            'js' => 'application/javascript',
            'bin' => 'application/octet-stream',
            'ogg' => 'application/ogg',
            'pdf' => 'application/pdf',
            'xhtml' => 'application/xhtml+xml',
            'swf' => 'application/x-shockwave-flash',
            'json' => 'application/json',
            'jsonld' => 'application/ld+json',
            'xml' => 'application/xml',
            'zip' => 'application/zip',
            'form-urlencoded' => 'application/x-www-form-urlencoded',
            'mp3' => 'audio/mpeg',
            'wma' => 'audio/x-ms-wma',
            'ra' => 'audio/vnd.rn-realaudio',
            'wav' => 'audio/x-wav',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'ico' => 'image/vnd.microsoft.icon',
            'djvu' => 'image/vnd.djvu',
            'svg' => 'image/svg+xml',
            'mixed' => 'multipart/mixed',
            'alternative' => 'multipart/alternative',
            'related' => 'multipart/related',
            'form-data' => 'multipart/form-data',
            'css' => 'text/css',
            'csv' => 'text/csv',
            'html' => 'text/html',
            'javascript' => 'text/javascript',
            'plain' => 'text/plain',
            'mpeg' => 'video/mpeg',
            'mp4' => 'video/mp4',
            'quicktime' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'avi' => 'video/x-msvideo',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xul' => 'application/vnd.mozilla.xul+xml'
        ];


        if (array_key_exists($fileExtension, $mimeTypes)) {
            $contentType = $mimeTypes[$fileExtension];
        }
        header("Content-Type: $contentType");
        readfile($filePath);
    }
}
