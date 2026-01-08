<?php

/**
 * Format Helper Functions
 * 
 * Helper untuk format data (currency, date, etc.)
 */

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah
     *
     * @param mixed $number Number to format
     * @param bool $withPrefix Include "Rp" prefix
     * @return string Formatted currency string
     */
    function format_rupiah($number, bool $withPrefix = true): string
    {
        $number = (float) ($number ?? 0);
        $formatted = number_format($number, 0, ',', '.');
        
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_ribuan')) {
    /**
     * Format number with thousand separator (Indonesian style)
     *
     * @param mixed $number Number to format
     * @return string Formatted number
     */
    function format_ribuan($number): string
    {
        return number_format((float)($number ?? 0), 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format date to Indonesian format
     *
     * @param string|null $date Date string
     * @param string $format Output format
     * @return string Formatted date
     */
    function format_tanggal(?string $date, string $format = 'd M Y'): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return $date;
            }

            // Indonesian month names
            $months = [
                'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar',
                'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep',
                'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
            ];

            $formatted = date($format, $timestamp);
            
            // Replace English month with Indonesian
            foreach ($months as $en => $id) {
                $formatted = str_replace($en, $id, $formatted);
            }

            return $formatted;
        } catch (\Exception $e) {
            return $date;
        }
    }
}

if (!function_exists('format_tanggal_lengkap')) {
    /**
     * Format date to full Indonesian format
     *
     * @param string|null $date Date string
     * @return string Formatted date (e.g., "Senin, 1 Januari 2024")
     */
    function format_tanggal_lengkap(?string $date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return $date;
            }

            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $months = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            $dayName = $days[date('w', $timestamp)];
            $day = date('j', $timestamp);
            $month = $months[(int)date('n', $timestamp)];
            $year = date('Y', $timestamp);

            return "{$dayName}, {$day} {$month} {$year}";
        } catch (\Exception $e) {
            return $date;
        }
    }
}

if (!function_exists('format_waktu')) {
    /**
     * Format time
     *
     * @param string|null $time Time string
     * @return string Formatted time (e.g., "10:00 WIB")
     */
    function format_waktu(?string $time, bool $withTimezone = true): string
    {
        if (empty($time)) {
            return '-';
        }

        try {
            $timestamp = strtotime($time);
            if ($timestamp === false) {
                return $time;
            }

            $formatted = date('H:i', $timestamp);
            return $withTimezone ? $formatted . ' WIB' : $formatted;
        } catch (\Exception $e) {
            return $time;
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime
     *
     * @param string|null $datetime Datetime string
     * @return string Formatted datetime
     */
    function format_datetime(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }

        return format_tanggal($datetime, 'd M Y') . ' ' . format_waktu($datetime, false);
    }
}

if (!function_exists('time_ago')) {
    /**
     * Get relative time (e.g., "5 menit yang lalu")
     *
     * @param string|null $datetime Datetime string
     * @return string Relative time
     */
    function time_ago(?string $datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }

        try {
            $timestamp = strtotime($datetime);
            if ($timestamp === false) {
                return $datetime;
            }

            $diff = time() - $timestamp;

            if ($diff < 60) {
                return 'Baru saja';
            } elseif ($diff < 3600) {
                $mins = floor($diff / 60);
                return $mins . ' menit yang lalu';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                return $hours . ' jam yang lalu';
            } elseif ($diff < 604800) {
                $days = floor($diff / 86400);
                return $days . ' hari yang lalu';
            } elseif ($diff < 2592000) {
                $weeks = floor($diff / 604800);
                return $weeks . ' minggu yang lalu';
            } else {
                return format_tanggal($datetime);
            }
        } catch (\Exception $e) {
            return $datetime;
        }
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number for display
     *
     * @param string|null $phone Phone number
     * @return string Formatted phone
     */
    function format_phone(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }

        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format Indonesian phone
        if (strlen($phone) >= 10) {
            if (substr($phone, 0, 2) === '62') {
                $phone = '0' . substr($phone, 2);
            }
            
            // Format: 0812-3456-7890
            if (strlen($phone) >= 11) {
                return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
            }
        }

        return $phone;
    }
}

if (!function_exists('format_wa_link')) {
    /**
     * Create WhatsApp link from phone number
     *
     * @param string|null $phone Phone number
     * @param string $message Optional message
     * @return string WhatsApp URL
     */
    function format_wa_link(?string $phone, string $message = ''): string
    {
        if (empty($phone)) {
            return '#';
        }

        // Remove non-numeric
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to international format
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $url = 'https://wa.me/' . $phone;
        
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }

        return $url;
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size to human readable
     *
     * @param int $bytes File size in bytes
     * @return string Formatted size
     */
    function format_file_size(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}