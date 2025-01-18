<?php

declare(strict_types=1);

namespace ModelContextProtocol\Types;

enum CreateMessageRequestParamsIncludeContext: string {
    case allServers = 'allServers';
    case none = 'none';
    case thisServer = 'thisServer';
}
