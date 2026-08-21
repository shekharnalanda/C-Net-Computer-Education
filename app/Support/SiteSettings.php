<?php

namespace App\Support;

class SiteSettings
{
    public static function defaults(): array
    {
        return [
            'phone' => '+91 70047 73247',
            'whatsapp' => '917004773247',
            'email' => 'cnetbiharsharif@gmail.com',
            'address_line' => 'Opp. Kalawati Palace, Quamruddin Ganj',
            'city' => 'Bihar Sharif',
            'district' => 'Nalanda',
            'state' => 'Bihar',
            'pin' => '803101',
            'job_location' => 'Bihar Sharif',
            'job_role' => 'Computer Operator',
            'admission_notice' => 'Admissions Open · प्रवेश प्रारंभ',
            'hero_title' => 'Learn computer skills.',
            'hero_highlight' => 'Build your future.',
            'hero_text_en' => 'Industry-relevant computer education, practical lab training and career guidance for students and job seekers.',
            'hero_text_hi' => 'व्यावहारिक कंप्यूटर प्रशिक्षण के साथ सीखें, आगे बढ़ें और रोजगार के लिए तैयार बनें।',
            'highlight_two_value' => '100%',
            'highlight_two_label' => 'Practical Learning',
            'highlight_three_value' => 'Job',
            'highlight_three_label' => 'Guidance Support',
            'why_title' => 'More than a certificate—',
            'why_highlight' => 'a confident skillset.',
            'why_lead' => 'Clear teaching, repeated practice and useful projects.',
        ];
    }

    public static function all(): array
    {
        $path = storage_path('app/cnet-settings.json');
        $stored = is_readable($path) ? json_decode((string) file_get_contents($path), true) : [];

        return array_merge(self::defaults(), is_array($stored) ? $stored : []);
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        return self::all()[$key] ?? $fallback;
    }

    public static function update(array $data): void
    {
        $path = storage_path('app/cnet-settings.json');
        file_put_contents(
            $path,
            json_encode(array_merge(self::all(), $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }
}
