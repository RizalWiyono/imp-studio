<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Article::with('user');

                return DataTables::of($query)
                    ->addColumn('author', fn($article) => $article->user?->name ?? '-')
                    ->addColumn('status_badge', function ($article) {
                        $badgeClass = match ($article->status) {
                            'published' => 'badge-success',
                            'draft' => 'badge-secondary',
                            'archived' => 'badge-warning',
                            default => 'badge-light',
                        };
                        return '<span class="badge ' . $badgeClass . '">' . ucfirst($article->status) . '</span>';
                    })
                    ->addColumn('thumbnail', function ($article) {
                        $url = $article->thumbnail_url;
                        return '<img src="' . e($url) . '" alt="thumbnail" class="rounded" width="60" height="40">';
                    })
                    ->addColumn('actions', function ($article) {
                        $btn = '<button class="btn btn-warning btn-sm btn-edit" data-id="' . $article->id . '">Edit</button> ';
                        $btn .= '<button class="btn btn-danger btn-sm btn-delete" data-id="' . $article->id . '">Delete</button>';
                        return $btn;
                    })
                    ->editColumn('published_at', fn($article) => $article->published_at?->format('Y-m-d H:i') ?? '-')
                    ->rawColumns(['status_badge', 'actions', 'thumbnail'])
                    ->make(true);
            }

            return view('dashboard.articles.index');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        return view('dashboard.articles.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'title' => 'required|min:3|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'status' => 'required|in:draft,published,archived',
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);

            $article = new Article($validated);
            $article->user_id = auth()->id();
            $article->slug = Str::slug($validated['title']);

            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('uploads/articles', 'public');
                $article->thumbnail_path = $path;
                $article->thumbnail_url = asset('storage/' . $path);
            }

            if ($validated['status'] === 'published') {
                $article->published_at = now();
            }

            $article->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Article created successfully.',
                'article' => $article,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Article $article)
    {
        try {
            if (request()->ajax()) {
                return response()->json($article);
            }
            return view('dashboard.articles.edit', compact('article'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Article $article)
    {
        try {
            return view('dashboard.articles.show', compact('article'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Article $article)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'title' => 'required|min:3|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'status' => 'required|in:draft,published,archived',
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);

            // Handle thumbnail update
            if ($request->hasFile('thumbnail')) {
                if ($article->thumbnail_path) {
                    Storage::disk('public')->delete($article->thumbnail_path);
                }

                $path = $request->file('thumbnail')->store('uploads/articles', 'public');
                $validated['thumbnail_path'] = $path;
                $validated['thumbnail_url'] = asset('storage/' . $path);
            }

            if ($validated['status'] === 'published' && !$article->published_at) {
                $validated['published_at'] = now();
            }

            $validated['slug'] = Str::slug($validated['title']);
            $article->update($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Article updated successfully.',
                'article' => $article,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Article $article)
    {
        try {
            if ($article->thumbnail_path) {
                Storage::disk('public')->delete($article->thumbnail_path);
            }

            $article->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
