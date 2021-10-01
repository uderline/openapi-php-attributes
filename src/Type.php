<?php

namespace OpenApiGenerator;

class Type
{
    // COMMON
    public const REF = 'ref';
    public const INT = 'integer';
    public const STRING = 'string';
    public const JSON = 'json';
    public const OBJECT = 'object';
    public const ARRAY = 'array';

    // MEDIA
    // Media Types
    public const MEDIA_IMAGE_PNG = 'image/png';
    // Encoding
    public const ENCODING_BASE64 = 'base64';

    // PARAM
    public const PATH = 'path';

    // PROPERTY
    public const BOOLEAN = 'boolean';
    public const MEDIA = 'media';

    // REQUEST BODY
    public const FORM_DATA = 'formdata';

    // RESPONSE
    public const IMAGE = 'image';
}