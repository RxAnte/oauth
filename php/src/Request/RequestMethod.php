<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

enum RequestMethod
{
    case GET;
    case HEAD;
    case POST;
    case PUT;
    case DELETE;
    case CONNECT;
    case OPTIONS;
    case TRACE;
    case PATCH;
}
