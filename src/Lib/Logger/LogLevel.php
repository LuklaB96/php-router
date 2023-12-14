<?php

namespace App\Lib\Logger;

enum LogLevel
{
    case INFO;
    case WARNING;
    case ERROR;
    case DEBUG;
}