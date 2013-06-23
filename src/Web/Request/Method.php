<?php

namespace Web\Request;

class Method 
{
    /**
     * HTTP method constants.
     */
    const DELETE = 'DELETE';

    const GET = 'GET';

    const HEAD = 'HEAD';

    const OPTIONS = 'OPTIONS';

    const POST = 'POST';

    const PUT = 'PUT';

    const TRACE = 'TRACE';

    /**
     * WebDAV method constants.
     */
    const COPY = 'COPY';

    const LOCK = 'LOCK';

    const MKCOL = 'MKCOL';

    const MOVE = 'MOVE';

    const PROPFIND = 'PROPFIND';

    const PROPPATCH = 'PROPPATCH';

    const UNLOCK = 'UNLOCK';
}
