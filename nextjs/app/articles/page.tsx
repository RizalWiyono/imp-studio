import ArticleCard from "@/components/ArticleCard";

async function getArticles() {
  const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/v1/articles`, {
    next: { revalidate: 60 },
  });
  const json = await res.json();
  return json.data?.data || [];
}

export default async function ArticlesPage() {
  const articles = await getArticles();

  return (
    <main className="min-h-screen py-16">
      <section className="container mx-auto px-6">
        <div className="text-center mb-12">
          <h1 className="text-4xl font-extrabold text-primary mb-3">
            Artikel Terbaru
          </h1>
          <p className="text-gray-500 max-w-2xl mx-auto">
            Temukan wawasan menarik, berita terbaru, dan tips profesional dari
            tim IMP Studio. Setiap artikel dirancang untuk menginspirasi dan
            memperluas pandangan Anda.
          </p>
        </div>

        {articles.length === 0 ? (
          <div className="flex justify-center items-center h-64">
            <div className="text-center">
              <div className="text-gray-400 text-lg mb-2">
                📭 Belum ada artikel yang tersedia.
              </div>
              <p className="text-sm text-gray-500">
                Kami sedang menyiapkan konten terbaik untuk Anda.
              </p>
            </div>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {articles.map((a: any) => (
              <ArticleCard key={a.slug} {...a} />
            ))}
          </div>
        )}
      </section>
    </main>
  );
}
