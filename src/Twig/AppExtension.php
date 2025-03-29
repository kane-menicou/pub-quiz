<?php

declare(strict_types=1);

namespace App\Twig;

use chillerlan\QRCode\QRCode;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('asQrCode', $this->getQrData(...)),
        ];
    }

    public function getQrData(string $data): string
    {
        return (new QRCode())->render($data);
    }
}
