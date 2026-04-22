<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Users ----
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gatekeeper.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ADMIN,
        ]);

        $org1 = User::create([
            'name'     => 'Rahim Ahmed',
            'email'    => 'organizer@gatekeeper.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ORGANIZER,
        ]);

        $org2 = User::create([
            'name'     => 'Nusrat Jahan',
            'email'    => 'nusrat@gatekeeper.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ORGANIZER,
        ]);

        $attendee = User::create([
            'name'     => 'Attendee Demo',
            'email'    => 'user@gatekeeper.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ATTENDEE,
        ]);

        $attendee2 = User::create([
            'name'     => 'Sumon Kabir',
            'email'    => 'sumon@gatekeeper.test',
            'password' => Hash::make('password'),
            'role'     => User::ROLE_ATTENDEE,
        ]);

        // ---- Events ----
        $events = [
            [
                'organizer'   => $org1,
                'title'       => 'Dhaka Tech Summit 2026',
                'description' => "The biggest tech conference in Bangladesh featuring talks on AI, web3, robotics, and developer tooling. Network with industry leaders, attend hands-on workshops, and discover the latest in software innovation.",
                'date'        => now()->addDays(20)->setTime(10, 0),
                'location'    => 'Dhaka Convention Center',
                'image'       => 'images/events/tech.jpg',
                'status'      => Event::STATUS_PUBLISHED,
                'categories'  => [
                    ['name' => 'Regular', 'price' => 1200, 'capacity' => 200],
                    ['name' => 'VIP',     'price' => 3000, 'capacity' => 30],
                    ['name' => 'Student', 'price' => 500,  'capacity' => 80],
                ],
            ],
            [
                'organizer'   => $org1,
                'title'       => 'Rajshahi Startup Meetup',
                'description' => "An evening of pitches, panel discussions, and networking with founders and investors from across the northern region of Bangladesh.",
                'date'        => now()->addDays(35)->setTime(17, 30),
                'location'    => 'Rajshahi IT Park',
                'image'       => 'images/events/startup.jpg',
                'status'      => Event::STATUS_PUBLISHED,
                'categories'  => [
                    ['name' => 'Standard', 'price' => 800,  'capacity' => 100],
                    ['name' => 'Premium',  'price' => 1500, 'capacity' => 20],
                ],
            ],
            [
                'organizer'   => $org2,
                'title'       => 'Bangladesh Music Festival',
                'description' => "A spectacular cultural night with the country's most celebrated artists performing under the stars. Food, art, and live music all evening.",
                'date'        => now()->addDays(50)->setTime(18, 0),
                'location'    => 'Army Stadium, Dhaka',
                'image'       => 'images/events/music.jpg',
                'status'      => Event::STATUS_PUBLISHED,
                'categories'  => [
                    ['name' => 'General Admission', 'price' => 1500, 'capacity' => 500],
                    ['name' => 'Front Row',         'price' => 5000, 'capacity' => 50],
                ],
            ],
            [
                'organizer'   => $org2,
                'title'       => 'AI & Machine Learning Conference',
                'description' => "Workshops, keynotes, and labs on practical AI/ML for engineers, researchers, and product folks.",
                'date'        => now()->addDays(60)->setTime(9, 0),
                'location'    => 'Dhaka International Convention Center',
                'image'       => 'images/events/ai.jpg',
                'status'      => Event::STATUS_PUBLISHED,
                'categories'  => [
                    ['name' => 'Conference Pass', 'price' => 2500, 'capacity' => 150],
                    ['name' => 'Workshop Add-on', 'price' => 1000, 'capacity' => 60],
                ],
            ],
            [
                'organizer'   => $org1,
                'title'       => 'Design Thinking Workshop',
                'description' => "Hands-on workshop covering empathy mapping, prototyping, and user testing. Bring a laptop.",
                'date'        => now()->addDays(75)->setTime(10, 0),
                'location'    => 'Rajshahi Innovation Lab',
                'image'       => 'images/events/design.jpg',
                'status'      => Event::STATUS_DRAFT,
                'categories'  => [
                    ['name' => 'Seat', 'price' => 600, 'capacity' => 40],
                ],
            ],
        ];

        foreach ($events as $data) {
            $event = Event::create([
                'organizer_id' => $data['organizer']->id,
                'title'        => $data['title'],
                'description'  => $data['description'],
                'date'         => $data['date'],
                'location'     => $data['location'],
                'image'        => $data['image'],
                'status'       => $data['status'],
            ]);
            foreach ($data['categories'] as $c) {
                $event->categories()->create($c);
            }
        }

        // ---- A few demo tickets so dashboards aren't empty ----
        $firstEvent = Event::published()->first();
        if ($firstEvent) {
            $regCat = $firstEvent->categories()->first();
            $vipCat = $firstEvent->categories()->skip(1)->first() ?? $regCat;

            Ticket::create([
                'user_id'        => $attendee->id,
                'event_id'       => $firstEvent->id,
                'category_id'    => $regCat->id,
                'payment_status' => Ticket::PAYMENT_PAID,
            ]);
            Ticket::create([
                'user_id'        => $attendee2->id,
                'event_id'       => $firstEvent->id,
                'category_id'    => $vipCat->id,
                'payment_status' => Ticket::PAYMENT_PAID,
            ]);
            Ticket::create([
                'user_id'        => $attendee->id,
                'event_id'       => $firstEvent->id,
                'category_id'    => $regCat->id,
                'payment_status' => Ticket::PAYMENT_PENDING,
            ]);
        }

        $this->command->info('');
        $this->command->info('================================');
        $this->command->info('  GateKeeper demo data seeded');
        $this->command->info('================================');
        $this->command->info('  admin@gatekeeper.test     / password');
        $this->command->info('  organizer@gatekeeper.test / password');
        $this->command->info('  user@gatekeeper.test      / password');
        $this->command->info('================================');
    }
}
