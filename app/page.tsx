import { rails } from "@/lib/content";
import { Rail } from "@/components/Rail";

export default function Home(){
  return (
    <div className="pt-6">
      <section className="rounded-[22px] border border-line bg-card p-6 shadow">
        <h1 className="text-3xl font-black tracking-tight">Hulu x YouTube — built for Deaf viewers</h1>
        <p className="mt-2 max-w-2xl text-muted">
          Watch Mux HLS titles and curated YouTube channels with an ASL avatar panel displayed below or side-by-side.
        </p>
      </section>

      <div className="mt-8 space-y-8">
        {rails.map((r) => (
          <Rail key={r.title} title={r.title} items={r.items} />
        ))}
      </div>
    </div>
  );
}
