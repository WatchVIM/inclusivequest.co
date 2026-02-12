# InclusiveQuest (GitHub-native deploy)

Starter repo (Next.js) for InclusiveQuest:
- Rails-style home page
- Channels feed (YouTube Data API v3)
- Watch page (Mux HLS + ASL avatar panel below/side)
- Store page (Stripe Checkout linkout MVP)

## Run locally
```bash
npm install
cp .env.example .env.local
npm run dev
```

## Deploy (Vercel recommended)
1) Push to GitHub
2) Import the repo in Vercel
3) Add env vars in Vercel Project Settings:
- YOUTUBE_API_KEY
- STRIPE_SECRET_KEY
- NEXT_PUBLIC_SITE_URL
