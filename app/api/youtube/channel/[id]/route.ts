import { NextResponse } from "next/server";
export const runtime = "nodejs";

export async function GET(_: Request, { params }: { params: { id: string } }) {
  const apiKey = process.env.YOUTUBE_API_KEY;
  const channelId = params.id;
  if (!apiKey) return NextResponse.json({ items: [], error: "Missing YOUTUBE_API_KEY" }, { status: 200 });

  const url = new URL("https://www.googleapis.com/youtube/v3/search");
  url.searchParams.set("part", "snippet");
  url.searchParams.set("channelId", channelId);
  url.searchParams.set("maxResults", "12");
  url.searchParams.set("order", "date");
  url.searchParams.set("type", "video");
  url.searchParams.set("key", apiKey);

  const res = await fetch(url.toString(), { next: { revalidate: 900 } });
  if (!res.ok) return NextResponse.json({ items: [], error: "YouTube API error" }, { status: 200 });

  const data = await res.json();
  const items = (data.items ?? []).map((it: any) => ({
    videoId: it?.id?.videoId ?? "",
    title: it?.snippet?.title ?? "Untitled",
    thumb: it?.snippet?.thumbnails?.medium?.url ?? it?.snippet?.thumbnails?.default?.url ?? ""
  })).filter((x: any) => x.videoId);

  return NextResponse.json({ items });
}
