import Image from "next/image";
import Link from "next/link";
import { IQVideo } from "@/lib/content";

export function VideoCard({ v }: { v: IQVideo }){
  return (
    <Link href={`/watch/${v.id}`} className="group overflow-hidden rounded-[18px] border border-line bg-card hover:bg-white/5">
      <div className="relative aspect-video">
        {v.posterUrl ? (
          <Image src={v.posterUrl} alt="" fill className="object-cover transition-transform duration-200 group-hover:scale-[1.02]"
            sizes="(max-width: 1024px) 50vw, 16vw" />
        ) : <div className="h-full w-full bg-white/5" />}
        <span className="absolute left-2 top-2 rounded-full bg-black/60 px-2 py-1 text-xs font-bold">
          {v.isPaid ? "Paid" : "Free"}
        </span>
      </div>
      <div className="p-2.5">
        <div className="line-clamp-2 text-sm font-extrabold">{v.title}</div>
        <div className="mt-1 text-xs text-muted">{v.source === "mux" ? "Mux HLS" : "YouTube"}</div>
      </div>
    </Link>
  );
}
