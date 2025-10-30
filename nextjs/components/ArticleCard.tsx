"use client";
import Link from "next/link";

interface ArticleCardProps {
  title: string;
  excerpt?: string;
  slug: string;
  thumbnail_url?: string;
  author_name?: string;
  published_at?: string;
}

export default function ArticleCard({
  title,
  excerpt,
  slug,
  thumbnail_url,
  author_name,
  published_at,
}: ArticleCardProps) {
  return (
    <div className="card bg-base-100 shadow-md hover:shadow-xl transition-all duration-200">
      <figure>
        <img
          src={thumbnail_url || "/placeholder.jpg"}
          alt={title}
          className="h-48 w-full object-cover"
        />
      </figure>
      <div className="card-body">
        <h2 className="card-title text-lg font-bold">
          <Link href={`/articles/${slug}`} className="hover:text-primary">
            {title}
          </Link>
        </h2>
        {excerpt && <p className="text-sm text-gray-500">{excerpt}</p>}

        <div className="flex justify-between text-xs text-gray-400 mt-2">
          <span>{author_name || "Anonim"}</span>
          <span>
            {published_at
              ? new Date(published_at).toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "short",
                  year: "numeric",
                })
              : ""}
          </span>
        </div>
      </div>
    </div>
  );
}
