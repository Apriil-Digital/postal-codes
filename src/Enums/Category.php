<?php

namespace Apriil\PostalCodes\Enums;

enum Category: string
{
    case Address = 'G';
    case Mailbox = 'P';
    case Both = 'B';
    case ServicePoint = 'S';
}