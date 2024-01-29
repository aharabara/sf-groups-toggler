<?php
declare(strict_types=1);

use PhpTui\Term\Terminal;
use PhpTui\Tui\Bridge\PhpTerm\PhpTermBackend;
use PhpTui\Tui\DisplayBuilder;

require 'vendor/autoload.php';

$terminal = Terminal::new();
$display = DisplayBuilder::default(PhpTermBackend::new($terminal))->build();

while (true) {
    $event = $terminal->events()->next();
    if ($event instanceof \PhpTui\Term\Event\CharKeyEvent) {
        if ($event->char === 'h') {
            print 'Hello';
        }
    }
    if ($event instanceof \PhpTui\Term\Event\CodedKeyEvent) {
        if ($event->code === \PhpTui\Term\KeyCode::Esc) {
            die('Bye');
        }
    }
}
