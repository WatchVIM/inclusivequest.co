"use client";
import { useEffect, useRef } from "react";
import MuxPlayer from "@mux/mux-player-react";

export function MuxHlsPlayer({
  playbackId, onTime, onPlay, onPause
}:{
  playbackId: string;
  onTime?: (t:number)=>void;
  onPlay?: ()=>void;
  onPause?: ()=>void;
}){
  const ref = useRef<any>(null);

  useEffect(() => {
    const el = ref.current as any;
    if (!el) return;

    const handleTime = () => onTime?.(el.currentTime ?? 0);
    const handlePlay = () => onPlay?.();
    const handlePause = () => onPause?.();

    el.addEventListener("timeupdate", handleTime);
    el.addEventListener("play", handlePlay);
    el.addEventListener("pause", handlePause);

    return () => {
      el.removeEventListener("timeupdate", handleTime);
      el.removeEventListener("play", handlePlay);
      el.removeEventListener("pause", handlePause);
    };
  }, [onTime, onPlay, onPause]);

  return (
    <div className="overflow-hidden rounded-[18px] border border-line bg-black/40">
      <MuxPlayer ref={ref} playbackId={playbackId} streamType="on-demand"
        style={{ width: "100%", height: "100%" }} />
    </div>
  );
}
