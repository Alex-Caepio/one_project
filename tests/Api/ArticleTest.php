<?php

namespace Tests\Api;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

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
        $service = Article::factory()->make();
        $user = User::factory()->make();
        $response = $this->json('post', '/api/articles', [
            'description' => $service->description,
            'introduction' => $service->introduction,
            'is_published' => $service->is_published,
            'title' => $service->title,
            'user_id' => $this->user->id,
            'url' => $service->url,
        ]);
        $response->assertOk();
    }

    public function test_delete_article(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('delete', "/api/articles/{$article->id}");

        $response->assertStatus(204);
    }

    public function test_update_article(): void
    {
        $article = Article::factory()->create();
        $newArticle = Article::factory()->make();

        $response = $this->json('put', "/api/articles/{$article->id}",
            [
                'title' => $newArticle->title,
            ]);

        $response->assertOk()
            ->assertJson([
                'title' => $newArticle->title,
            ]);
    }

    public function test_show_article(): void
    {
        $article = Article::factory()->create();
        $response = $this->json('get', "api/articles/{$article->id}");

        $response
            ->assertOk();
    }

    public function test_store_article_favorite(): void
    {
        $authUser = User::factory()->create();
        $articleId = Article::factory()->create();
        $response = $this->json('post', "article/{$articleId->id}/favourite");
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
}
