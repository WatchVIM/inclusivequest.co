export type VideoSource = "mux" | "youtube";

export type IQVideo = {
  id: string;
  title: string;
  description?: string;
  source: VideoSource;
  muxPlaybackId?: string;
  youtubeId?: string;
  posterUrl?: string;
  aslAvatarUrl?: string;
  aslDefaultPosition?: "below" | "side";
  isPaid?: boolean;
  priceLabel?: string;
  stripePriceId?: string;
};

export const featured: IQVideo[] = [
  {
    id: "demo-mux-1",
    title: "InclusiveQuest Demo (Mux)",
    description: "Replace muxPlaybackId with a real Mux Playback ID to play.",
    source: "mux",
    muxPlaybackId: "YOUR_MUX_PLAYBACK_ID",
    posterUrl: "https://image.mux.com/YOUR_MUX_PLAYBACK_ID/thumbnail.png?time=1",
    aslAvatarUrl: "https://example.com/asl-avatar-demo.mp4",
    aslDefaultPosition: "below",
    isPaid: false
  },
  {
    id: "demo-yt-1",
    title: "InclusiveQuest Demo (YouTube)",
    description: "YouTube embed + ASL avatar panel shown beside/below.",
    source: "youtube",
    youtubeId: "dQw4w9WgXcQ",
    posterUrl: "https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg",
    aslAvatarUrl: "https://example.com/asl-avatar-demo.mp4",
    aslDefaultPosition: "side",
    isPaid: true,
    priceLabel: "$3.99 Rent / $12.99 Buy",
    stripePriceId: "price_YOUR_STRIPE_PRICE_ID"
  }
];

export const rails = [
  { title: "Featured", items: featured },
];
