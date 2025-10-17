<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(5)->create();
        $users = User::all();

        foreach ($users as $u) {
            Post::factory()->count(3)->create([
                'user_id' => $u->id,
                'status' => ['active','draft','archived'][rand(0,2)],
            ]);
        }

        // Комменты к постам
        $posts = Post::all();
        foreach ($posts as $p) {
            Comment::factory()->count(2)->create([
                'user_id' => $users->random()->id,
                'commentable_type' => Post::class,
                'commentable_id' => $p->id,
            ]);
        }

        // Ответы на комментарии
        $comments = Comment::where('commentable_type', Post::class)->get();
        foreach ($comments as $c) {
            Comment::factory()->count(1)->create([
                'user_id' => $users->random()->id,
                'commentable_type' => Comment::class,
                'commentable_id' => $c->id,
            ]);
        }
    }

}
