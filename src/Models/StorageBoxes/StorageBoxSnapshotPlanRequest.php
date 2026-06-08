<?php

namespace LKDev\HetznerCloud\Models\StorageBoxes;

class StorageBoxSnapshotPlanRequest
{
    /**
     * Maximum number of snapshots to keep. Older snapshots are deleted when the limit is reached.
     *
     * @var int
     */
    public int $max_snapshots;

    /**
     * Minute of the hour when the plan runs (UTC, 0–59).
     *
     * @var int
     */
    public int $minute;

    /**
     * Hour of the day when the plan runs (UTC, 0–23).
     *
     * @var int
     */
    public int $hour;

    /**
     * Day of the week (1 = Monday, 7 = Sunday). Null means every day.
     *
     * @var int|null
     */
    public ?int $day_of_week;

    /**
     * Day of the month (1–31). Null means every day.
     *
     * @var int|null
     */
    public ?int $day_of_month;

    /**
     * @param  int  $max_snapshots
     * @param  int  $minute
     * @param  int  $hour
     * @param  int|null  $day_of_week
     * @param  int|null  $day_of_month
     */
    public function __construct(
        int $max_snapshots,
        int $minute,
        int $hour,
        ?int $day_of_week = null,
        ?int $day_of_month = null
    ) {
        $this->max_snapshots = $max_snapshots;
        $this->minute = $minute;
        $this->hour = $hour;
        $this->day_of_week = $day_of_week;
        $this->day_of_month = $day_of_month;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'max_snapshots' => $this->max_snapshots,
            'minute' => $this->minute,
            'hour' => $this->hour,
            'day_of_week' => $this->day_of_week,
            'day_of_month' => $this->day_of_month,
        ];
    }
}
