<?php
/// app/CustomPPTX.php

namespace App;

use Cristal\Presentation\PPTX;

class CustomPPTX extends PPTX
{
    protected $slidesLoaded = false;

    public function loadSlides($filePath = null)
    {
        if (!$this->slidesLoaded) {
            parent::__construct($filePath ?: $this->getFilePath());
            $this->slidesLoaded = true;
        }
    }

    public function getSlides()
    {
        $this->loadSlides();
        return parent::getSlides();
    }
}
