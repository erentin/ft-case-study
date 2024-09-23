<?php

use App\Jobs\FetchCryptoNews;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new FetchCryptoNews)->everyMinute();
