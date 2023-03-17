<?php


namespace Tests\Admin;

use App\Http\Requests\Request;
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

        $this->user = $this->createAdmin();
        $this->login($this->user);
    }

    public function test_admin_can_see_article_list(): void
    {
        Article::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->json('get','/admin/articles');
        $response->assertOk();
    }

    public function test_admin_can_publish_articles(): void
    {
        $article = Article::factory()->create(['is_published' => false]);

        $response = $this->actingAs($this->user)->json('post',"/admin/articles/{$article->id}/publish");
        $response->assertOk();
    }

    public function test_admin_can_set_unpublished_articles(): void
    {
        $article = Article::factory()->create(['is_published' => true]);

        $response = $this->actingAs($this->user)->json('post',"/admin/articles/{$article->id}/unpublish");
        $response->assertStatus(204);
    }

    public function test_admin_can_delete_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->user)->json('delete',"/admin/articles/{$article->id}");
        $response->assertStatus(204);
    }
}
