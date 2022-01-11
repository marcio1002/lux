<?php

namespace Lux\Providers;

use
    Lux\Providers\Provider,
    React\Stream\WritableStreamInterface,
    React\Stream\WritableResourceStream;

    
class CommandProvider extends Provider
{

    public function boot()
    {
        $this->bind(WritableStreamInterface::class, function ($app) {
            return new WritableResourceStream(fopen('/home/marcio-zorion/Downloads/Eminem Ft Nf - Courage -4K.mp4', 'r+'));
        });
    }
}
