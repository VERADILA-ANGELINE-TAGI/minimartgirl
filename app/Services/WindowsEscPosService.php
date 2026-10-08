<?php

namespace App\Services;

use InvalidArgumentException;

class WindowsEscPosService
{
    /**
     * @return array{
     *     paperWidthMm: int,
     *     maxPageHeightMm: int,
     *     printPadding: string,
     *     fontSizePx: int,
     *     infoFontSizePx: int,
     *     storeNameFontSizePx: int,
     *     grandTotalFontSizePx: int,
     *     lineHeight: float,
     *     itemSpacingMm: float,
     *     dividerSpacingMm: float
     * }
     */
    public function layoutFor(string $paperWidth): array
    {
        return match ($paperWidth) {
            '58' => [
                'paperWidthMm' => 58,
                'maxPageHeightMm' => 110,
                'printPadding' => '3mm 3mm 4mm',
                'fontSizePx' => 9,
                'infoFontSizePx' => 9,
                'storeNameFontSizePx' => 16,
                'grandTotalFontSizePx' => 14,
                'lineHeight' => 1.3,
                'itemSpacingMm' => 1,
                'dividerSpacingMm' => 1.5,
            ],
            '80' => [
                'paperWidthMm' => 80,
                'maxPageHeightMm' => 150,
                'printPadding' => '3mm 4mm 4mm',
                'fontSizePx' => 10,
                'infoFontSizePx' => 10,
                'storeNameFontSizePx' => 18,
                'grandTotalFontSizePx' => 15,
                'lineHeight' => 1.4,
                'itemSpacingMm' => 1.5,
                'dividerSpacingMm' => 2,
            ],
            default => throw new InvalidArgumentException('Unsupported receipt paper width.'),
        };
    }
}
