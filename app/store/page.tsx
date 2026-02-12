import Link from "next/link";
import { featured } from "@/lib/content";

export default function Store(){
  const paid = featured.filter(v => v.isPaid);
  return (
    <div className="pt-6">
      <h1 className="text-2xl font-black">Store</h1>
      <p className="mt-2 text-muted">Purchase/rent titles (MVP uses Stripe Checkout linkouts).</p>

      <div className="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
        {paid.map((v) => (
          <Link key={v.id} href={`/watch/${v.id}`}
            className="rounded-[18px] border border-line bg-card p-4 hover:bg-white/5">
            <div className="text-sm font-extrabold">{v.title}</div>
            <div className="mt-1 text-xs text-muted">{v.priceLabel ?? ""}</div>
            <div className="mt-3 text-xs text-muted">Open watch page → Buy / Rent</div>
          </Link>
        ))}
        {paid.length === 0 ? (
          <div className="rounded-[18px] border border-line bg-card p-4 text-muted">
            No paid titles yet. Mark a title as <code className="text-white/80">isPaid</code> in <code className="text-white/80">lib/content.ts</code>.
          </div>
        ) : null}
      </div>
    </div>
  );
}
