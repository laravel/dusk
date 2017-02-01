<?php

namespace Laravel\Dusk\Helpers;

class ConsoleLogFormatter
{
    /**
     * Get formatted logs
     *
     * @param array $logs
     *
     * @return string
     */
    public function get(array $logs)
    {
        $output = [];
        foreach ($logs as $index => $record) {
            $output[] = $this->formatRecord($record, $index);
        }

        return implode("\n", $output);
    }

    /**
     * Get formatted single log record
     *
     * @param array $log
     * @param int $index
     *
     * @return string
     */
    protected function formatRecord(array $log, $index)
    {
        return sprintf("%s. %s (%s) on %s\n%s\n-------",
            $index + 1,
            ! empty($log['level']) ? ucfirst(strtolower($log['level'])) : '',
            ! empty($log['source']) ? $log['source'] : '',
            ! empty($log['timestamp']) ? date('Y-m-d H:i:s',
                (int)round($log['timestamp'] / 1000.0)) : '',
            ! empty($log['message']) ? trim(str_replace('\n', "\n", $log['message'])) : ''
        );
    }
}