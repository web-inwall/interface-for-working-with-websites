<?php
class SiteInspector
{
    public function getUrl($url)
    {
        $url = 'http://stackoverflow.com';
        $headers = @get_headers($url);
        if (!empty($headers[0])) {
            preg_match('/\d{3}/', $headers[0], $matches);
            echo $matches[0] . PHP_EOL;
        }
    }
}
