"use client";
import { useState } from "react";
import { IQVideo } from "@/lib/content";
import { MuxHlsPlayer } from "@/components/MuxHlsPlayer";
import { AslPanel } from "@/components/AslPanel";

async function buyNow(priceId?: string){
  if (!priceId) return;
  const res = await fetch("/api/stripe/checkout", {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({ priceId }),
  });
  const data = await res.json();
  if (data?.url) window.location.href = data.url;
}

export function WatchClient({ v }: { v: IQVideo }){
  const [t, setT] = useState(0);
  const [playing, setPlaying] = useState(false);

  const position = v.aslDefaultPosition ?? "below";
  const isSide = position === "side";

  return (
    <div className="pt-6">
      <div className="mb-4">
        <h1 className="text-2xl font-black">{v.title}</h1>
        {v.description ? <p className="mt-2 text-muted">{v.description}</p> : null}
      </div>

      {v.isPaid ? (
        <div className="mb-4 rounded-[18px] border border-line bg-card p-4">
          <div className="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div className="text-sm font-extrabold">Paid title</div>
              <div className="text-xs text-muted">{v.priceLabel ?? "Purchase to unlock."}</div>
            </div>
            <button onClick={() => buyNow(v.stripePriceId)}
              className="rounded-xl bg-white px-4 py-2 text-sm font-black text-black hover:opacity-90">
              Buy / Rent
            </button>
          </div>
        </div>
      ) : null}

      <div className={isSide ? "flex flex-col gap-4 lg:flex-row" : "space-y-4"}>
        <div className="w-full flex-1">
          {v.source === "mux" ? (
            v.muxPlaybackId && v.muxPlaybackId !== "YOUR_MUX_PLAYBACK_ID" ? (
              <MuxHlsPlayer playbackId={v.muxPlaybackId}
                onTime={setT} onPlay={() => setPlaying(true)} onPause={() => setPlaying(false)} />
            ) : (
              <div className="rounded-[18px] border border-line bg-card p-4 text-muted">
                Add a real Mux Playback ID in <code className="text-white/80">lib/content.ts</code>.
              </div>
            )
          ) : (
            <div className="overflow-hidden rounded-[18px] border border-line bg-black/40">
              <iframe className="aspect-video w-full"
                src={`https://www.youtube.com/embed/${v.youtubeId}?rel=0`}
                title={v.title}
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen />
            </div>
          )}
        </div>

        <AslPanel url={v.aslAvatarUrl} masterTime={t} masterPlaying={playing} position={position} />
      </div>
    </div>
  );
}
