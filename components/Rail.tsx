import { IQVideo } from "@/lib/content";
import { VideoCard } from "@/components/VideoCard";

export function Rail({ title, items }: { title: string; items: IQVideo[] }){
  return (
    <section>
      <div className="mb-3 flex items-end justify-between">
        <h2 className="text-xl font-black">{title}</h2>
      </div>
      <div className="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-6">
        {items.map(v => <VideoCard key={v.id} v={v} />)}
      </div>
    </section>
  );
}
