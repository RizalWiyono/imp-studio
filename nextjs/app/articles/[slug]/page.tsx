import Link from "next/link";

async function getArticle(slug: string) {
  const res = await fetch(
    `${process.env.NEXT_PUBLIC_API_URL}/v1/articles/${slug}`,
    { next: { revalidate: 60 } }
  );
  return res.json();
}

export default async function ArticleDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  console.log("📦 Slug dari route:", slug);

  const { data, success } = await getArticle(slug);

  if (!success) {
    return (
      <div className="container mx-auto py-10 text-center">
        <h2 className="text-2xl font-semibold text-error">
          Artikel tidak ditemukan
        </h2>
        <Link href="/articles" className="btn btn-outline mt-4">
          Kembali
        </Link>
      </div>
    );
  }

  const article = data;

  return (
    <div className="container mx-auto px-4 py-10 prose max-w-3xl">
      <h1 className="text-4xl font-bold mb-4">{article.title}</h1>
      <div className="flex items-center justify-between text-sm text-gray-500 mb-6">
        <span>{article.author_name || "Anonim"}</span>
        {article.published_at && (
          <span>
            {new Date(article.published_at).toLocaleDateString("id-ID", {
              day: "2-digit",
              month: "short",
              year: "numeric",
            })}
          </span>
        )}
      </div>
      {article.thumbnail_url && (
        <img
          src={article.thumbnail_url}
          alt={article.title}
          className="w-full rounded-lg mb-6"
        />
      )}
      <div dangerouslySetInnerHTML={{ __html: article.content }} />
    </div>
  );
}
