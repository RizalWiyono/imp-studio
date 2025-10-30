import Link from "next/link";
import Image from "next/image";

export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center p-10">
      <Image
        src="/logo.png"
        alt="Logo IMP Studio"
        width={150}
        height={150}
        className="mb-6"
      />
      <h1 className="text-4xl font-bold mb-6">Tes IMP Studio</h1>

      <div className="flex gap-4">
        <Link href="/articles" className="btn btn-outline">
          Lihat Artikel
        </Link>
      </div>
    </main>
  );
}
