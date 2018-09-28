<?php

namespace Core;

class                           HTTP
{
    static private              $CODE_HTTP = array
    (
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
    );
    
    private                     $_code;
    private                     $_header;
    
    public function             __construct(int $code = 200, array $header = array())
    {
        $this->code($code);
        $this->_header = $header;
    }
    
    public function             code($code = null)
    {
        if ($code === null)
        {
            return $this->_code;
        }
        $this->_code = $code;
    }
    
    public function             getProtocol()
    {
        return $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
    }
    
    public function             getMessageCode()
    {
        return (array_key_exists($this->_code, self::$CODE_HTTP)) ? self::$CODE_HTTP[$this->_code] : "Unknown http status code {$this->_code}";
    }
    
    public function             header()
    {
        $argc = func_num_args();
        if ($argc === 1)
        {
            $param = func_get_arg(0);
            if (is_array($param))
            {
                foreach ($param as $key => $value)
                {
                    $this->_header[$key] = $value;
                }
            }
        }
        else if (($argc % 2) === 0)
        {
            $argv = func_get_args();
            for ($i = 0; $i < $argc; $i += 2)
            {
                if (is_string($argv[$i]) && is_string($argv[$i + 1]))
                {
                    $this->_header[$argv[$i]] = $argv[$i + 1];
                }
            }
        }
    }
    
    public function             getHeader($keys = null)
    {
        if ($keys === null)
        {
            return $this->_header;
        }
        else if (array_key_exists($keys, $this->_header))
        {
            return $this->_header[$keys];
        }
        else if (is_array($keys))
        {
            $header = array();
            foreach ($keys as $key)
            {
                if (array_key_exists($key, $this->_header))
                {
                    $header[$key] = $this->_header[$key];
                }
            }
            return ($header);
        }
        return null;
    }


    public function             send()
    {
        header("{$this->getProtocol()} {$this->_code} {$this->getMessageCode()}");
        foreach ($this->_header as $key => $value)
        {
            header("{$key}:{$value}");
        }
    }
}