<?php

namespace Wolff\Core\Http;

class Response implements ResponseInterface
{

    const COOKIE_TIMES = [
        'FOREVER' => 157680000, // Five years
        'MONTH'   => 2629743,
        'DAY'     => 86400,
        'HOUR'    => 3600
    ];

    /**
     * The response content
     *
     * @var string
     */
    private $content;

    /**
     * The HTTP status code.
     *
     * @var int|null
     */
    private $status_code;

    /**
     * The header location.
     *
     * @var string
     */
    private $url;

    /**
     * The header tag list.
     *
     * @var array
     */
    private $headers;

    /**
     * The cookies list.
     *
     * @var array
     */
    private $cookies;


    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->content = '';
        $this->status_code = null;
        $this->headers = [];
        $this->url = '';
        $this->cookies = [];
    }


    /**
     * {@inheritdoc}
     */
    public function write($content)
    {
        $this->content = strval($content);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function append($content)
    {
        $this->content .= strval($content);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->content;
    }


    /**
     * {@inheritdoc}
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[trim($key)] = $value;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function setCode(int $status)
    {
        $this->status_code = $status;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function setCookie(
        string $key,
        string $value,
        $time = null,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $http_only = true
    ) {
        if (isset($time) && array_key_exists($time, self::COOKIE_TIMES)) {
            $time = self::COOKIE_TIMES[strtoupper($time)];
        }

        array_push($this->cookies, [
            'key'       => $key,
            'value'     => $value,
            'time'      => isset($time) ? time() + $time : 0,
            'path'      => $path,
            'domain'    => $domain,
            'secure'    => $secure,
            'http_only' => $http_only
        ]);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function unsetCookie(string $key)
    {
        array_push($this->cookies, [
            'key'       => $key,
            'value'     => '',
            'time'      => time() - self::COOKIE_TIMES['HOUR'],
            'path'      => '',
            'domain'    => '',
            'secure'    => false,
            'http_only' => false
        ]);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function send(bool $return = false)
    {
        if (isset($this->status_code)) {
            http_response_code($this->status_code);
        }

        foreach ($this->headers as $key => $val) {
            header("$key: $val");
        }

        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie['key'],
                $cookie['value'],
                $cookie['time'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['http_only']
            );
        }

        if ($return) {
            return $this->content;
        }

        print($this->content);
    }
}
