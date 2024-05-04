<?php

namespace Stoyantodorov\ApiClient\Enum;

enum RequestMethod: string
{
    case GET = 'get';

    case HEAD = 'head';

    case POST = 'post';

    case PATCH = 'patch';

    case PUT = 'put';

    case DELETE = 'delete';

    case SEND = 'send';
}
