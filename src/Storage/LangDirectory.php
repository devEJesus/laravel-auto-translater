<?php

namespace LaravelAutoTranslator\Storage;

class LangDirectory extends BaseDirectory
{
    public function __construct()
    {
        // Initialize with the language directory
        parent::__construct(base_path('lang'));
    }
}
