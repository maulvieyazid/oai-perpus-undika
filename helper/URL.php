<?php

namespace Helper;

class URL
{
    /** Untuk mempermudah memahami function2 yang ada maka,
     * disini hanya akan ditampilkan output dari function2 yang ada
     * contoh kasus URL yang digunakan adalah sebagai berikut :
     */
    /* https://example.com/index.php?coba=coba&foo=bar */


    function getScheme()
    {
        /* Output : https */
        return $_SERVER['REQUEST_SCHEME'];
    }

    function getHost()
    {
        /* Output : example.com */
        return $_SERVER['HTTP_HOST'];
    }

    function getRequestUri()
    {
        /* Output : /index.php?coba=coba&foo=bar */
        return $_SERVER["REQUEST_URI"];
    }

    function getQueryParam()
    {
        /* Output : coba=coba&foo=bar */
        return $_SERVER['QUERY_STRING'];
    }

    function getRequestUriWithoutQuery()
    {
        /* Output : /index.php */
        return strtok($_SERVER["REQUEST_URI"], '?');
    }

    static function getUrl($full = false)
    {
        $url = new self;

        // Jika full
        if ($full) {
            /* Output : https://example.com/index.php?coba=coba&foo=bar */
            return "{$url->getScheme()}://{$url->getHost()}{$url->getRequestUri()}";
        }

        // Jika tidak full, maka tanpa query param
        /* Output : https://example.com/index.php */
        return "{$url->getScheme()}://{$url->getHost()}{$url->getRequestUriWithoutQuery()}";
    }

    static function getFullUrl()
    {
        /* Output : https://example.com/index.php?coba=coba&foo=bar */
        return self::getUrl(true);
    }
}
