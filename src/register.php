<?php

\Timpack\TvshowTracker\Core\Command\CommandRegistry::getInstance()
    ->pushCommand([
        '\\Timpack\\TvshowTracker\\Tv\\Command\\SyncCommand'
    ]);