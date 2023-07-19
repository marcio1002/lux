<?php

namespace Lux\Traits;

trait MessageTrait
{
    protected function info(string $message, ?array $options = []): string
    {
        $options = $this->setMessageOptions($options);
        return "<fg=#F5F5F5;bg=#FF861D;$options> $message </>";
    }


    protected function success(string $message, ?array $options = [])
    {
        $options = $this->setMessageOptions($options);
        return "<fg=#080808;bg=#10F34A;$options> $message </>";
    }

    protected function error(string $message, ?array $options = []): string
    {
        $options = $this->setMessageOptions($options);
        return "<fg=#F5F5F5;bg=#FF3534;$options> $message </>";
    }

    private function setMessageOptions(array $options): string
    {
        return "options=" . (count($options) > 0 ? join(',', $options) : '');
    }
}