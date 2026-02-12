import { ChannelGrid } from "@/components/ChannelGrid";

export default function Channels(){
  return (
    <div className="pt-6">
      <h1 className="text-2xl font-black">Channels</h1>
      <p className="mt-2 text-muted">Curated YouTube channel feed inside InclusiveQuest.</p>
      <div className="mt-6">
        <ChannelGrid />
      </div>
    </div>
  );
}
