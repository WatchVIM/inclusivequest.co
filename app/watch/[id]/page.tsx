import { notFound } from "next/navigation";
import { featured } from "@/lib/content";
import { WatchClient } from "@/components/WatchClient";

export default function WatchPage({ params }: { params: { id: string } }){
  const v = featured.find(x => x.id === params.id);
  if (!v) return notFound();
  return <WatchClient v={v} />;
}
