<?php

declare(strict_types=1);

namespace App;

final class NavItem
{
    public function __construct(public string $shortcut, public string $label)
    {
    }
}
