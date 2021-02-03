<?php

namespace Tests\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;

class ArticleTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
        $this->login($this->user);
    }

    public function test_user_can_see_article_list(): void
    {
        Article::factory()->count(2)->create();
        $response = $this->json('get', "/api/articles");
        $response
            ->assertOk();
    }

    public function test_can_create_article(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner', 'is_published' => true]);
        $article = Article::factory()->make();

        $response = $this->actingAs($user)->json('post', '/api/articles', [
            'description' => $article->description,
            'introduction' => $article->introduction,
            'is_published' => $article->is_published,
            'title' => $article->title,
            'user_id' => $user->id,
            'url' => $article->url,
        ]);
        $response->assertOk();
    }

    public function test_delete_article(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $article = Article::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->json('delete', "/api/articles/{$article->id}");

        $response->assertStatus(204);
    }

    public function test_update_article(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);

        $article = Article::factory()->create();
        $newArticle = Article::factory()->make();

        $response = $this->actingAs($user)->json('put', "/api/articles/{$article->id}",
            [
                'title' => $newArticle->title,
                'media_images'           => [
                    'http://google.com',
                    'http://google.com',
                ],
                'media_videos'           => [
                    'http://google.com',
                    'http://google.com',
                ],
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newArticle->title,
            ]);
        self::assertCount(2, $article->media_images);
        self::assertCount(2, $article->media_videos);
    }

    public function test_show_article(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner']);
        $article = Article::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->json('get', "api/articles/practitioner/{$article->id}");

        $response
            ->assertOk();
    }

    public function test_store_article_favorite(): void
    {
        $authUser = User::factory()->create();
        $articleId = Article::factory()->create();
        $this->json('post', "article/{$articleId->id}/favourite");
        $authUser->favourite_articles()->attach($articleId);

        $this->assertDatabaseHas('article_favorites', [
            'user_id' => $authUser->id,
            'article_id' => $articleId->id
        ]);
    }
    public function test_delete_article_favorite(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('delete', "/api/articles/{$article->id}/favourite");
        $authUser = User::factory()->create();
        $authUser->favourite_articles()->attach($article);
        $response->assertStatus(204);
    }

    public function test_can_create_article_with_media_images(): void
    {
        $user = User::factory()->create(['account_type' => 'practitioner', 'is_published' => true]);
        $article = Article::factory()->make();
        $response = $this->actingAs($user)->json('post', '/api/articles', [
            'description' => $article->description,
            'introduction' => $article->introduction,
            'is_published' => $article->is_published,
            'title' => $article->title,
            'user_id' => $user->id,
            'url' => $article->url,
            'media_images' => [
                 'http://google.com',
                 'http://google.com',
            ]
        ]);
        $response->assertOk();
        $this->assertCount(2, Article::first()->media_images);
    }
}
