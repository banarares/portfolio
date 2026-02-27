<?php

namespace App\Services;

use App\Models\Setting;

final class PortfolioContext
{
    public int $userId;
    public array $settings;

    public function __construct()
    {
        $this->userId = (int) ($_ENV['PORTFOLIO_USER_ID'] ?? 1);
        
        $this->settings = (new Setting())->firstByUser($this->userId) ?? [];
    }
}