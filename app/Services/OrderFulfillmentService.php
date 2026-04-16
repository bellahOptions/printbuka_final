<?php

namespace App\Services;

use App\Models\Order;
use App\Support\SiteSettings;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class OrderFulfillmentService
{
    private const DEFAULT_BUSINESS_TIMEZONE = 'Africa/Lagos';

    private const DEFAULT_STANDARD_LEAD_DAYS = 4;

    private const DEFAULT_EXPRESS_LEAD_HOURS = 48;

    private const DEFAULT_DAILY_CAPACITY = 6;

    private const DEFAULT_EXPRESS_SURCHARGE = 5000;

    private const DEFAULT_SAMPLE_SURCHARGE = 5000;

    private const WORK_DAY_START_HOUR = 8;

    private const WORK_DAY_END_HOUR = 18;

    /**
     * @return array{express_fee:float,sample_fee:float,total_adjustment:float}
     */
    public function pricingAdjustments(bool $isExpress, bool $isSample): array
    {
        $sampleFee = $isSample ? $this->sampleSurcharge() : 0.0;
        $expressFee = ($isExpress && ! $isSample) ? $this->expressSurcharge() : 0.0;

        return [
            'express_fee' => $expressFee,
            'sample_fee' => $sampleFee,
            'total_adjustment' => $expressFee + $sampleFee,
        ];
    }

    public function expressSurcharge(): float
    {
        return max(0, (float) SiteSettings::get('express_order_surcharge', self::DEFAULT_EXPRESS_SURCHARGE));
    }

    public function sampleSurcharge(): float
    {
        return max(0, (float) SiteSettings::get('sample_order_surcharge', self::DEFAULT_SAMPLE_SURCHARGE));
    }

    public function estimateForNewOrder(
        bool $isExpress,
        CarbonInterface|string|null $orderedAt = null,
        CarbonInterface|string|null $paymentAt = null
    ): Carbon {
        if ($isExpress) {
            return $this->estimateExpressDelivery($paymentAt ?? $orderedAt);
        }

        return $this->estimateStandardDelivery($orderedAt);
    }

    public function estimateExpressDelivery(CarbonInterface|string|null $paymentAt = null): Carbon
    {
        $anchor = $this->asBusinessTime($paymentAt ?? now());

        return $anchor
            ->addHours($this->expressLeadHours())
            ->setTimezone($this->appTimezone());
    }

    public function estimateStandardDelivery(CarbonInterface|string|null $orderedAt = null): Carbon
    {
        $orderTime = $this->alignToWorkWindow($this->asBusinessTime($orderedAt ?? now()));
        $candidate = $this->addWorkingDays($orderTime, $this->standardLeadDays())
            ->setTime(self::WORK_DAY_END_HOUR, 0, 0);

        while (! $this->hasDailyCapacityFor($candidate)) {
            $candidate = $this->nextWorkingDayAtClose($candidate);
        }

        return $candidate->setTimezone($this->appTimezone());
    }

    private function hasDailyCapacityFor(CarbonInterface $candidate): bool
    {
        $activeOrderCount = Order::query()
            ->where('service_type', '!=', 'quote')
            ->where('is_express', false)
            ->whereDate('estimated_delivery_at', $candidate->toDateString())
            ->whereNull('actual_delivery_at')
            ->where(function ($query): void {
                $query
                    ->whereNull('status')
                    ->orWhereNotIn('status', ['Cancelled', 'Delivered', 'Client Review — Satisfactory']);
            })
            ->count();

        return $activeOrderCount < $this->dailyCapacity();
    }

    private function alignToWorkWindow(Carbon $at): Carbon
    {
        $cursor = $at->copy();

        if (! $this->isWorkingDay($cursor)) {
            return $this->nextWorkingDay($cursor)->setTime(self::WORK_DAY_START_HOUR, 0, 0);
        }

        if ($cursor->hour < self::WORK_DAY_START_HOUR) {
            return $cursor->setTime(self::WORK_DAY_START_HOUR, 0, 0);
        }

        if ($cursor->hour >= self::WORK_DAY_END_HOUR) {
            return $this->nextWorkingDay($cursor)->setTime(self::WORK_DAY_START_HOUR, 0, 0);
        }

        return $cursor;
    }

    private function addWorkingDays(Carbon $start, int $days): Carbon
    {
        $cursor = $start->copy();

        for ($i = 0; $i < $days; $i++) {
            $cursor = $this->nextWorkingDay($cursor);
        }

        return $cursor;
    }

    private function nextWorkingDayAtClose(CarbonInterface $from): Carbon
    {
        return $this->nextWorkingDay(Carbon::parse($from))
            ->setTime(self::WORK_DAY_END_HOUR, 0, 0);
    }

    private function nextWorkingDay(CarbonInterface $from): Carbon
    {
        $cursor = Carbon::parse($from)->addDay()->startOfDay();

        while (! $this->isWorkingDay($cursor)) {
            $cursor->addDay();
        }

        return $cursor;
    }

    private function isWorkingDay(CarbonInterface $day): bool
    {
        return $day->dayOfWeek !== CarbonInterface::SUNDAY;
    }

    private function asBusinessTime(CarbonInterface|string $value): Carbon
    {
        return Carbon::parse($value, $this->businessTimezone())->setTimezone($this->businessTimezone());
    }

    private function standardLeadDays(): int
    {
        return max(1, (int) SiteSettings::get('standard_order_lead_days', self::DEFAULT_STANDARD_LEAD_DAYS));
    }

    private function expressLeadHours(): int
    {
        return max(1, (int) SiteSettings::get('express_order_lead_hours', self::DEFAULT_EXPRESS_LEAD_HOURS));
    }

    private function dailyCapacity(): int
    {
        return max(1, (int) SiteSettings::get('daily_job_capacity_limit', self::DEFAULT_DAILY_CAPACITY));
    }

    private function businessTimezone(): string
    {
        return (string) SiteSettings::get('order_business_timezone', self::DEFAULT_BUSINESS_TIMEZONE);
    }

    private function appTimezone(): string
    {
        return (string) config('app.timezone', 'UTC');
    }
}
