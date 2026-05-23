<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeService
{
    public function generateBarcode(string $value): string
    {
        $generator = new BarcodeGeneratorPNG();
        return base64_encode($generator->getBarcode($value, $generator::TYPE_CODE_128, 2, 60));
    }

    /**
     * Generate a QR code SVG string.
     */
    public function generateQrCode(string $value, int $size = 150): string
    {
        return QrCode::size($size)->generate($value);
    }

    /**
     * Build the barcode value from a product SKU.
     * Strips non-alphanumeric characters for scanner compatibility.
     */
    public function buildBarcodeValue(string $sku): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($sku));
    }

    /**
     * Generate a print-ready HTML label for a product.
     * Returns raw HTML fragment suitable for embedding in a print view.
     */
    public function generateLabel(Product $product): string
    {
        $barcode = $this->generateBarcode($product->barcode);

        return <<<HTML
        <div class="label-wrapper" style="width:200px;text-align:center;font-family:Arial;padding:8px;border:1px solid #ccc;">
            <div style="font-weight:bold;font-size:11px;">{$product->name}</div>
            <div style="font-size:10px;color:#666;">{$product->sku}</div>
            <img src="data:image/png;base64,{$barcode}" alt="barcode" style="width:180px;height:60px;">
            <div style="font-size:9px;">{$product->barcode}</div>
        </div>
        HTML;
    }

    private function formatPrice(float $price): string
    {
        return number_format($price, 0, ',', '.');
    }
}
