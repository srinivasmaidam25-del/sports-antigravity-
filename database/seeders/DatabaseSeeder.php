<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Facility;
use App\Models\Membership;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::updateOrCreate(
            ['email' => 'superadmin@thecrickethub.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '9999999999',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@thecrickethub.com'],
            [
                'name' => 'Business Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '8888888888',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@thecrickethub.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '7777777777',
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@thecrickethub.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '9876543210',
                'referral_code' => 'CRICKETHUB10',
            ]
        );

        // 2. Seed Facilities
        Facility::updateOrCreate(
            ['slug' => 'box-cricket'],
            [
                'name' => 'Box Cricket',
                'description' => 'Premium outdoor box cricket arena.',
                'dimensions' => '110 x 60',
                'duration_minutes' => 60,
                'base_price_weekday' => 600.00,
                'base_price_weekend' => 700.00,
                'is_active' => true,
            ]
        );

        Facility::updateOrCreate(
            ['slug' => 'practice-net-1'],
            [
                'name' => 'Practice Net 1',
                'description' => 'Professional cricket practice net 1.',
                'dimensions' => '110 x 16',
                'duration_minutes' => 60,
                'base_price_weekday' => 200.00,
                'base_price_weekend' => 250.00,
                'is_active' => true,
            ]
        );

        Facility::updateOrCreate(
            ['slug' => 'practice-net-2'],
            [
                'name' => 'Practice Net 2',
                'description' => 'Professional cricket practice net 2.',
                'dimensions' => '110 x 16',
                'duration_minutes' => 60,
                'base_price_weekday' => 200.00,
                'base_price_weekend' => 250.00,
                'is_active' => true,
            ]
        );

        Facility::updateOrCreate(
            ['slug' => 'open-cricket-pitch'],
            [
                'name' => 'Open Cricket Pitch',
                'description' => 'Massive 1-acre open cricket outfield and pitch.',
                'dimensions' => '1 Acre',
                'duration_minutes' => 120, // 2-hour booking
                'base_price_weekday' => 1400.00,
                'base_price_weekend' => 1600.00,
                'is_active' => true,
            ]
        );

        // 3. Seed Memberships
        Membership::updateOrCreate(
            ['name' => 'Bronze'],
            [
                'price' => 2499.00,
                'total_hours' => 4,
                'discount_percentage' => 10.00,
                'priority_booking' => false,
                'kit_rental' => false,
                'special_benefits' => json_encode(['description' => '4 hours free + 10% discount on additional hours']),
                'is_active' => true,
            ]
        );

        Membership::updateOrCreate(
            ['name' => 'Silver'],
            [
                'price' => 4999.00,
                'total_hours' => 8,
                'discount_percentage' => 15.00,
                'priority_booking' => true,
                'kit_rental' => false,
                'special_benefits' => json_encode(['description' => '8 hours free + 15% discount on additional hours + Priority booking']),
                'is_active' => true,
            ]
        );

        Membership::updateOrCreate(
            ['name' => 'Gold'],
            [
                'price' => 9499.00,
                'total_hours' => 16,
                'discount_percentage' => 20.00,
                'priority_booking' => true,
                'kit_rental' => true,
                'special_benefits' => json_encode(['description' => '16 hours free + 20% discount on additional hours + Kit rental']),
                'is_active' => true,
            ]
        );

        Membership::updateOrCreate(
            ['name' => 'Platinum'],
            [
                'price' => 14999.00,
                'total_hours' => 24,
                'discount_percentage' => 25.00,
                'priority_booking' => true,
                'kit_rental' => true,
                'special_benefits' => json_encode([
                    'description' => '24 hours free + 25% discount on additional hours + Kit rental + Birthday offers + Tournament discounts',
                    'weekend_discount' => 25.00,
                    'birthday_offer' => true,
                    'tournament_discount' => true,
                ]),
                'is_active' => true,
            ]
        );

        // 4. Seed Settings
        $settings = [
            'weekday_days' => json_encode([1, 2, 3, 4]), // Mon, Tue, Wed, Thu
            'friday_weekend_cutoff' => '17:00', // Friday after 5 PM is weekend
            'weekend_days' => json_encode([6, 7]), // Sat, Sun (Friday handled via cutoff)
            'operating_hours_mon_thu' => json_encode(['start' => '06:00', 'end' => '23:00']),
            'operating_hours_fri_sun' => json_encode(['start' => '06:00', 'end' => '00:00']),
            'advance_payment_box-cricket' => '150.00',
            'advance_payment_practice-net-1' => '100.00',
            'advance_payment_practice-net-2' => '100.00',
            'advance_payment_open-cricket-pitch' => '400.00', // Configurable advance payment
            'referral_discount_percentage' => '10.00',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
