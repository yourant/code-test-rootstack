<?php

use App\Repositories\TimezoneRepository;
use Illuminate\Database\Seeder;

class TimezoneTableSeeder extends Seeder {

    public function __construct(TimezoneRepository $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->timezone->create(["name" => "Pacific/Midway", "description" => "(UTC-11:00) Midway Island"]);
        $this->timezone->create(["name" => "Pacific/Samoa", "description" => "(UTC-11:00) Samoa"]);
        $this->timezone->create(["name" => "Pacific/Honolulu", "description" => "(UTC-10:00) Hawaii"]);
        $this->timezone->create(["name" => "US/Alaska", "description" => "(UTC-09:00) Alaska"]);
        $this->timezone->create(["name" => "America/Los_Angeles", "description" => "(UTC-08:00) Pacific Time (US &amp; Canada)"]);
        $this->timezone->create(["name" => "America/Tijuana", "description" => "(UTC-08:00) Tijuana"]);
        $this->timezone->create(["name" => "US/Arizona", "description" => "(UTC-07:00) Arizona"]);
        $this->timezone->create(["name" => "America/Chihuahua", "description" => "(UTC-07:00) Chihuahua"]);
        $this->timezone->create(["name" => "America/Chihuahua", "description" => "(UTC-07:00) La Paz"]);
        $this->timezone->create(["name" => "America/Mazatlan", "description" => "(UTC-07:00) Mazatlan"]);
        $this->timezone->create(["name" => "US/Mountain", "description" => "(UTC-07:00) Mountain Time (US &amp; Canada)"]);
        $this->timezone->create(["name" => "America/Managua", "description" => "(UTC-06:00) Central America"]);
        $this->timezone->create(["name" => "US/Central", "description" => "(UTC-06:00) Central Time (US &amp; Canada)"]);
        $this->timezone->create(["name" => "America/Mexico_City", "description" => "(UTC-06:00) Guadalajara"]);
        $this->timezone->create(["name" => "America/Mexico_City", "description" => "(UTC-06:00) Mexico City"]);
        $this->timezone->create(["name" => "America/Monterrey", "description" => "(UTC-06:00) Monterrey"]);
        $this->timezone->create(["name" => "Canada/Saskatchewan", "description" => "(UTC-06:00) Saskatchewan"]);
        $this->timezone->create(["name" => "America/Bogota", "description" => "(UTC-05:00) Bogota"]);
        $this->timezone->create(["name" => "US/Eastern", "description" => "(UTC-05:00) Eastern Time (US &amp; Canada)"]);
        $this->timezone->create(["name" => "US/East-Indiana", "description" => "(UTC-05:00) Indiana (East)"]);
        $this->timezone->create(["name" => "America/Lima", "description" => "(UTC-05:00) Lima"]);
        $this->timezone->create(["name" => "America/Bogota", "description" => "(UTC-05:00) Quito"]);
        $this->timezone->create(["name" => "Canada/Atlantic", "description" => "(UTC-04:00) Atlantic Time (Canada)"]);
        $this->timezone->create(["name" => "America/Caracas", "description" => "(UTC-04:30) Caracas"]);
        $this->timezone->create(["name" => "America/La_Paz", "description" => "(UTC-04:00) La Paz"]);
        $this->timezone->create(["name" => "America/Santiago", "description" => "(UTC-04:00) Santiago"]);
        $this->timezone->create(["name" => "Canada/Newfoundland", "description" => "(UTC-03:30) Newfoundland"]);
        $this->timezone->create(["name" => "America/Sao_Paulo", "description" => "(UTC-03:00) Brasilia"]);
        $this->timezone->create(["name" => "America/Argentina/Buenos_Aires", "description" => "(UTC-03:00) Buenos Aires"]);
        $this->timezone->create(["name" => "America/Argentina/Buenos_Aires", "description" => "(UTC-03:00) Georgetown"]);
        $this->timezone->create(["name" => "America/Godthab", "description" => "(UTC-03:00) Greenland"]);
        $this->timezone->create(["name" => "America/Noronha", "description" => "(UTC-02:00) Mid-Atlantic"]);
        $this->timezone->create(["name" => "Atlantic/Azores", "description" => "(UTC-01:00) Azores"]);
        $this->timezone->create(["name" => "Atlantic/Cape_Verde", "description" => "(UTC-01:00) Cape Verde Is."]);
        $this->timezone->create(["name" => "Africa/Casablanca", "description" => "(UTC+00:00) Casablanca"]);
        $this->timezone->create(["name" => "Europe/London", "description" => "(UTC+00:00) Edinburgh"]);
        $this->timezone->create(["name" => "Etc/Greenwich", "description" => "(UTC+00:00) Greenwich Mean Time : Dublin"]);
        $this->timezone->create(["name" => "Europe/Lisbon", "description" => "(UTC+00:00) Lisbon"]);
        $this->timezone->create(["name" => "Europe/London", "description" => "(UTC+00:00) London"]);
        $this->timezone->create(["name" => "Africa/Monrovia", "description" => "(UTC+00:00) Monrovia"]);
        $this->timezone->create(["name" => "UTC", "description" => "(UTC+00:00) UTC"]);
        $this->timezone->create(["name" => "Europe/Amsterdam", "description" => "(UTC+01:00) Amsterdam"]);
        $this->timezone->create(["name" => "Europe/Belgrade", "description" => "(UTC+01:00) Belgrade"]);
        $this->timezone->create(["name" => "Europe/Berlin", "description" => "(UTC+01:00) Berlin"]);
        $this->timezone->create(["name" => "Europe/Berlin", "description" => "(UTC+01:00) Bern"]);
        $this->timezone->create(["name" => "Europe/Bratislava", "description" => "(UTC+01:00) Bratislava"]);
        $this->timezone->create(["name" => "Europe/Brussels", "description" => "(UTC+01:00) Brussels"]);
        $this->timezone->create(["name" => "Europe/Budapest", "description" => "(UTC+01:00) Budapest"]);
        $this->timezone->create(["name" => "Europe/Copenhagen", "description" => "(UTC+01:00) Copenhagen"]);
        $this->timezone->create(["name" => "Europe/Ljubljana", "description" => "(UTC+01:00) Ljubljana"]);
        $this->timezone->create(["name" => "Europe/Madrid", "description" => "(UTC+01:00) Madrid"]);
        $this->timezone->create(["name" => "Europe/Paris", "description" => "(UTC+01:00) Paris"]);
        $this->timezone->create(["name" => "Europe/Prague", "description" => "(UTC+01:00) Prague"]);
        $this->timezone->create(["name" => "Europe/Rome", "description" => "(UTC+01:00) Rome"]);
        $this->timezone->create(["name" => "Europe/Sarajevo", "description" => "(UTC+01:00) Sarajevo"]);
        $this->timezone->create(["name" => "Europe/Skopje", "description" => "(UTC+01:00) Skopje"]);
        $this->timezone->create(["name" => "Europe/Stockholm", "description" => "(UTC+01:00) Stockholm"]);
        $this->timezone->create(["name" => "Europe/Vienna", "description" => "(UTC+01:00) Vienna"]);
        $this->timezone->create(["name" => "Europe/Warsaw", "description" => "(UTC+01:00) Warsaw"]);
        $this->timezone->create(["name" => "Africa/Lagos", "description" => "(UTC+01:00) West Central Africa"]);
        $this->timezone->create(["name" => "Europe/Zagreb", "description" => "(UTC+01:00) Zagreb"]);
        $this->timezone->create(["name" => "Europe/Athens", "description" => "(UTC+02:00) Athens"]);
        $this->timezone->create(["name" => "Europe/Bucharest", "description" => "(UTC+02:00) Bucharest"]);
        $this->timezone->create(["name" => "Africa/Cairo", "description" => "(UTC+02:00) Cairo"]);
        $this->timezone->create(["name" => "Africa/Harare", "description" => "(UTC+02:00) Harare"]);
        $this->timezone->create(["name" => "Europe/Helsinki", "description" => "(UTC+02:00) Helsinki"]);
        $this->timezone->create(["name" => "Europe/Istanbul", "description" => "(UTC+02:00) Istanbul"]);
        $this->timezone->create(["name" => "Asia/Jerusalem", "description" => "(UTC+02:00) Jerusalem"]);
        $this->timezone->create(["name" => "Europe/Helsinki", "description" => "(UTC+02:00) Kyiv"]);
        $this->timezone->create(["name" => "Africa/Johannesburg", "description" => "(UTC+02:00) Pretoria"]);
        $this->timezone->create(["name" => "Europe/Riga", "description" => "(UTC+02:00) Riga"]);
        $this->timezone->create(["name" => "Europe/Sofia", "description" => "(UTC+02:00) Sofia"]);
        $this->timezone->create(["name" => "Europe/Tallinn", "description" => "(UTC+02:00) Tallinn"]);
        $this->timezone->create(["name" => "Europe/Vilnius", "description" => "(UTC+02:00) Vilnius"]);
        $this->timezone->create(["name" => "Asia/Baghdad", "description" => "(UTC+03:00) Baghdad"]);
        $this->timezone->create(["name" => "Asia/Kuwait", "description" => "(UTC+03:00) Kuwait"]);
        $this->timezone->create(["name" => "Europe/Minsk", "description" => "(UTC+03:00) Minsk"]);
        $this->timezone->create(["name" => "Africa/Nairobi", "description" => "(UTC+03:00) Nairobi"]);
        $this->timezone->create(["name" => "Asia/Riyadh", "description" => "(UTC+03:00) Riyadh"]);
        $this->timezone->create(["name" => "Europe/Volgograd", "description" => "(UTC+03:00) Volgograd"]);
        $this->timezone->create(["name" => "Asia/Tehran", "description" => "(UTC+03:30) Tehran"]);
        $this->timezone->create(["name" => "Asia/Muscat", "description" => "(UTC+04:00) Abu Dhabi"]);
        $this->timezone->create(["name" => "Asia/Baku", "description" => "(UTC+04:00) Baku"]);
        $this->timezone->create(["name" => "Europe/Moscow", "description" => "(UTC+04:00) Moscow"]);
        $this->timezone->create(["name" => "Asia/Muscat", "description" => "(UTC+04:00) Muscat"]);
        $this->timezone->create(["name" => "Europe/Moscow", "description" => "(UTC+04:00) St. Petersburg"]);
        $this->timezone->create(["name" => "Asia/Tbilisi", "description" => "(UTC+04:00) Tbilisi"]);
        $this->timezone->create(["name" => "Asia/Yerevan", "description" => "(UTC+04:00) Yerevan"]);
        $this->timezone->create(["name" => "Asia/Kabul", "description" => "(UTC+04:30) Kabul"]);
        $this->timezone->create(["name" => "Asia/Karachi", "description" => "(UTC+05:00) Islamabad"]);
        $this->timezone->create(["name" => "Asia/Karachi", "description" => "(UTC+05:00) Karachi"]);
        $this->timezone->create(["name" => "Asia/Tashkent", "description" => "(UTC+05:00) Tashkent"]);
        $this->timezone->create(["name" => "Asia/Calcutta", "description" => "(UTC+05:30) Chennai"]);
        $this->timezone->create(["name" => "Asia/Kolkata", "description" => "(UTC+05:30) Kolkata"]);
        $this->timezone->create(["name" => "Asia/Calcutta", "description" => "(UTC+05:30) Mumbai"]);
        $this->timezone->create(["name" => "Asia/Calcutta", "description" => "(UTC+05:30) New Delhi"]);
        $this->timezone->create(["name" => "Asia/Calcutta", "description" => "(UTC+05:30) Sri Jayawardenepura"]);
        $this->timezone->create(["name" => "Asia/Katmandu", "description" => "(UTC+05:45) Kathmandu"]);
        $this->timezone->create(["name" => "Asia/Almaty", "description" => "(UTC+06:00) Almaty"]);
        $this->timezone->create(["name" => "Asia/Dhaka", "description" => "(UTC+06:00) Astana"]);
        $this->timezone->create(["name" => "Asia/Dhaka", "description" => "(UTC+06:00) Dhaka"]);
        $this->timezone->create(["name" => "Asia/Yekaterinburg", "description" => "(UTC+06:00) Ekaterinburg"]);
        $this->timezone->create(["name" => "Asia/Rangoon", "description" => "(UTC+06:30) Rangoon"]);
        $this->timezone->create(["name" => "Asia/Bangkok", "description" => "(UTC+07:00) Bangkok"]);
        $this->timezone->create(["name" => "Asia/Bangkok", "description" => "(UTC+07:00) Hanoi"]);
        $this->timezone->create(["name" => "Asia/Jakarta", "description" => "(UTC+07:00) Jakarta"]);
        $this->timezone->create(["name" => "Asia/Novosibirsk", "description" => "(UTC+07:00) Novosibirsk"]);
        $this->timezone->create(["name" => "Asia/Hong_Kong", "description" => "(UTC+08:00) Beijing"]);
        $this->timezone->create(["name" => "Asia/Chongqing", "description" => "(UTC+08:00) Chongqing"]);
        $this->timezone->create(["name" => "Asia/Hong_Kong", "description" => "(UTC+08:00) Hong Kong"]);
        $this->timezone->create(["name" => "Asia/Krasnoyarsk", "description" => "(UTC+08:00) Krasnoyarsk"]);
        $this->timezone->create(["name" => "Asia/Kuala_Lumpur", "description" => "(UTC+08:00) Kuala Lumpur"]);
        $this->timezone->create(["name" => "Australia/Perth", "description" => "(UTC+08:00) Perth"]);
        $this->timezone->create(["name" => "Asia/Singapore", "description" => "(UTC+08:00) Singapore"]);
        $this->timezone->create(["name" => "Asia/Taipei", "description" => "(UTC+08:00) Taipei"]);
        $this->timezone->create(["name" => "Asia/Ulan_Bator", "description" => "(UTC+08:00) Ulaan Bataar"]);
        $this->timezone->create(["name" => "Asia/Urumqi", "description" => "(UTC+08:00) Urumqi"]);
        $this->timezone->create(["name" => "Asia/Irkutsk", "description" => "(UTC+09:00) Irkutsk"]);
        $this->timezone->create(["name" => "Asia/Tokyo", "description" => "(UTC+09:00) Osaka"]);
        $this->timezone->create(["name" => "Asia/Tokyo", "description" => "(UTC+09:00) Sapporo"]);
        $this->timezone->create(["name" => "Asia/Seoul", "description" => "(UTC+09:00) Seoul"]);
        $this->timezone->create(["name" => "Asia/Tokyo", "description" => "(UTC+09:00) Tokyo"]);
        $this->timezone->create(["name" => "Australia/Adelaide", "description" => "(UTC+09:30) Adelaide"]);
        $this->timezone->create(["name" => "Australia/Darwin", "description" => "(UTC+09:30) Darwin"]);
        $this->timezone->create(["name" => "Australia/Brisbane", "description" => "(UTC+10:00) Brisbane"]);
        $this->timezone->create(["name" => "Australia/Canberra", "description" => "(UTC+10:00) Canberra"]);
        $this->timezone->create(["name" => "Pacific/Guam", "description" => "(UTC+10:00) Guam"]);
        $this->timezone->create(["name" => "Australia/Hobart", "description" => "(UTC+10:00) Hobart"]);
        $this->timezone->create(["name" => "Australia/Melbourne", "description" => "(UTC+10:00) Melbourne"]);
        $this->timezone->create(["name" => "Pacific/Port_Moresby", "description" => "(UTC+10:00) Port Moresby"]);
        $this->timezone->create(["name" => "Australia/Sydney", "description" => "(UTC+10:00) Sydney"]);
        $this->timezone->create(["name" => "Asia/Yakutsk", "description" => "(UTC+10:00) Yakutsk"]);
        $this->timezone->create(["name" => "Asia/Vladivostok", "description" => "(UTC+11:00) Vladivostok"]);
        $this->timezone->create(["name" => "Pacific/Auckland", "description" => "(UTC+12:00) Auckland"]);
        $this->timezone->create(["name" => "Pacific/Fiji", "description" => "(UTC+12:00) Fiji"]);
        $this->timezone->create(["name" => "Pacific/Kwajalein", "description" => "(UTC+12:00) International Date Line West"]);
        $this->timezone->create(["name" => "Asia/Kamchatka", "description" => "(UTC+12:00) Kamchatka"]);
        $this->timezone->create(["name" => "Asia/Magadan", "description" => "(UTC+12:00) Magadan"]);
        $this->timezone->create(["name" => "Pacific/Fiji", "description" => "(UTC+12:00) Marshall Is."]);
        $this->timezone->create(["name" => "Asia/Magadan", "description" => "(UTC+12:00) New Caledonia"]);
        $this->timezone->create(["name" => "Asia/Magadan", "description" => "(UTC+12:00) Solomon Is."]);
        $this->timezone->create(["name" => "Pacific/Auckland", "description" => "(UTC+12:00) Wellington"]);
        $this->timezone->create(["name" => "Pacific/Tongatapu", "description" => "(UTC+13:00) Nuku'alofa"]);
        $this->timezone->create(["name" => "Asia/Qatar", "description" => "(UTC+03:00) Qatar"]);
    }
}