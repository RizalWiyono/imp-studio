<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleApiController extends Controller
{
    /**
     * GET /api/v1/articles
     * List all articles (with optional search & pagination)
     */
    public function index(Request $request)
    {
        try {
            $query = Article::query();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('content', 'like', "%{$request->search}%");
                });
            }

            $perPage = $request->get('per_page', 10);

            $articles = $query->with(['user.profile'])->latest()->paginate($perPage);

            $articles->getCollection()->transform(function ($article) {
                $profile = $article->user?->profile;
                $article->author_name = $profile
                    ? trim("{$profile->first_name} {$profile->last_name}")
                    : null;
                unset($article->user);
                return $article;
            });

            return response()->json([
                'success' => true,
                'message' => 'Articles fetched successfully',
                'data' => $articles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch articles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * GET /api/v1/articles/{slug}
     * Show single article by slug
     */
    public function show($slug)
    {
        try {
            // Cari artikel berdasarkan slug (bukan ID)
            $article = Article::with('user.profile')
                ->where('slug', $slug)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Article fetched successfully',
                'data' => $article
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch article',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
