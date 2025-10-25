<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $users = User::factory(20)->create();

        $posts = Post::factory(15)->create([
            'user_id' => $users->random()->id
        ]);

        $comments = Comment::factory(15)->create([
            'post_id' => $posts->random()->id,
            'user_id' => $users->random()->id
        ]);


        foreach ($posts as $post) {
            foreach ($users->random(rand(0,10)) as $user) {
                DB::table('post_user_reactions')->insert([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'reaction' => fake()->randomElement(['like' , 'dislike']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
