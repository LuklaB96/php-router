<?php

namespace App\Lib\Logger\Enums;

enum LogLevel: string
{
    case INFO = 'info';
    case WARNING = 'warning';
    case ERROR = 'error';
    case DEBUG = 'debug';
}