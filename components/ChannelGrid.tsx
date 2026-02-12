"use client";
import { useEffect, useState } from "react";

type YTItem = { videoId: string; title: string; thumb: string };

const DEFAULT_CHANNELS = ["UC_x5XG1OV2P6uZZ5FSM9Ttw"]; // replace

export function ChannelGrid(){
  const [items, setItems] = useState<YTItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try{
        const cid = DEFAULT_CHANNELS[0];
        const res = await fetch(`/api/youtube/channel/${cid}`);
        const json = await res.json();
        setItems(json.items ?? []);
      } finally{
        setLoading(false);
      }
    })();
  }, []);

  if (loading) return <div className="text-muted">Loading channel feed…</div>;

  return (
    <div className="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
      {items.map((v) => (
        <a key={v.videoId} href={`https://www.youtube.com/watch?v=${v.videoId}`} target="_blank" rel="noreferrer"
          className="flex gap-3 rounded-[18px] border border-line bg-card p-3 hover:bg-white/5">
          <div className="h-20 w-32 overflow-hidden rounded-xl border border-line bg-white/5"
            style={{ backgroundImage: `url(${v.thumb})`, backgroundSize: "cover", backgroundPosition: "center" }} />
          <div className="min-w-0">
            <div className="line-clamp-2 text-sm font-extrabold">{v.title}</div>
            <div className="mt-1 text-xs text-muted">Open on YouTube (MVP)</div>
          </div>
        </a>
      ))}
    </div>
  );
}
