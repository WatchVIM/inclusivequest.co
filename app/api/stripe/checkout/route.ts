import { NextResponse } from "next/server";
import Stripe from "stripe";

export const runtime = "nodejs";

export async function POST(req: Request){
  const { priceId } = await req.json();
  const key = process.env.STRIPE_SECRET_KEY;
  const site = process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000";

  if (!key) return NextResponse.json({ error: "Missing STRIPE_SECRET_KEY" }, { status: 200 });
  if (!priceId) return NextResponse.json({ error: "Missing priceId" }, { status: 200 });

  const stripe = new Stripe(key, { apiVersion: "2024-06-20" });

  const session = await stripe.checkout.sessions.create({
    mode: "payment",
    line_items: [{ price: priceId, quantity: 1 }],
    success_url: `${site}/?purchase=success`,
    cancel_url: `${site}/?purchase=cancel`
  });

  return NextResponse.json({ url: session.url });
}
