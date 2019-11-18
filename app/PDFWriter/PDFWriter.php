<?php

namespace App\PDFWriter;

use setasign\Fpdi\Fpdi;

class PDFWriter {
    private $pdf;
    private $pages = 0;
    private $outputTexts = [], $outputBoxes = [], $outputSignature = [];

    public function setFile($file) {
        if (file_exists($file)) {
            $this->pdf = new Fpdi();
            $this->pages = $this->pdf->setSourceFile($file);
            $this->intSetFont();
            return true;
        } else {
            return false;
        }
    }

    public function writeAt($pageNumber, $x, $y, $text, $fontSize) {
        $this->outputTexts["page" . $pageNumber][] = ["x" => $x, "y" => $y, "text" => $text, "size" => $fontSize];
    }

    public function box($pageNumber, $x, $y, $w, $h, $rgb = [0,0,0], $borderWidth = 0, $borderRGB = [0,0,0]) {
        $this->outputBoxes["page" . $pageNumber][] = ["x" => $x, "y" => $y, "w" => $w, "h" => $h, "rgb" => $rgb, 
                                                        "borderWidth" => $borderWidth, "borderRGB" => $borderRGB];
    }
    
    public function getPageWidth() {
        if (isset($this->pdf)) {
            return $this->pdf->GetPageWidth();
        } else {
            return 0;
        }
    }

    public function stampElectronicSignature($resellerId, $time, $page = 1, $x = 0, $y = 0, $title = "DOCUMENT ELECTRONICALLY SIGNED") {
        $this->outputSignature = ["id" => $resellerId, "time" => $time, "page" => $page, "x" => $x, "y" => $y, "title" => $title];
    }

    public function output($fileName = "", $includeHashInFileName = "") {
        if (!isset($this->pdf)) return false;
        if ($this->pages <= 0) return false;
        
        $signature = "";
        $hash = "";
        $width = $this->getPageWidth();
        for ($pageNo = 0; $pageNo < $this->pages; $pageNo++) {
            $this->pdf->AddPage();
            $pageId = $this->pdf->importPage($pageNo + 1);
            $this->pdf->useImportedPage($pageId, 0, 0, $width);

            // Output boxes (behind texts)
            if (array_key_exists("page" . ($pageNo + 1), $this->outputBoxes)) {
                $pageBoxes = $this->outputBoxes["page" . ($pageNo + 1)];
                for ($index = 0; $index < count($pageBoxes); $index++) {
                    $this->intBox($pageBoxes[$index]["x"], $pageBoxes[$index]["y"], 
                                    $pageBoxes[$index]["w"], $pageBoxes[$index]["h"], $pageBoxes[$index]["rgb"], 
                                    $pageBoxes[$index]["borderWidth"], $pageBoxes[$index]["borderRGB"]);
                }
            }

            // Output texts
            if (array_key_exists("page" . ($pageNo + 1), $this->outputTexts)) {
                $pageOutput = $this->outputTexts["page" . ($pageNo + 1)];
                for ($index = 0; $index < count($pageOutput); $index++) {
                    $this->intWriteText($pageOutput[$index]["x"], $pageOutput[$index]["y"], 
                                        $pageOutput[$index]["size"], $pageOutput[$index]["text"]);
                }
            }

            if (count($this->outputSignature) > 0) {
                if ($pageNo + 1 == $this->outputSignature["page"]) {
                    // Parameters
                    $x = $this->outputSignature["x"];
                    $y = $this->outputSignature["y"];
                    $ip = $_SERVER["REMOTE_ADDR"];
                    $userAgent = $_SERVER["HTTP_USER_AGENT"];
                    $time = $this->outputSignature["time"];
                    $id = $this->outputSignature["id"];
                    $title = $this->outputSignature["title"];
                    $colorBlue = [0, 50, 150];
                    
                    // Text preparation
                    if (strlen($title) > 0) $signature = "********************** $title **********************\n";
                    if (strlen($fileName) > 0) {
                        $hash = $this->getHash($id, $pageNo + 1, $ip, $time);
                        $signature .= "Signature Code: " . $hash . "\n";
                        $signature .= "Server Time:    " . date('r', $time) . "\n";
                    }
                    $signature .=     "IP Address:     " . $ip . "\n";
                    $signature .=     "User Agent:     " . (str_replace(")", ")" . PHP_EOL . "               ", $userAgent)) . "\n";
                    if (strlen($title) > 0) $signature .= str_pad("", 46 + strlen($this->outputSignature["title"]), "*");

                    // Write
                    $this->intSetFont('Courier', $colorBlue, "I");
                    $this->intWriteText($x + 2, $y, 10, $signature, 5);
                    $this->intSetFont();
                }
            }
        }

        if (strlen($fileName) > 0) {
            if (strlen($includeHashInFileName) > 0) {
                $fileName = str_replace($includeHashInFileName, str_replace(":", "", $hash), $fileName);
            }
            $this->pdf->Output("F", $fileName);
            
            return file_exists($fileName);
        } else {
            $this->pdf->Output("");
        }
    }

    // Output: [ID]:[PAGE]:[MD5] (4 by 4)
    private function getHash($id, $page, $ip, $time) {
        $sig = sprintf("%08d", $id) . sprintf("%04d", $page) . md5($id . $ip . $page . $time);
        $chunk = substr(chunk_split($sig, 4), 0, -strlen("\r\n"));
        return implode(":", explode("\r\n", $chunk));
    }

    private function intSetFont($fontName = 'Helvetica', $textColor = [0, 0, 0], $textStyle = "") {
        $this->pdf->SetFont($fontName, $textStyle);
        $this->pdf->SetTextColor($textColor[0], $textColor[1], $textColor[2]);
    }

    private function intBox($x, $y, $w, $h, $rgb, $borderWidth = 0, $borderRGB = [0, 0, 0]) {
        $boxType = ($borderWidth > 0 ? "DF" : "F");
        // Border
        $this->pdf->setLineWidth($borderWidth);
        $this->pdf->setDrawColor($borderRGB[0], $borderRGB[1], $borderRGB[2]);

        // Fill
        $this->pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        $this->pdf->Rect($x, $y, $w, $h, $boxType);
    }

    private function intWriteText($x, $y, $size, $text, $lineHeight = 0) {
        $this->pdf->SetXY($x, $y);
        $this->pdf->SetFontSize($size);
        $this->pdf->Write($lineHeight, $text);
    }
}

?>