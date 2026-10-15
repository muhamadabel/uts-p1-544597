<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Tetap semangat mengejar deadline!');
})->purpose('Display an inspiring quote');
