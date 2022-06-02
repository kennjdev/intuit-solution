<?php

namespace IntuitSolutions\BigCommerce;

use IntuitSolutions\BigCommerce\File;


function params_to_multipart($params)
{
    return array_map(function ($v, $k) {
        if ($v instanceof File) {
            return file_to_multipart_entry($k, $v);
        } else {
            return [
                'name' => $k,
                'contents' => $v
            ];
        }
    }, array_values($params), array_keys($params));
}

function file_to_multipart_entry($key, File $file)
{
    return [
        'name' => $key,
        'contents' => $file->getStream(),
        'headers' => [
            'Content-Type' => $file->getContentType()
        ],
        'filename' => $file->getFilename()
    ];
}

function params_has_file($params)
{
    foreach ($params as $v) {
        if ($v instanceof File) {
            return true;
        }
    }

    return false;
}
