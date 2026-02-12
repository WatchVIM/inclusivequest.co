import "./globals.css";
import type { Metadata } from "next";
import { Nav } from "@/components/Nav";

export const metadata: Metadata = {
  title: "InclusiveQuest",
  description: "Hulu x YouTube experience built for Deaf viewers with ASL avatar panels.",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body>
        <Nav />
        <main className="mx-auto max-w-7xl px-4 pb-14">{children}</main>
      </body>
    </html>
  );
}
