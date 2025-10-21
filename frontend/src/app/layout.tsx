import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Feature Flag Service",
  description: "A feature flag service with admin panel and client interface",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="antialiased">
        {children}
      </body>
    </html>
  );
}
