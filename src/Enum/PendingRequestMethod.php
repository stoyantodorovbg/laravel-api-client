<?php

namespace Stoyantodorov\ApiClient\Enum;

enum PendingRequestMethod: string
{
    case BASE_URL = 'baseUrl';

    case WITH_BODY = 'withBody';

    case AS_JSON = 'asJson';

    case AS_FORM = 'asForm';

    case ATTACH = 'attach';

    case AS_MULTIPART = 'asMultipart';

    case BODY_FORMAT = 'bodyFormat';

    case WITH_QUERY_PARAMETERS = 'withQueryParameters';

    case CONTENT_TYPE = 'contentType';

    case ACCEPT_JSON = 'acceptJson';

    case ACCEPT = 'accept';

    case WITH_HEADERS = 'withHeaders';

    case WITH_HEADER = 'withHeader';

    case REPLACE_HEADERS = 'replaceHeaders';

    case WITH_BASIC_AUTH = 'withBasicAuth';

    case WITH_DIGEST_AUTH = 'withDigestAuth';

    case WITH_TOKEN = 'withToken';

    case WITH_USER_AGENT = 'withUserAgent';

    case WITH_URL_PARAMETERS = 'withUrlParameters';

    case WITH_COOKIES = 'withCookies';

    case MAX_REDIRECTS = 'maxRedirects';

    case WITHOUT_REDIRECTING = 'withoutRedirecting';

    case WITHOUT_VERIFYING = 'withoutVerifying';

    case SINK = 'sink';

    case TIMOUT = 'timeout';

    case CONNECT_TIMOUT = 'connectTimeout';

    case RETRY = 'retry';

    case WITH_OPTIONS = 'withOptions';

    case WITH_MIDDLEWARE = 'withMiddleware';

    case WITH_REQUEST_MIDDLEWARE = 'withRequestMiddleware';

    case WITH_RESPONSE_MIDDLEWARE = 'withResponseMiddleware';

    case BEFORE_SENDING = 'beforeSending';

    case THROW = 'throw';

    case THROW_IF = 'throwIf';

    case DUMP = 'dump';

    case DD = 'dd';
}
