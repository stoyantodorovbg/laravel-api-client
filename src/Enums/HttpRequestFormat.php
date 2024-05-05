<?php

namespace Stoyantodorov\ApiClient\Enums;

enum HttpRequestFormat: string
{
    case QUERY = 'query';
    case BODY = 'body';
    case JSON = 'json';
    case FORM = 'form_params';
    case MULTIPART = 'multipart';
}
